<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Controller to create a problem action
 */
class CreateProblemController extends Controller {

	protected static $allowedMimeTypes = [
		'image/jpeg', 
		'image/png', 
		'image/gif',
	];

	protected $name;
	protected $email;
	protected $text;
	protected $image;
	protected $imageData;

	protected $errors = [];


	/**
	 * Preload the data provider.
	 * 
	 * @return void
	 */
	public function initialize() {
		$this->app->load('problemsProvider', \App\DAL\ProblemProvider::class);
	}

	/**
	 * Render a compose form.
	 * Use the old entered values in case we return back with an error.
	 * 
	 * @return void
	 */
	public function showCreateForm() {
		$name = $this->session->pullOldInput('name', '');
		$email = $this->session->pullOldInput('email', '');
		$text = $this->session->pullOldInput('text', '');

		$error = $this->session->pull('error');

		$this->response->render('problems/create', compact('name', 'email', 'text', 'error'));
	}	
	
	/**
	 * Ugly function to handle the validation.
	 * Should be refactored into a set of validation classes.
	 * Return TRUE if validation passed.
	 * 
	 * @return boolean
	 */
	protected function validateInput() {

		// validate name

		$this->name = trim($this->request->input('name', ''));
		if(empty($this->name)) {
			$this->errors[] = 'Your name is required.';
		} else if(strlen($this->name) >= 191) {
			$this->errors[] = 'Your name is too long.';			
		}


		// validate email

		$this->email = trim($this->request->input('email', ''));
		if(empty($this->email)) {
			$this->errors[] = 'Your email is required.';
		} else {
			if(strlen($this->email) >= 191) {
				$this->errors[] = 'Your email is too long.';			
			}

			if(filter_var($this->email, FILTER_VALIDATE_EMAIL) == false) {
				$this->errors[] = 'Not a valid email address.';				
			}
		}


		// validate problem text

		$this->text = $this->request->input('text', '');
		if(empty($this->text)) {
			$this->errors[] = 'Problem text is required.';
		} else if(strlen($this->text) >= 15000) {
			$this->errors[] = 'Problem text is too long.';			
		}


		// if image is present, we check if we can process it

		$this->image = $this->request->file('image');
		if($this->image) {
			$this->imageData = getimagesize($this->image['tmp_name']);

			if(!in_array(
					$this->imageData['mime'] ?? '', 
					self::$allowedMimeTypes)) {

				$this->errors[] = 'Invalid or unrecognized image file format';
			}
		}

		// return true if no errors
		return empty($this->errors);
	}

	/**
	 * Scale the provided image if needed.
	 * Recode it to a JPEG file.
	 * We do NOT use the provided file as it is unsafe.
	 * 
	 * @param  integer
	 * @return void
	 */
	protected function processAndMoveUploadedImage($id) {

	    list($width, $height) = $this->imageData;

	    $max_width = $this->config->get('problems.image_max_width', 320);
	    $max_height = $this->config->get('problems.image_max_height', 240);


	    // resize if needed

	    if($width > $max_width || $height > $max_height) {

	    	$aspectRatio = $width / $height;

	    	if ($max_width / $max_height > $aspectRatio) {
	            $newWidth = $max_height * $aspectRatio;
	            $newHeight = $max_height;
	        } else {
	            $newWidth = $max_width;
	            $newHeight = $max_width / $aspectRatio;
	        }
	    } else {

	    	$newWidth = $max_width;
	    	$newHeight = $max_height;
	    }


	    // convert to jpg and store

	    switch($this->imageData['mime']) {
	    	case 'image/jpeg': $src = imagecreatefromjpeg($this->image['tmp_name']); break;
	    	case 'image/png': $src = imagecreatefrompng($this->image['tmp_name']); break;
	    	case 'image/gif': $src = imagecreatefromgif($this->image['tmp_name']); break;
	    }
	    $dst = imagecreatetruecolor($newWidth, $newHeight);
	    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

	    imagejpeg($dst, $this->problemsProvider->getImageLocalPath($id));
	}
	
	/**
	 * Create a new problem record.
	 * 
	 * @return void
	 */
	public function create() {

		// if validation is not passed, return with errors

		if(!$this->validateInput()) {

			$this->session->flashOldInput([
				'name' => $this->request->input('name'),
				'email' => $this->request->input('email'),
				'text' => $this->request->input('text'),
			]);

			$this->response->redirectBack()
				->withError(implode('<br>', $this->errors));
			return;
		}


		// store the record and get LastInsertId

		$id = $this->problemsProvider->create([
			'name' => $this->name,
			'email' => $this->email,
			'text' => $this->text,
		]);


		// save the image as tmp/upload/{id}.jpg
		// the folder is symlinked to public/upload/
		// the actual local paths are in the config file
		// "convention over configuration" approach!

		if($this->image) {
			$this->processAndMoveUploadedImage($id);
		}

		// redirect back to the list of problems
		$this->response->redirect('/');
	}

}