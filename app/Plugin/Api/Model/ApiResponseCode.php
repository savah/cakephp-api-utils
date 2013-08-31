<?php
App::uses('AppModel', 'Model');

/**
 * Api Response Code Model
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @since       1.0
 * @package     Api
 * @subpackage  Api.Model
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiResponseCode extends AppModel {
	
	/**
	 * Table
	 *
	 * @since   1.0
	 * @var     boolean
	 */
	public $useTable = false;
	
	/**
	 * Records
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $records = array(
		array(
			'id' => 2000,
			'httpCode' => 200,
			'message' => ''
		),
		array(
			'id' => 4000,
			'httpCode' => 403,
			'message' => 'Must pass ID'
		),
		array(
			'id' => 4001,
			'httpCode' => 403,
			'message' => 'Cannot pass ID'
		),
		array(
			'id' => 4002,
			'httpCode' => 403,
			'message' => 'ID mismatch'
		),
		array(
			'id' => 4003,
			'httpCode' => 403,
			'message' => 'ID does not belong'
		),
		array(
			'id' => 4004,
			'httpCode' => 404,
			'message' => 'No data was found'
		),
		array(
			'id' => 4005,
			'httpCode' => 403,
			'message' => 'No acceptable data fields'
		),
		array(
			'id' => 4006,
			'httpCode' => 403,
			'message' => 'Missing required data'
		),
		array(
			'id' => 4007,
			'httpCode' => 403,
			'message' => 'Data is not related'
		),
		array(
			'id' => 4008,
			'httpCode' => 403,
			'message' => 'Data should not be indexed'
		),
		array(
			'id' => 4009,
			'httpCode' => 403,
			'message' => 'Data should be indexed'
		),
		array(
			'id' => 4012,
			'httpCode' => 403,
			'message' => 'Validation errors'
		),
		array(
			'id' => 4013,
			'httpCode' => 403,
			'message' => 'Unauthorized'
		),
		/**
		 * Used for:
		 * - Batch updates
		 * - Saves with related saveable=false
		 * - Finds with related findable=false 
		 */
		array(
			'id' => 4015,
			'httpCode' => 403,
			'message' => 'Related data not allowed'
		),
		array(
			'id' => 4016,
			'httpCode' => 403,
			'message' => 'Must pass search term'
		),
		array(
			'id' => 4017,
			'httpCode' => 403,
			'message' => 'Conditions are required for batch operations'
		),
		array(
			'id' => 4018,
			'httpCode' => 410,
			'message' => 'Resource deleted'
		),
		array(
			'id' => 5000,
			'httpCode' => 500,
			'message' => 'System error'
		),
		array(
			'id' => 5001,
			'httpCode' => 500,
			'message' => 'System error saving data'
		),
		array(
			'id' => 5002,
			'httpCode' => 500,
			'message' => 'System error deleting data'
		),
	);
	
	/**
	 * Find By ID
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @param   integer $id Id to find. 
	 * @return  void
	 */
	public function findById($id = 0) {
		
		$result = Hash::extract($this->records, "{n}[id=$id]");
		
		return empty($result) ? false : array($this->alias => array_shift($result));
		
	}

}
