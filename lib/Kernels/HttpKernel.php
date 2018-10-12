<?php

namespace Core\Kernels;

use Core\Component;

/**
 * This class processes HTTP requests
 */
class HttpKernel extends Component {

	// where to look for controllers and middlewares
	protected static $controllersNamespace = 'App\Controllers';
	protected static $middlewareNamespace = 'App\Middleware';

	protected $currentRoute;


	/**
	 * Perform the processing.
	 * 
	 * @return void
	 */
	public function run() {

		$this->boot();

		$this->currentRoute = $this->findRouteOrAbort();

		$this->process($this->currentRoute);
	}	

	/**
	 * Set defaults.
	 * 
	 * @return void
	 */
	public function boot() {
		$this->response->setViewFolder('app/resources/views');
		$this->router->loadRoutes('app/routes.php');		
	}

	/**
	 * Find a matching route or abort if nothing found.
	 * 
	 * @return Core\Route
	 */
	public function findRouteOrAbort() {
		$route = $this->router->find(
			$this->request->getMethod(), 
			$this->request->getPath()
		);

		if(!$route) {
			$this->response->abort();
		}

		return $route;
	}

	/**
	 * Process the route: middlewares, then the handler.
	 * 
	 * @param  Core\Route
	 * @return void
	 */
	public function process($route) {

		// if middlewares have intercepted the request, we're done
		if($this->processMiddlewares($route)) {
			return;
		}

		// create a controller and check if it has the specified action
		$controllerName = self::$controllersNamespace . '\\' . $route->getController();
		$actionName = $route->getAction();
		$parameters = $route->getParameters();

		$controller = new $controllerName($this->app);

		if(!$controller) {
			throw new \Exception("Controller $controllerName was not found in '" . 
				self::$controllersNamespace . "'");
		}

		if(!method_exists($controller, $actionName)) {
			throw new \Exception("Controller $controllerName has no action '" . 
				$actionName . "'");
		}

		// initialize controller
		// this is NOT done in controller __construct method to avoid passing
		// $app instance
		$controller->initialize();

		// actually call the controller's action
		call_user_func_array([$controller, $actionName], $parameters);
	}

	/**
	 * Process route middlewares
	 * 
	 * @param  Core\Route
	 * @return Boolean
	 */
	public function processMiddlewares($route) {

		$middlewares = $route->getMiddlewares();

		if(!is_array($middlewares)) {
			return false;
		}

		foreach($middlewares as $middleware) {

			// create a middleware instance
			$middlewareName = self::$middlewareNamespace . '\\' . ucwords($middleware);
			$middlewareInstance = new $middlewareName($this->app);

			// if middleware has return TRUE - stop processing
			if($middlewareInstance->process($this->request)) {
				return true;
			}
		}

		// middlewares have not intercepted the request
		return false;
	}

}
