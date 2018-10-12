<?php

namespace Core\Components;

use Core\Component;

/**
 * Class to provide handy methods to send out responses.
 */
class Response extends Component {

	protected $viewFolder;


	/**
	 * Set the views folder location.
	 * 
	 * @param string
	 */
	public function setViewFolder($folder) {
		$this->viewFolder = ROOT . '/' . $folder;
	}

	/**
	 * Render a view with parameters.
	 * Return self for method chaining.
	 * 
	 * @param  string
	 * @param  array
	 * @return \Core\Components\Response
	 */
	public function render($_filename_, $_data_ = []) {
		extract($_data_);
		require($this->viewFolder . '/' . $_filename_ . '.php');
		return $this;
	}

	/**
	 * Render a JSON response.
	 * Return self for method chaining.
	 * 
	 * @param  mixed
	 * @return \Core\Components\Response
	 */
	public function json($data) {
		header('Content-type: application/json');
		echo json_encode($data);
		return $this;
	}	

	/**
	 * Render a redirect response.
	 * Return self for method chaining.
	 * 
	 * @param  string
	 * @return \Core\Components\Response
	 */
	public function redirect($path) {
		header("Location: " . $path, true, 303);
		return $this;
	}

	/**
	 * Render a redirect response to the current location.
	 * This is useful to return to the form with errors.
	 * Return self for method chaining.
	 * 
	 * @return \Core\Components\Response
	 */
	public function redirectBack() {
		return $this->redirect('/' . $this->request->getPath());
	}

	/**
	 * Flash a session information.
	 * Return self for method chaining.
	 * 
	 * @param  string
	 * @param  string
	 * @return \Core\Components\Response
	 */
	public function with($tag, $message) {
		$this->session->put($tag, $message);
		return $this;
	}

	/**
	 * Flash an error into the session.
	 * 
	 * @param  string
	 * @return \Core\Components\Response
	 */
	public function withError($message) {
		return $this->with('error', $message);
	}

	/**
	 * Flash a success message into the session.
	 * 
	 * @param  string
	 * @return \Core\Components\Response
	 */
	public function withSuccess($message) {
		return $this->with('success', $message);
	}
	
	/**
	 * Render a 404 or 500 error page.
	 * 
	 * @param  integer
	 * @return \Core\Components\Response
	 */
	public function abort($code = 404) {
		if($code == 404) {
			header(
				($_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.0') . " 404 Not Found", 
				true, 
				404
			);
		} else {
			header(
				($_SERVER["SERVER_PROTOCOL"] ?? 'HTTP/1.0') . " 500 Server Error", 
				true, 
				500
			);
		}
		return $this;
	}

}