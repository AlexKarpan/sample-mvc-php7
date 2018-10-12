<?php

namespace Core\Components;

use Core\Component;

/**
 * Class to handle all session-related tasks.
 * Too tightly coupled to $_SESSION - should be refactored.
 */
class Session extends Component {

	protected static $oldInputPrefix = 'old.';

	protected $values = [];

	/**
	 * Start the session and load its values.
	 * 
	 * @return void
	 */
	public function start() {
		session_start();
		$this->values = $_SESSION;
	}

	/**
	 * Get a session value.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function get($key, $default = null) {
		return $this->values[$key] ?? $default;
	}

	/**
	 * Store a value in the session.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return void
	 */
	public function put($key, $value) {
		$this->values[$key] = $value;
		$_SESSION[$key] = $value;
	}

	/**
	 * Remove a value from the session.
	 * 
	 * @param  string
	 * @return void
	 */
	public function unset($key) {
		if(isset($this->values[$key])) {
			unset($this->values[$key]);
		}

		if(isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Retrieve a value and remove it from the session.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function pull($key, $default = null) {
		$value = $this->get($key, $default);
		$this->unset($key);
		return $value;
	}

	/**
	 * Save old input values into the session.
	 * 
	 * @param  array
	 * @return void
	 */
	public function flashOldInput($values) {
		foreach($values as $key => $value) {
			$this->put(self::$oldInputPrefix . $key, $value);
		}
	}

	/**
	 * Pull an old input value from the session.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function pullOldInput($key, $default = null) {
		return $this->pull(self::$oldInputPrefix . $key, $default);
	}

}