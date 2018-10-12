<?php

define('ROOT', __DIR__);


// simple PSR4 class autoloader

spl_autoload_register(function ($class) {

	$roots = [		
		'app' => 'App',
		'lib' => 'Core',
	];

	foreach($roots as $folder => $prefix) {
	
	    // does the class use the namespace prefix?
	    $len = strlen($prefix);
	    if (strncmp($prefix, $class, $len) !== 0) {
	    	continue;
	    }

	    // get the relative class name
	    $relative_class = substr($class, $len);

	    // replace the namespace prefix with the base directory, replace namespace
	    // separators with directory separators in the relative class name, append
	    // with .php
	    $file = ROOT . '/' . $folder . str_replace('\\', '/', $relative_class) . '.php';

	    if (file_exists($file)) {
	        require $file;
	        return;
	    }

	}

});
