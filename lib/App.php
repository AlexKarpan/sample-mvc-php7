<?php

namespace Core;

/**
 *  The main app class
 *
 *  It creates components, which become available as $this->{name} everywhere
 *
 *  The components are divided in two packs: 'base' and 'http'.
 *  This is done to make adding 'console' or 'test' kernels simpler.
 */
class App {

	/**
	 * @var array
	 */
	protected static $components = [
		'base' => [
			'config' => \Core\Components\Config::class,
			'logger' => \Core\Components\Logger::class,
			'errors' => \Core\Components\ErrorHandler::class,
			'db' => \Core\Components\DB::class,
		],

		'http' => [
			'session' => \Core\Components\Session::class,
			'request' => \Core\Components\Request::class,
			'response' => \Core\Components\Response::class,
			'router' => \Core\Components\Router::class,
			'kernel' => \Core\Kernels\HttpKernel::class,
			'auth' => \Core\Components\Auth::class,
		],
	];

	/**
	 * Create the base components
	 */
	public function __construct() {
		$this->registerComponents('base');

		$this->config->loadConfiguration('config.php');
		$this->logger->setOutputFilename('tmp/app.log');
		$this->errors->setHandling();
		$this->db->connect();
	}

	/**
	 * Create a component pack
	 * 
	 * @param $pack
	 */
	public function registerComponents($pack) {
		foreach (self::$components[$pack] as $alias => $class) {
			$this->load($alias, $class);
		}
	}

	/**
	 * Load a single component.
	 * Used to load a required dependency e.g. a DAL object.
	 * 
	 * @param $alias
	 * @param $class
	 */
	public function load($alias, $class) {
		$this->{$alias} = new $class($this);
	}

	/**
	 * Create the HTTP components and process the HTTP request
	 * 
	 * @return void
	 */
	public function processHttpRequest() {
		$this->registerComponents('http');

		$this->session->start();
		$this->request->initializeFromGlobals();
		$this->kernel->run();
	}

}