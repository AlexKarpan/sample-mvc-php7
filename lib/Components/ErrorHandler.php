<?php

namespace Core\Components;

use Core\Component;

/**
 * Class to handle errors and exceptions.
 */
class ErrorHandler extends Component {

	/**
	 * Set up error handlers.
	 * Turn on or off the error reporting for debug/production modes.
	 */
	public function setHandling() {

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);

		if($this->config->get('app.debug', false)) {
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 0);
			error_reporting(0);
		}
	}

	/**
	 * Log the exception and display 500 Server Error page.
	 * 
	 * @param  string
	 * @return void
	 */
	public function handleException($exception) {

		if($exception instanceof \Exception) {
			$this->logger->logException($exception);
        } else {
        	$this->logger->log(print_r($exception, true));
        }

        $this->response->abort(500);
	}

	/**
	 * Turn error into an exception, since we log exceptions.
	 */
	public function handleError($level, $message, $file = '', $line = 0, $context = []) {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

}
