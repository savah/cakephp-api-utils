<?php
App::uses('Component', 'Controller');

/**
 * Api Permissions Component
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
 * @subpackage  Api.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiPermissionsComponent extends Component {
	
	/**
	 * Default Permissions Behavior
	 *
	 * @since   1.0
	 * @var     mixed
	 */
	protected $_permissions_behavior = false;
	
	/**
	 * Model
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_model = '';
	
	/**
	 * Protected _parent_model
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_parent_model = '';
	
	/**
	 * Attributes
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_attributes = array();
	
	/**
	 * Fields
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_fields = array();
	
	/**
	 * On
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_on = 'read';
	
	/**
	 * Allow attributes cache 
	 */
	protected $_allow_attributes_cache = array();
	
	/**
	 * Components
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $components = array(
		'Auth',
		'Acl'
	);
	
	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		
		parent::__construct($collection, $settings);

		$this->Controller = $collection->getController();
		
		if (!empty($settings['permissionsBehavior'])) {
			$this->_permissions_behavior = $settings['permissionsBehavior'];
		}
		
	}
	
	/**
	 * For Model
	 *
	 * @since   1.0
	 * @return  object
	 */
	public function forModel($model = '') {
		
		$this->_model = $model;
		
		return $this;
		
	}
	
	/**
	 * On
	 *
	 * @since   1.0
	 * @return  object
	 */
	public function on($crud = 'read') {
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$this->_on = $crud;
		
		return $this;
		
	}
	
	/**
	 * With Permissions Behavior
	 *
	 * @since   1.0
	 * @param   mixed $behavior 
	 * @return  object
	 */
	public function withPermissionsBehavior($behavior = false) {
		
		$this->_permissions_behavior = $behavior;
		
		return $this;
		
	}
	
	/**
	 * With Parent Model
	 *
	 * @since   1.0
	 * @param   string $parent_model Parent Model name. 
	 * @return  object
	 */
	public function withParentModel($parent_model = null) {
		
		$this->_parent_model = $parent_model;
		
		return $this;
		
	}
	
	/**
	 * With Attributes
	 *
	 * @since   1.0
	 * @return  object
	 */
	public function withAttributes($attributes = array()) {
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$this->_attributes = $attributes;
		
		return $this;
		
	}
	
	/**
	 * With Fields
	 *
	 * @since   1.0
	 * @return  object
	 */
	public function withFields($fields = array()) {
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$this->_fields = $fields;
		
		return $this;
		
	}
	
	/**
	 * Can Create
	 *
	 * @since   1.0
	 * @param	array	$data	The data to save (as attributes). For example:
	 *							array(
	 *								'someAttribute' => 'some value',
	 *								'someOtherAttribute' => 'some other value'
	 *							)
	 * @return  boolean
	 */
	public function canCreate($data = array()) {
		
		if (empty($this->_model)) {
			return false;
		}
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$authenticated_user_id = $this->Auth->user('id');
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			// Model Alias is in Relation to Parent Model
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		if (
			!method_exists($modelObject, 'isUserAuthorizedToCreate') &&
			$this->_permissions_behavior !== false &&
			!$modelObject->Behaviors->enabled($this->_permissions_behavior)
		) {
			return false;
		}
		
		$role = $this->Auth->user('Role.slug');
		
		return $modelObject
			->userId($authenticated_user_id)
			->userRole($role)
			->isUserAuthorizedToCreate($data);
		
	}
	
	/**
	 * Can Update
	 *
	 * @since   1.0
	 * @return  boolean
	 */
	public function canUpdate($id = 0, $data = array()) {
		
		if (empty($id)) {
			return false;
		}
		
		if (empty($this->_model)) {
			return false;
		}
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$authenticated_user_id = $this->Auth->user('id');
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			// Model Alias is in Relation to Parent Model
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
					
		if (
			!method_exists($modelObject, 'isUserAuthorizedToUpdate') &&
			$this->_permissions_behavior !== false &&
			!$modelObject->Behaviors->enabled($this->_permissions_behavior)
		) {
			return false;
		}
		
		$role = $this->Auth->user('Role.slug');
		
		return $modelObject
			->userId($authenticated_user_id)
			->id($id)
			->userRole($role)
			->isUserAuthorizedToUpdate($data);
		
	}
	
	/**
	 * Can Delete
	 *
	 * @since   1.0
	 * @return  boolean
	 */
	public function canDelete($id = 0) {
		
		if (empty($id)) {
			return false;
		}
		
		if (empty($this->_model)) {
			return false;
		}
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$authenticated_user_id = $this->Auth->user('id');
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			// Model Alias is in Relation to Parent Model
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
					
		if (
			!method_exists($modelObject, 'isUserAuthorizedToDelete') &&
			$this->_permissions_behavior !== false &&
			!$modelObject->Behaviors->enabled($this->_permissions_behavior)
		) {
			return false;
		}
		
		$role = $this->Auth->user('Role.slug');
		
		return $modelObject
			->userId($authenticated_user_id)
			->id($id)
			->userRole($role)
			->isUserAuthorizedToDelete();
		
	}
	
	/**
	 * Require Conditions
	 *
	 * @since   1.0
	 * @return  array
	 */
	public function requireConditions() {
		
		if (!in_array($this->_on, array('read', 'update', 'delete'))) {
			return array();
		}
		
		$on = ucfirst($this->_on);
		$this->on();
		
		if (empty($this->_model)) {
			return array();
		}
		$model = $this->_model;
		$this->forModel();
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		if (empty($this->_fields)) {
			return array();
		}
		
		$fields = $this->_fields;
		
		$authenticated_user_id = $this->Auth->user('id');
		
		$role = $this->Auth->user('Role.slug');
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			// Model Alias is in Relation to Parent Model
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		if (
			$this->_permissions_behavior !== false &&
			$modelObject->Behaviors->enabled($this->_permissions_behavior)
		) {
			$permissions_settings = $modelObject->permissionsSettings();
		}
		
		$strlen = strlen($model) + 1;
		
		$conditions = array();
		
		foreach ($fields as $field) {
			
			if (!strstr($field, '.')) {
				continue;
			}
			
			$field = substr($field, $strlen);
			
			$methodSuffix = Inflector::pluralize(Inflector::camelize($field));
			
			// The following line may build a method name such as: `userIsAuthorizedToReadIds`
			$methodName = 'userIsAuthorizedTo' . $on . $methodSuffix;
			
			if (method_exists($modelObject, $methodName)) {
				
				$condition = $modelObject
					->userId($authenticated_user_id)
					->userRole($role)
					->{$methodName}();
				
				if ($condition !== '*') {
					$conditions[$model . '.' . $field] = $condition;
				}
				
			} elseif (
				!empty($permissions_settings['foreignKey']) && 
				$permissions_settings['foreignKey'] === $field
			) {
				
				$behaviorMethodName = 'userIsAuthorizedTo' . $on . 'ForeignKeys';
				
				$condition = $modelObject
					->userId($authenticated_user_id)
					->userRole($role)
					->{$behaviorMethodName}();
				
				if ($condition !== '*') {
					$conditions[$model . '.' . $field] = $condition;
				}
				
			}
			
		}
		
		return $conditions;
		
	}
	
	/**
	 * Allow Attributes
	 *
	 * @since   1.0
	 * @return  array
	 */
	public function allowAttributes() {
		
		$crud = $this->_on;
		
		if (empty($this->_model)) {
			return array();
		}
		
		$model = $this->_model;
		
		if (empty($this->_attributes)) {
			return array();
		}
		
		$attributes = $this->_attributes;
		
		$crc32_attributes = crc32(serialize($attributes));

		$role_id = $this->Auth->user('Role.id');
		
		if (empty($role_id) && $role_id !== 0) {
			$role_id = 'public';
		}
		
		$role_name = 'Role/' . $role_id;
		
		// The `Acl::check()` logic is extremely expensive, so we cache results so they're only made once
		$cached = Cache::read($this->_on . ':' . $this->_model . ':' . $crc32_attributes . ':' . $role_name, 'allowedAttributes');
		
		if ($cached !== false) {
			return $cached;
		}
		
		foreach ($attributes as $attribute => $settings) {
			if (!$this->Acl->check($role_name, 'crud/' . $model . '/' . $crud . '/' . $attribute)) {
				unset($attributes[$attribute]);
			}
		}
		
		Cache::write(
			$this->_on . ':' . $this->_model . ':' . $crc32_attributes . ':' . $role_name, 
			$attributes, 
			'allowedAttributes'
		);
		
		return $attributes;
		
	}
	
}
