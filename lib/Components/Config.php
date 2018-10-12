<?php

namespace Core\Components;

use Core\Component;

/**
 * System configuration
 */
class Config extends Component {

	protected $data = [];


	/**
	 * Load the configuration values from a file.
	 * 
	 * @param  string
	 * @return void
	 */
	public function loadConfiguration($filename) {
		$this->data = require(ROOT . '/' . $filename);
	}

	/**
	 * Get a configuration value.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function get($value, $default = NULL) {
		return $this->data[$value] ?? $default;
	}

	/**
	 * Check if a configuration value is set.
	 * 
	 * @param  string
	 * @return boolean
	 */
	public function has($value) {
		return isset($this->data[$value]);
	}

}