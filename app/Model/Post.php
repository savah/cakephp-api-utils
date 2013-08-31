<?php
App::uses('AppModel', 'Model');

class Post extends AppModel {

	public $_attributes = array(
		'id' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'user.id' => array(
			'field' => 'user_id',
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'title' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true
		),
		'body' => array(
			'type' => 'string',
			'sort' => false, // No use sorting on `body`
			'query' => true
		),
		'created' => array(
			'type' => 'datetime',
			'sort' => true,
			'query' => true
		),
		'modified' => array(
			'type' => 'datetime',
			'sort' => true,
			'query' => true
		)
	);

	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
			// always show related user data (default unless `request` is specified)
		)
	);

	public $hasMany = array(
		'Comments' => array(
			'foreignKey' => 'post_id',
			// only show the related comment data on a request for a single post object
			'request' => 'collection' 
		)
	);

	public $validate = array(
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => 'create',
				'allowEmpty' => false,
				'message' => 'Please enter a title',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Title must be no longer than 255 characters'
			)
		),
		'body' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => 'create',
				'allowEmpty' => false,
				'message' => 'Please enter a body',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 5000),
				'message' => 'Body must be no longer than 5,000 characters'
			)
		)
	);

	// always authorized
	public function isUserAuthorizedToCreate($data = array()) {
		return true;
	}

	// authorized if the post was originally created by current user
	public function isUserAuthorizedToUpdate($data = array()) {
		$user_id = $this->field('user_id');
		if ($user_id != $this->userId()) {
			return false;
		}
		return true;
	}

	// authorized if the post was originally created by current user
	public function isUserAuthorizedToDelete() {
		$user_id = $this->field('user_id');
		if ($user_id != $this->userId()) {
			return false;
		}
		return true;
	}

	// authorized to read all posts created by current user
	public function userIsAuthorizedToReadUserIds() {
		return array($this->userId());
	}

}