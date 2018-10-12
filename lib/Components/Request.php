<?php

namespace Core\Components;

use Core\Component;

/**
 * Class to abstract the request.
 */
class Request extends Component {

	protected $post;
	protected $get;
	protected $files;
	protected $method;
	protected $path;

	/**
	 * Load request information from superglobals.
	 * Alternatively, the request can be mocked with another contructor,
	 * for example for testing.
	 * 
	 * @return void
	 */
	public function initializeFromGlobals() {
		$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		$this->get = $_GET;
		$this->post = $_POST;
		$this->files = $_FILES;
		$this->path = trim($_SERVER['REQUEST_URI'], '/');

		// for JSON POST request from axios, we do need some special processing
		if(stripos($_SERVER["CONTENT_TYPE"], 'application/json') !== false) {
			$this->post += (array) json_decode(file_get_contents('php://input'), true);
		}
	}

	/**
	 * Get an input value - from GET or POST.
	 * 
	 * @param  string
	 * @param  mixed
	 * @return mixed
	 */
	public function input($key, $default = null) {
		return $this->get[$key] ?? ($this->post[$key] ?? $default);
	}

	/**
	 * Get the first file from the request.
	 * In this app we only need the first file.
	 * 
	 * @param  string
	 * @return \FileData
	 */
	public function file($name) {
		$file = $this->files[$name] ?? null;
		if($file['tmp_name'] ?? false) {
			return $file;
		}
	}


	// Getters

	public function getMethod() {
		return $this->method;
	}

	public function getPath() {
		return $this->path;
	}
	
}