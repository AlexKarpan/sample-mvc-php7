<?php

namespace App\DAL;

use Core\Component;

/**
 * Data provider class to handle all CRUD operations with 'problems'.
 */
class ProblemProvider extends Component {

	public static $table = 'problems';

	/**
	 * Get the total number of records.
	 * 
	 * @return integer
	 */
	public function count() {
		$result = $this->db->query('SELECT count(*) FROM ' . self::$table);
		return (int) $result->fetchColumn(0);
	}

	/**
	 * Retrieve the records.
	 * If offset and limit are both zero, get all records.
	 * If sorting is specified, use it.
	 * 
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return array
	 */
	public function load($offset, $limit, $sorting) {

		$offset = (int) $offset;
		$limit = (int) $limit;

		$sql = 'SELECT * FROM ' . self::$table;

		// bad practice NOT to use param binding
		// but it's safe here
		if($sorting) {
			$sql .= " ORDER BY $sorting";
		}

		if($offset > 0 || $limit > 0) {
			$sql .= " LIMIT $offset,$limit";
		}

		$query = $this->db->prepare($sql);
		$query->execute();

		$items = $query->fetchAll(\PDO::FETCH_OBJ);

		// for each record we check if the image file exists
		// since we're getting 10 records at once by most, this is ok
		// otherwise it should be reworked into a database column
		// with an image name or presence flag
		foreach($items as $item) {
			$imageFilename = $this->getImageLocalPath($item->id);
			if(file_exists($imageFilename)) {
				$item->image = $this->getImageUrl($item->id);
			}
		}
	
		return $items;		
	}

	/**
	 * Insert a record and return its ID.
	 * 
	 * @param  array
	 * @return integer
	 */
	public function create($attributes) {

		$query = $this->db->prepare(
			'INSERT INTO ' . self::$table . 
			' SET name=:name, email=:email, text=:text');

		$query->bindParam(':name', $attributes['name'], \PDO::PARAM_STR);
		$query->bindParam(':email', $attributes['email'], \PDO::PARAM_STR);
		$query->bindParam(':text', $attributes['text'], \PDO::PARAM_STR);
		$query->execute();

		return $this->db->lastInsertId();
	}

	/**
	 * Toggle status (Solved / Not solved) for a record by ID.
	 * 
	 * @param  integer
	 * @return void
	 */
	public function toggleStatus($id) {

		$id = (int) $id;

		$query = $this->db->prepare(
			'UPDATE ' . self::$table . 
			' SET is_completed = 1 - is_completed ' .
			' WHERE id=:id');
		$query->bindParam(':id', $id, \PDO::PARAM_INT);
		$query->execute();
	}

	/**
	 * Update the problem text by record ID.
	 * It is safe to store it since it's passed the validation.
	 * 
	 * @param  integer
	 * @param  string
	 * @return void
	 */
	public function updateText($id, $text) {

		$id = (int) $id;

		$query = $this->db->prepare(
			'UPDATE ' . self::$table . 
			' SET text=:text ' .
			' WHERE id=:id');
		$query->bindParam(':id', $id, \PDO::PARAM_INT);
		$query->bindParam(':text', $text, \PDO::PARAM_STR);
		$query->execute();
	}	

	/**
	 * Utility function to get the relative local path to an image file for a record.
	 * 
	 * @param  integer
	 * @return string
	 */
	public function getImageLocalPath($id) {
		return ROOT . '/'. $this->config->get('problems.upload_folder') . '/'. $id . '.jpg';
	}

	/**
	 * Utitlity function to get the relative URL for an image for a record.
	 * 
	 * @param  integer
	 * @return string
	 */
	public function getImageUrl($id) {
		return $this->config->get('problems.upload_url') . '/'. $id . '.jpg';
	}
	
}