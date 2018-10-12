<?php

namespace Core\Components;

use Core\Component;

/**
 * Database access class
 */
class DB extends Component {

	protected $connection;

	/**
	 * Connect to the database.
	 * 
	 * @return void
	 */
	public function connect() {

		if (!$this->config->has('db.host') ||
		    !$this->config->has('db.user') ||
		 	!$this->config->has('db.pass') ||
			!$this->config->has('db.name')) {

			throw new \Exception('Database settings are missing or incomplete!');
		}

		$this->connection = new \PDO(
			"mysql:dbname=" . $this->config->get('db.name') . 
				";host=" . $this->config->get('db.host'), 
			$this->config->get('db.user'), 
			$this->config->get('db.pass')
		);
	}

	/**
	 * Magic method to route all calls to $this->db to the PDO instance.
	 * 
	 * @param  string
	 * @param  array
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		if(method_exists($this->connection, $name)) {
			return call_user_func_array([$this->connection, $name], $arguments);
		}

		throw new \Exception("Call to undefined method: DB::", $name);
	}
	
}