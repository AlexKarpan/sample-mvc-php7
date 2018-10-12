<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Controller to manage problems by an admin
 */
class AdminManageProblemsController extends Controller {
	
	/**
	 * Preload the data provider
	 * 
	 * @return void
	 */
	public function initialize() {
		$this->app->load('problemsProvider', \App\DAL\ProblemProvider::class);
	}

	/**
	 * Render the admin page to show problems.
	 * 
	 * @return void
	 */
	public function showProblems() {
		$this->response->render('admin/table');
	}

	/**
	 * Toggle the status of a problem: Solved/Not solved
	 * 
	 * @return void
	 */
	public function toggleStatus() {
		$id = $this->request->input('id');

		$this->problemsProvider->toggleStatus($id);
	}

	/**
	 * Update the text of a problem.
	 * 
	 * @return void
	 */
	public function updateText() {
		$id = $this->request->input('id');
		$text = $this->request->input('text');

		$this->problemsProvider->updateText($id, $text);
	}	
	
}