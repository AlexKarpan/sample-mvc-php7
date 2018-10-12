<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Controller to render an error page.
 */
class ErrorController extends Controller {
	
	public function show404() {
		$this->response->render('errors/404');
	}
	
}