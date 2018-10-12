<?php

namespace Core;

/**
 * Utility class to store information about a route.
 */
class Route {

	protected $method;
	protected $pattern;
	protected $controller;
	protected $action;

	// Route middlewares 
	protected $middlewares = [];

	// Route parameters
	protected $parameters = [];

	/**
	 * Create a route and canonize the method and pattern
	 * Methods becomes uppercase: GET, POST, ANY
	 * Pattern looses slashes.
	 * 
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	public function __construct($method, $pattern, $controller, $action) {
		$this->method = strtoupper($method);
		$this->pattern = trim($pattern, '/');
		$this->controller = $controller;
		$this->action = $action;
	}

	/**
	 * Add one or several middlewares.
	 * Return self to allow method chaining.
	 * 
	 * @param  string|array
	 * @return Core\Route
	 */
	public function middleware($value) {
		if(is_array($value)) {
			$this->middlewares += $value;
		} else {
			$this->middlewares[] = $value;
		}

		return $this;
	}

	/**
	 * Check if the current route matches the provided method/path.
	 * 
	 * @param  string
	 * @param  string
	 * @return Boolean
	 */
	public function matches($method, $path) {

		if($this->method != $method && $this->method != 'ANY') {
			return false;
		}

		// if they are both empty, it matches
		if($this->pattern == "") {
			return ($path == "");
		}

		// incomplete matches are not allowed
		if(preg_match('#^' . $this->pattern . '$#i', $path, $this->parameters)) {
			array_shift($this->parameters);
			return true;
		}

		return false;
	}


	// Getters

	public function getController() {
		return $this->controller;
	}

	public function getAction() {
		return $this->action;
	}

	public function getParameters() {
		return $this->parameters;
	}

	public function getMiddlewares() {
		return $this->middlewares;
	}

	/**
	 * Any unknown function is turned into a middleware, e.g.:
	 * ->adminOnly() becomes 'adminOnly' middleware
	 * 
	 * @param  string
	 * @param  array - ignored
	 * @return Core\Route
	 */
	public function __call($name, $arguments) {
		return $this->middleware($name);
	}
}
