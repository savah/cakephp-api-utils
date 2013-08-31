<?php
App::uses('AppModel', 'Model');

class User extends AppModel {

	public $_attributes = array(
		'id' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'role.id' => array(
			'field' => 'role_id',
			'type' => 'int',
			'sort' => true,
			'query' => true
		),
		'username' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true
		),
		'password' => array(
			'type' => 'string',
			'sort' => false, // do not sort
			'query' => false // do not query
		),
		'displayName' => array(
			'field' => 'display_name',
			'type' => 'string',
			'sort' => false, // do not sort
			'query' => false // do not sort
		),
		'email' => array(
			'type' => 'string',
			'sort' => true,
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
		'Role' => array(
			'foreignKey' => 'role_id'
			// always show related user data (default unless `request` is specified)
		)
	);

	public $hasMany = array(
		'Posts' => array(
			'foreignKey' => 'user_id',
			// only show the user's related post data if it is specifically requested
			'request' => true
		),
		'Comments' => array(
			'foreignKey' => 'user_id',
			// only show the user's related comment data if it is specifically requested
			'request' => true
		)
	);

	public $validate = array(
		'username' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => 'create',
				'allowEmpty' => false,
				'message' => 'Please enter a username',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Username must be no longer than 255 characters'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => 'create',
				'allowEmpty' => false,
				'message' => 'Please enter a password',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Password must be no longer than 255 characters'
			)
		),
		'display_name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Display name must be no longer than 255 characters'
			)
		),
		'email' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'allowEmpty' => true,
				'message' => 'Email must be no longer than 255 characters'
			)
		)
	);

	// hashes up the user's password on signup
	public function beforeSave($options = array()) {

		if (!empty($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = Security::hash(
				$this->data[$this->alias]['password'], 
				null, 
				true
			);
		}

	}

	// always authorized
	public function isUserAuthorizedToCreate($data = array()) {
		return true;
	}

	// authorized if current user is updating his/her own user
	public function isUserAuthorizedToUpdate($data = array()) {
		if ($this->id != $this->userId()) {
			return false;
		}
		return true;
	}

	// authorized if current user is deleting his/her own user
	public function isUserAuthorizedToDelete() {
		if ($this->id != $this->userId()) {
			return false;
		}
		return true;
	}

	// authorized to read current user
	public function userIsAuthorizedToReadIds() {
		return array($this->userId());
	}

}