<?php

// entry point

// fire up PSR4 autoloader
require(__DIR__ . '/../bootstrap.php');

// create a new app instance
$app = new Core\App;

// use this app to process the current HTTP request
$app->processHttpRequest();
