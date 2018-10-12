<?php

namespace App\Middleware;

use Core\Middleware;

/**
 * Middleware to check if the current user has admin rights.
 */
class AdminOnly extends Middleware {
	
	protected static $redirectPath = '/login';

	public function process() {
		if(!$this->auth->isAdmin()) {

			// if not an admin, save the current URL and take them to the log in page
			$this->auth->saveIntendedUrl($this->request->getPath());
			$this->response->redirect(self::$redirectPath);
			return true;
		}

		return false;
	}
	
}
