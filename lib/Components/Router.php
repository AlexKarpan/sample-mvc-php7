<?php

namespace Core\Components;

use Core\Component;
use Core\Route;


/**
 * Class to manage routes.
 */
class Router extends Component {

	protected $routes = [];

	// route to follow when nothing matches.
	protected $notFound = null;

	/**
	 * Loads the app routes from a file.
	 * 
	 * @param  string
	 * @return void
	 */
	public function loadRoutes($filename) {
		require(ROOT . '/' . $filename);
	}

	/**
	 * Create a route and return it.
	 * 
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return \Core\Route
	 */
	public function addRoute($method, $pattern, $controller, $action) {
		$route = new Route($method, $pattern, $controller, $action);
		$this->routes[] = $route;
		return $route;
	}

	/**
	 * Create a route to match all methods (ANY).
	 * 
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return \Core\Route
	 */
	public function any($pattern, $controller, $action) {
		return $this->addRoute('any', $pattern, $controller, $action);
	}

	/**
	 * Create a GET route.
	 * 
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return \Core\Route
	 */
	public function get($pattern, $controller, $action) {
		return $this->addRoute('get', $pattern, $controller, $action);
	}

	/**
	 * Create a POST route.
	 * 
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return \Core\Route
	 */
	public function post($pattern, $controller, $action) {
		return $this->addRoute('post', $pattern, $controller, $action);
	}

	/**
	 * Register a route to use when nothing matches.
	 * 
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return \Core\Route
	 */
	public function otherwise($controller, $action) {
		$this->notFound = new Route('ANY', '*', $controller, $action);
		return $this->notFound;
	}

	/**
	 * Go through routes and find a match for the provided method and path.
	 * 
	 * @param  string
	 * @param  string
	 * @return \Core\Route
	 */
	public function find($method, $path) {
		foreach($this->routes as $route) {
			if($route->matches($method, $path)) {
				return $route;
			}
		}

		// return this special route when nothing was found
		return $this->notFound;
	}
	
}
