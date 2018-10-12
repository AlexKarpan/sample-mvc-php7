<?php

namespace App\Middleware;

use Core\Middleware;

/**
 * Middleware to check if the current user is a guest
 */
class GuestOnly extends Middleware {
	
	protected static $redirectPath = '/admin';

	public function process() {
		if(!$this->auth->isGuest()) {

			// if not a guest, take them to the admin dashboard
			$this->response->redirect(self::$redirectPath);
			return true;
		}

		return false;
	}
	
}
