<?php

namespace Core\Components;

use Core\Component;

/**
 * This class handles all authorization logic.
 */
class Auth extends Component {

	// session key to store auth information
	protected static $sessionKey = 'auth';

	/**
	 * Check if credentials are valid.
	 * We don't use password hashing in this sample app.
	 * 
	 * @param  string
	 * @param  string
	 * @return Boolean
	 */
	public function check($username, $password) {
		return ($username == $this->config->get('admin.username')) &&
			($password == $this->config->get('admin.password'));
	}

	/**
	 * Check if the current user is a guest.
	 * 
	 * @return boolean
	 */
	public function isGuest() {
		return !$this->isAdmin();
	}

	/**
	 * Check if the current user is an admin, using the session.
	 * 
	 * @return boolean
	 */
	public function isAdmin() {
		$storedUsername = $this->session->get(self::$sessionKey . '.username', '');
		$storedPassword = $this->session->get(self::$sessionKey . '.password', '');

		return ($this->check($storedUsername, $storedPassword));
	}

	/**
	 * Try to log in with credentials.
	 * 
	 * @param  string
	 * @param  string
	 * @return boolean
	 */
	public function login($username, $password) {
		if(!$this->check($username, $password)) {
			return false;
		}

		$this->session->put(self::$sessionKey . '.username', $username);
		$this->session->put(self::$sessionKey . '.password', $password);

		return true;
	}

	/**
	 * Log out
	 * 
	 * @return void
	 */
	public function logout() {
		$this->session->put(self::$sessionKey . '.username', '');
		$this->session->put(self::$sessionKey . '.password', '');		
	}

	/**
	 * Save the current request path to return after the login.
	 * 
	 * @param  string
	 * @return void
	 */
	public function saveIntendedUrl($url) {
		$this->session->put(self::$sessionKey . '.intended', $url);
	}

	/**
	 * Get the saved request path.
	 * 
	 * @return string
	 */
	public function getIntendedUrl() {
		return $this->session->get(self::$sessionKey . '.intended');
	}
	
}