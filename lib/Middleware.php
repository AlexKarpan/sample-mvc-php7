<?php

namespace Core;

/**
 * Base class for all midlleware.
 */
abstract class Middleware extends Component {

	/**
	 * Middlewares are executed in order. Each may return TRUE to indicate
	 * that the request processing must be cancelled.
	 * 
	 * @return Boolean
	 */
	public function process() {
		return false;
	}
}
