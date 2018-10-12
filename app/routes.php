<?php

// View the problems
$this->get('/', 'ShowProblemsController', 'showHomepage');
$this->get('problem/data/([0-9]+)/([0-9]+)/(u|na|nd|ea|ed|s0|s1)', 
	'ShowProblemsController', 'getData');


// Create a new record
$this->get('problem/create', 'CreateProblemController', 'showCreateForm');
$this->post('problem/create', 'CreateProblemController', 'create');


// Log in and log out
$this->get('login', 'AuthController', 'showLoginForm')->guestOnly();
$this->post('login', 'AuthController', 'login')->guestOnly();
$this->any('logout', 'AuthController', 'logout')->adminOnly();


// Manage problems as an admin
$this->get('admin', 'AdminManageProblemsController', 'showProblems')->adminOnly();
$this->post('admin/problem/toggle-status',
	'AdminManageProblemsController', 'toggleStatus')->adminOnly();
$this->post('admin/problem/update-text',
	'AdminManageProblemsController', 'updateText')->adminOnly();


// Otherwise show a 404 page
$this->otherwise('ErrorController', 'show404');
