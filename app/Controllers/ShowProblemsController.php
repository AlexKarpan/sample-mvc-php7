<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Controller to show the list of problems.
 */
class ShowProblemsController extends Controller {

	protected static $sortingModes = [
		'na' => 'name',
		'nd' => 'name DESC',
		'ea' => 'email',
		'ed' => 'email DESC',
		's0' => 'is_completed',
		's1' => 'is_completed DESC',
	];

	/**
	 * Preload the data provider.
	 * 
	 * @return void
	 */
	public function initialize() {
		$this->app->load('problemsProvider', \App\DAL\ProblemProvider::class);
	}
	
	/**
	 * Render the page with problems.
	 * Data is requested via AJAX.
	 * 
	 * @return void
	 */
	public function showHomepage() {
		$problemsPerPage = $this->config->get('problems.per_page', 3);

		$this->response->render('problems/index', compact('problemsPerPage'));
	}

	/**
	 * Query the data and render it as JSON.
	 * The validation is trivial here, so it's safe to pass the values
	 * to the data provider.
	 * 
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return void
	 */
	public function getData($offset, $limit, $sort) {

		$count = $this->problemsProvider->count();

		$sorting = self::$sortingModes[$sort] ?? '';
		$items = $this->problemsProvider->load($offset, $limit, $sorting);

		$this->response->json(compact('count', 'items'));
	}

}