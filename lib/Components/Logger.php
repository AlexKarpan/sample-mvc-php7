<?php

namespace Core\Components;

use Core\Component;

/**
 * Class to log information and exceptions.
 */
class Logger extends Component {

	protected $filename;

	/**
	 * Set the log filename.
	 * Check if it's writable.
	 * 
	 * @param string
	 */
	public function setOutputFilename($filename) {
		$this->filename = ROOT . '/' . $filename;

		if(!$this->canWriteToFile($this->filename)) {
			throw new \Exception("Log file " . $this->filename . " is not writable!");
		}
	}

	/**
	 * Check if the filename is writeable.
	 * 
	 * @param  string
	 * @return boolean
	 */
	public function canWriteToFile($filename) {
		if(!file_exists($filename)) {
			return touch($filename);
		}

		return is_writable($filename);
	}

	/**
	 * Log a message to the log file.
	 * 
	 * @param  string
	 * @return void
	 */
	public function log($message) {
		file_put_contents($this->filename, $message . PHP_EOL . PHP_EOL, FILE_APPEND);
	}

	/**
	 * Log an exception to the log file.
	 * 
	 * @param  \Exception
	 * @return void
	 */
	public function logException(\Exception $e) {
		$this->log(
			$e->getMessage() . ' in ' . $e->getFile() . ' at line ' . $e->getLine() . PHP_EOL . 
			$e->getTraceAsString()
		);
	}

}