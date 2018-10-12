<?php

namespace Core;

/**
 * Base class for all components.
 */
abstract class Component {

	/**
	 * @var Core\App
	 */
	protected $app;

	/**
	 * App is injected.
	 * 
	 * @param Core\App $app
	 */
	public function __construct(App $app) {
		$this->app = $app;
	}

	/**
	 * Magic method to make $this->{component} work everywhere.
	 * 
	 * @param $value
	 * @return mixed
	 */
	public function __get($value) {
		if (property_exists($this->app, $value)) {
			return $this->app->{$value};
		}

		throw new \Exception('Undefined property ' . $value);
	}

}