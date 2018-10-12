<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Controller to perform log in and log out actions
 */
class AuthController extends Controller {

	protected static $redirectAfterLogin = '/admin';
	protected static $redirectAfterLogout = '/';
	
	/**
	 * Render the log in form.
	 * 
	 * @return void
	 */
	public function showLoginForm() {

		// we keep the entered username in case the password was incorrect
		$username = $this->session->pullOldInput('username', '');

		// get error message if any
		$error = $this->session->pull('error');

		$this->response->render('auth/login', compact('username', 'error'));
	}
	
	/**
	 * Try to log in with the provided credentials.
	 * If failed, redirect back to form and keep the username.
	 * If successful, redirect to a saved URL or to the admin dashboard.
	 * 
	 * @return void
	 */
	public function login() {
		$username = $this->request->input('username', '');
		$password = $this->request->input('password', '');

		if(!$this->auth->login($username, $password)) {
			$this->session->flashOldInput(compact('username'));

			$this->response->redirectBack()
				->withError('Invalid credentials!');

			return;		
		}

		$this->response->redirect(
				$this->auth->getIntendedUrl() ?: self::$redirectAfterLogin);
	}

	/**
	 * Log out and return to the homepage.
	 * 
	 * @return void
	 */
	public function logout() {
		$this->auth->logout();
		$this->response->redirect(self::$redirectAfterLogout);
	}

}