<?php
App::uses('AppModel', 'Model');

class Comment extends AppModel {

	public $_attributes = array(
		'id' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'post.id' => array(
			'field' => 'post_id',
			'type' => 'int',
			'sort' => true,
			'query' => true,
			// only show this attribute if it is requested; it is basically assumed since
			// comments are a sub-resource of posts
			'request' => true
		),
		'user.id' => array(
			'field' => 'user_id',
			'type' => 'int',
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
		'Post' => array(
			'foreignKey' => 'post_id',
			// only show the related post data on a request for a single comment object
			'request' => 'collection'
		),
		'User' => array(
			'foreignKey' => 'user_id'
			// always show related user data (default unless `request` is specified)
		)
	);

	public $validate = array(
		'body' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please enter a comment',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 500),
				'message' => 'Comments must be no longer than 500 characters'
			)
		)
	);

	// always authorized
	public function isUserAuthorizedToCreate($data = array()) {
		return true;
	}

	// authorized if the comment was originally created by current user
	public function isUserAuthorizedToUpdate($data = array()) {
		$user_id = $this->field('user_id');
		if ($user_id != $this->userId()) {
			return false;
		}
		return true;
	}

	// authorized if the comment was originally created by current user
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