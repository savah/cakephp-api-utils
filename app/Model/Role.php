<?php
App::uses('AppModel', 'Model');

class Role extends AppModel {

	public $_attributes = array(
		'id' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'name' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true
		)
	);

	public $hasMany = array(
		'Users' => array(
			'foreignKey' => 'role_id',
			// only show the related comment data on a request for a single post object
			'request' => 'collection' 
		)
	);

	// No validation, as roles are only modified directly in the database
	public $validate = array();

}