<?php
App::uses('Component', 'Controller');
App::uses('CakeTime', 'Utility');

/**
 * Api Resource Component
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
class ApiResourceComponent extends Component {
	
	/**
	 * Validate Only
	 *
	 * @since   1.0
	 * @var     boolean
	 */
	protected $_validate_only = false;
	
	/**
	 * Skip Errors And Save
	 *
	 * @since   1.0
	 * @var     boolean
	 */
	protected $_skip_errors_and_save = false;
	
	/**
	 * Field Map
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_field_map = array();
	
	/**
	 * Fields
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_fields = array();
	
	/**
	 * Required Attributes
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_required_attributes = array();
	
	/**
	 * Metadata Fields
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_metadata_fields = array();
	
	/**
	 * Special Fields
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_special_fields = array();
	
	/**
	 * Field Exceptions
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_field_exceptions = array();
	
	/**
	 * Result
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_result = array();
	
	/**
	 * Parent Model
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_parent_model = null;
	
	/**
	 * Model
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_model = null;
	
	/**
	 * Transaction
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_transactions = true;
	
	/**
	 * Passed Conditions
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_passed_conditions = array();
	
	/**
	 * Related Models
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_related_models = array();
	
	/**
	 * Related Field Dependencies
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_related_field_dependencies = array();
	
	/**
	 * Single Result
	 *
	 * @since   1.0
	 * @var     boolean
	 */
	protected $_single_result = false;
	
	/**
	 * ID
	 *
	 * @since   1.0
	 * @var     integer
	 */
	protected $_id = 0;
	
	/**
	 * Validation Errors
	 * 
	 * @var	array
	 *			Model
	 *				Model		=> model error message
	 *				fieldname	=> field error message
	 *				fieldname	=> field error message
	 *					
	 */
	protected $_validation_errors = array();
	
	/**
	 * Validation Index
	 * 
	 * @var	array
	 */
	protected $_validation_index = array(0);
	
	/**
	 * Noramlized Data
	 * @var	array 
	 */
	protected $_data = array();
	
	/**
	 * Relations
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_relations = array(
		'belongsTo',
		'hasOne',
		'hasMany'
	);

	/**
	 * Data Fields
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_data_fields = array();
	
	/**
	 * On
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_on = 'read';
	
	/**
	 * Was deleted
	 *
	 * @since   1.0
	 * @var     boolean
	 */
	protected $_was_deleted = false;
	
	/**
	 * Components
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $components = array(
		'ApiRequestHandler' => array(
			'className' => 'Api.ApiRequestHandler'
		),
		'ApiPaginator' => array(
			'className' => 'Api.ApiPaginator'
		),
		'Api' => array(
			'className' => 'Api.Api'
		),
		'Permissions' => array(
			'className' => 'Api.ApiPermissions',
			'useBehavior' => 'WorkspacePermissions'
		),
		'Query' => array(
			'className' => 'Api.ApiQuery'
		)
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
		
	}
	
	/**
	 * Save Associated hasOne Model Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data			Array of Resource Indexed Data
	 * @param	string	$this->_parent_model	Primary Resource Name
	 * @param	string	$this->_model			Associated Resource Name
	 * @param	int		$this->_id				Primary Resource ID
	 * @return	boolean 
	 */
	public function saveAssociatedHasOne() {
		
		if (empty($this->_model)) {
			$this->setResponseCode(5001);
			return false;
		}
		
		// Data Should At Least Have Foreign ID
		if (empty($this->_data) || empty($this->_data[$this->_model])) {
			$this->setResponseCode(4005);
			return false;
		}
		
		$data = $this->_data;
		$this->withData();
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$id = $this->_id;
		$this->withId();
		
		// Should Not be indexed array of fields
		if (Hash::numeric(array_keys($data[$model]))) {
			// Put in Validation Error
			$this->setResponseCode(4008);
			return false;
		}
		
		$modelObject = $this->Controller->{$parent_model}->{$model};
		
		$foreignKey = $this->Controller->{$parent_model}->hasOne[$model]['foreignKey'];
		$foreign_conditions = $this->Controller->{$parent_model}->hasOne[$model]['conditions'];
		
		// Associated Foreign ID from Data Does Not Match Primary ID
		if (isset($data[$model][$foreignKey]) &&
			$data[$model][$foreignKey] != $id) {
			$this->setResponseCode(4002);
			return false;
		}
		
		// Find Associated Row
		$foreignRow = $modelObject->find('first', array(
			'fields' => array($model . '.' . $modelObject->primaryKey),
			'conditions' => array_merge(
				array($model . '.' . $foreignKey => $id),
				!empty($foreign_conditions) ? $foreign_conditions : array()
			)
		));
		$foreignID = !empty($foreignRow[$model][$modelObject->primaryKey]) ? $foreignRow[$model][$modelObject->primaryKey] : null;
		
		// Make Sure we Save New Foreign IDs
		$data[$model][$foreignKey] = $id;

		return $this
			->withParentModel($parent_model)
			->forModel($model)
			->withId($foreignID)
			->withFieldExceptions(array($foreignKey))
			->withData(array($model => $data[$model]))
			->saveOne();
		
	}
	
	/**
	 * Save Associated hasMany Model Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data			Array of Resource Indexed Data
	 * @param	string	$this->_parent_model	Primary Resource Name
	 * @param	string	$this->_model			Associated Resource Name
	 * @param	int		$this->_id				Primary Resource ID
	 * @return	boolean 
	 */
	public function saveAssociatedHasMany() {
		
		if (empty($this->_model)) {
			$this->setResponseCode(5001);
			return false;
		}
		
		// Data Should At Least Have Foreign ID
		if (empty($this->_data) || empty($this->_data[$this->_model])) {
			$this->setResponseCode(4005);
			return false;
		}
		
		$data = $this->_data;
		$this->withData();
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$id = $this->_id;
		$this->withId();
		
		// Should be indexed array of fields
		if (!Hash::numeric(array_keys($data[$model]))) {
			// Put in Validation Error
			$this->setResponseCode(4009);
			return false;
		}
		
		$modelObject = $this->Controller->{$parent_model}->{$model};

		$foreignKey = $this->Controller->{$parent_model}->hasMany[$model]['foreignKey'];
		$foreign_conditions = $this->Controller->{$parent_model}->hasMany[$model]['conditions'];
		
		$validationIndex = $this->_validation_index;
		
		$read_field_map = $this
			->forModel($model)
			->withParentModel($parent_model)
			->returnsFieldMap('read');

		foreach ($data[$model] as $index => $fields) {
			
			$associatedIndex = $validationIndex;
			$associatedIndex[] = $index;
			$this->setValidationIndex($associatedIndex);
			
			$readable_fields = $this
				->forModel($modelObject->name)
				->withFieldMap($read_field_map)
				->withDataFields($fields)
				->returnsFilteredDataFields();
			
			// Associated Foreign ID from Data Does Not Match Primary ID
			if (
				isset($readable_fields[$foreignKey]) &&
				$readable_fields[$foreignKey] != $id
			) {
				$this->setResponseCode(4002);
				return false;
			}
			
			if (!isset($readable_fields[$foreignKey])) {
				$readable_fields[$foreignKey] = $id;
			}
						
			$foreignID = 0;

			// If Related ID Passed, Verify Belongs to Primary Record
			if (!empty($readable_fields['id'])) {

				// Find Associated Row
				$foreignRow = $modelObject->find('first', array(
					'fields' => array($model . '.' . $modelObject->primaryKey),
					'conditions' => array_merge(
						array(
							$model . '.id' => $readable_fields['id'],
							$model . '.' . $foreignKey => $id
						),
						!empty($foreign_conditions) ? $foreign_conditions : array()
					)
				));

				// ID Does Not Belong
				if (empty($foreignRow[$model][$modelObject->primaryKey])) {
					$this->setResponseCode(4003);
					return false;
				}
				
				$foreignID = $readable_fields['id'];

			}
			
			// Find Related ID by Unique ID
			if (
				empty($readable_fields['id']) &&
				$modelObject->hasUniqueID($parent_model)
			) {

				$uniqueConditions = $modelObject->getUniqueConditions($parent_model, $id, $readable_fields);
				
				if (empty($uniqueConditions)) {
					$this->setResponseCode(4006);
					return false;
				}

				$foreignID = $modelObject->getUniqueID($uniqueConditions);

			}
			
			// Make Sure we Save New Foreign Key IDs
			$fields[$foreignKey] = $id;

			$result = $this
				->withParentModel($parent_model)
				->forModel($model)
				->withId($foreignID)
				->withFieldExceptions(array($foreignKey))
				->withData(array($model => $fields))
				->saveOne();

			if (empty($result)) {
				return false;
			}
			
		}
		
		return true;
		
	}
	
	/**
	 * Utility Function to Validate and Save Single Resource Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data			Array of Resource Indexed Data
	 * @param	string	$this->_parent_model	Primary Resource Name
	 * @param	string	$this->_model			Associated Resource Name
	 * @param	int		$this->_id				Resource ID Being Saved
	 * @return	boolean 
	 */
	public function saveOne() {
		
		if (empty($this->_model)) {
			$this->setResponseCode(5001);
			return false;
		}
		
		$data = $this->_data;
		$this->withData();
		
		$field_exceptions = $this->_field_exceptions;
		$this->withFieldExceptions();
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$id = $this->_id;
		$this->withId();
		
		$create = (bool) ($id == 0);
		
		$field_map = $this
			->withParentModel($parent_model)
			->forModel($model)
			->returnsFieldMap($create ? 'create' : 'update');
		
		// Model Data May be Empty
		$data[$model] = !empty($data[$model]) ? $data[$model] : array();
		
		$fields = array();
		if (!empty($field_map)) {
			$fields = $this
				->forModel($model)
				->withFieldMap($field_map)
				->withDataFields($data[$model])
				->withFieldExceptions($field_exceptions)
				->returnsFilteredDataFields();
		}
		
		if ($create) {
			
			// There Should be No ID in Data
			if (!empty($data[$model]['id'])) {
				$this->setResponseCode(4001);
				return false;
			}
			
			// Can Create
			if (!$this->Permissions
				->withParentModel($parent_model)
				->forModel($model)
				->canCreate($fields)) {
				$this->setResponseCode(4013);
				return false;
			}
			
		} else {
			
			// Verfiy ID Data Param Matches ID
			if (isset($data[$model]['id']) &&
				$data[$model]['id'] != $id
				) {
				$this->setResponseCode(4002);
				return false;
			}
			
			// Can Update
			if (!$this->Permissions
				->withParentModel($parent_model)
				->forModel($model)
				->canUpdate($id, $fields)) {
				$this->setResponseCode(4013);
				return false;
			}

			// Set ID Data Param
			$fields['id'] = $id;
			
		}
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		$modelObject->create(false);
		$modelObject->set(array($model => $fields));
		
		// Validation
		if (!$modelObject->validates() ||
			!is_null($this->getIndexValidationErrors())) {
			
			$validationErrors = $modelObject->validationErrors;

			$this->setResponseCode(4012);

			if (!empty($validationErrors)) {
				$this->forModel($parent_model)
					 ->withFieldMap($field_map)
					 ->setValidationErrors($validationErrors);
			}

			return false;
		} 
		
		$fieldList = array_keys(Hash::expand($field_map));		
		if (!empty($field_exceptions)) {
			$fieldList = array_merge($fieldList, $field_exceptions);
		}
		
		// Validate Only
		if ($this->validateOnly()) {
			return true;
		}
		
		// Save Data
		if (!$modelObject->save(
			array(),
			array(
				'fieldList' => $fieldList,
				'validate' => false
			)
		)) {
			$this->setResponseCode(5001);
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * For Model
	 *
	 * @since   1.0
	 * @param   string $model Model name. 
	 * @return  object
	 */
	public function forModel($model = null) {
		
		$this->_model = $model;
		
		return $this;
		
	}
	
	/**
	 * Validate Only
	 *
	 * @since   1.0
	 * @param   boolean $validate 
	 * @return  object
	 */
	public function validateOnly($validate = null) {
		
		if (is_bool($validate)) {
			$this->_validate_only = $validate;
		}
		
		return $this->_validate_only;
		
	}
	
	/**
	 * Skip Errors And Save
	 *
	 * @since   1.0
	 * @param   boolean $validate 
	 * @return  object
	 */
	public function skipErrorsAndSave($skip = null) {
		
		if (is_bool($skip)) {
			$this->_skip_errors_and_save = $skip;
		}
		
		return $this->_skip_errors_and_save;
		
	}
	
	/**
	 * With Required Attributes
	 *
	 * @since   1.0
	 * @param   string $model Model name. 
	 * @return  object
	 */
	public function withRequiredAttributes($requested_attributes = array()) {
		
		$this->_required_attributes = $requested_attributes;
		
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
	 * With Field Map
	 *
	 * @since   1.0
	 * @param   array $field_map Field map array. 
	 * @return  object
	 */
	public function withFieldMap($field_map = array()) {
		
		$this->_field_map = $field_map;
		
		return $this;
		
	}
	
	/**
	 * With Fields
	 *
	 * @since   1.0
	 * @param   array $fields Fields array. 
	 * @return  object
	 */
	public function withFields($fields = array()) {
		
		$this->_fields = $fields;
		
		return $this;
		
	}
	
	/**
	 * With Metadata Fields
	 *
	 * @since   1.0
	 * @param   array $metadata_fields Metadata Fields array. 
	 * @return  object
	 */
	public function withMetadataFields($metadata_fields = array()) {
		
		$this->_metadata_fields = $metadata_fields;
		
		return $this;
		
	}
	
	/**
	 * With Special Fields
	 *
	 * @since   1.0
	 * @param   array $special_fields Special Fields array - JSON encoded, Virtual Fields, etc. 
	 * @return  object
	 */
	public function withSpecialFields($special_fields = array()) {
		
		$this->_special_fields = $special_fields;
		
		return $this;
		
	}
	
	/**
	 * With Field Exceptions
	 *
	 * @since   1.0
	 * @param   array $field_exceptions Field exceptions array. 
	 * @return  object
	 */
	public function withFieldExceptions($field_exceptions = array()) {
		
		$field_exceptions = array_merge(
			$this->_field_exceptions,
			$field_exceptions
		);
		
		$this->_field_exceptions = array_unique($field_exceptions);
		
		return $this;
		
	}
	
	/**
	 * With Data Fields
	 *
	 * @since   1.0
	 * @param   array $fields Field array. 
	 * @return  object
	 */
	public function withDataFields($fields = array()) {
		
		$this->_data_fields = $fields;
		
		return $this;
		
	}
	
	/**
	 * With Results
	 *
	 * @since   1.0
	 * @param   array $result Result array. 
	 * @return  object
	 */
	public function withResult($result = array()) {
		
		$this->_result = $result;
		
		return $this;
		
	}
	
	/**
	 * With Passed Conditions
	 *
	 * @since   1.0
	 * @param   array $conditions Conditions array. 
	 * @return  object
	 */
	public function withPassedConditions($conditions = array()) {
		
		$this->_passed_conditions = $conditions;
		
		return $this;
		
	}
	
	/**
	 * With Related Models 
	 *
	 * @since   1.0
	 * @param   array $related_models Related models array. 
	 * @return  object
	 */
	public function withRelatedModels($related_models = array()) {
		
		$this->_related_models = $related_models;
		
		return $this;
		
	}
	
	/**
	 * With Related Field Dependencies
	 *
	 * @since   1.0
	 * @param   string $related_field_dependencies Related field dependencies array. 
	 * @return  object
	 */
	public function withRelatedFieldDependencies($related_field_dependencies = array()) {
		
		$this->_related_field_dependencies = $related_field_dependencies;
		
		return $this;
		
	}
	
	/**
	 * With Transactions
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   string $transactions. 
	 * @return  object
	 */
	public function withTransactions($transactions = false) {
		
		$this->_transactions = $transactions;
		
		return $this;
		
	}

	/**
	 * Has Transactions
	 *
	 * @since   1.0
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @param   string $transactions. 
	 * @return  boolean
	 */
	public function hasTransactions() {
		
		return $this->_transactions;
		
	}
	
	/**
	 * Requiring A Single Result
	 *
	 * @since   1.0
	 * @return  object
	 */
	public function requiringASingleResult() {
		
		$this->_single_result = true;
		
		return $this;
		
	}
	
	/**
	 * With Id
	 *
	 * @since   1.0
	 * @param   integer $id Id. 
	 * @return  object
	 */
	public function withId($id = 0) {
		
		$this->_id = $id;
		
		return $this;
		
	}
	
	public function hasId() {
		
		return $this->_id;
		
	}
	
	/**
	 * Utility function to expand model-level "dotted" data
	 * e.g., $data['Model.field'] -> $data['Model']['field']
	 * 
	 * @author Paul W. Smith <paul@wizehive.com>
	 * @param array $data
	 * @return array
	 */
	public function expandFlattenedData($data = array()) {
		
		if (!is_array($data)) {
			return $data;
		}
		
		return Hash::expand($data);
	}
	
	/**
	 * With Data
	 *
	 * @since   1.0
	 * @param   array $data Data array. 
	 * @return  object
	 */
	public function withData($data = array()) {
		
		$data = $this->expandFlattenedData($data);
		
		if (empty($this->_model)) {
			return false;
		}
		
		$model = $this->_model;
		
		if (is_array($data)) {
			$this->_data = $data;
		}
		
		$this->forModel($model);
		
		return $this;
		
	}
	
	/**
	 * On
	 *
	 * @since   1.0
	 * @return  object Return object.
	 */
	public function on($crud = 'read') {
		
		$this->_on = $crud;
		
		return $this;
		
	}
	
	/**
	 * Set App Code
	 *
	 * @since   1.0
	 * @param   integer $code Code. 
	 * @return  boolean Boolean results.
	 */
	public function setResponseCode($code = 0) {
		
		return $this->Api->setResponseCode($code);
		
	}
	
	/**
	 * Append Validation Errors
	 * 
	 * Pass null to clear existing errors
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	mixed	$errors
	 * @return	boolean 
	 */
	public function setValidationErrors($errors = null) {
		
		$model = !empty($this->_model) ? $this->_model : $this->Controller->modelClass;
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$field_map = $this->_field_map;
		
		if ($this->{$model} && $this->{$model}->Behaviors->loaded('SpecialFields')) {
			// Process special fields (JSON-encoded, virtual fields, etc.)
			$validation_fields = $this->{$model}->getValidationFieldNames();
			$field_map = array_merge($validation_fields, $field_map);
			
		}
		
		$validationIndex = $this->getValidationIndexKey();
		
		if (is_null($errors)) {
			unset($this->_validation_errors[$validationIndex]);
			return true;
		}
		
		$formatted_errors = array();
		
		$errors = Hash::flatten($errors);
		
		if (empty($this->_validation_errors[$validationIndex])) {
			$this->_validation_errors[$validationIndex] = array();
		}
		
		// Translate Model Field Keys to API Field Keys
			
		foreach ($errors as $field => $message) {

			$potential_numeric_key = substr($field, -2);

			if (preg_match('/\\.[0-9]/', $potential_numeric_key)) {
				$numeric_key = $potential_numeric_key;
				$strlen = strlen($field);
				$field = substr($field, 0, ($strlen - 2));
			} else {
				$numeric_key = '';
			}

			if (
				array_key_exists($field, $field_map) || 
				$field === $model
			) {

				if (empty($numeric_key)) {
					$message = array($message);
				}

				$attribute = $field;
				if ($field !== $model) {
					$attribute = $field_map[$field];
				}

				$formatted_errors[$attribute . $numeric_key] = $message;

			}

		}
		
		$formatted_errors = Hash::expand($formatted_errors);
		
		$this->_validation_errors[$validationIndex] = array_merge(
			$this->_validation_errors[$validationIndex],
			$formatted_errors
		);
		
		return $this;
		
	}
	
	/**
	 * Clear Validation Errors and Reset Validation Index
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	object	$this
	 */
	public function resetValidationErrors() {
		
		$this->_validation_errors = array();
		
		$this->_validation_index = array();
		
		return $this;
		
	}
	
	/**
	 * Set Validation Index
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array|string	$index  
	 * @return	object			$this
	 */
	public function setValidationIndex($index = array()) {
		
		if (is_string($index)) {
			$index = lcfirst(explode('.', $index));
		} elseif (is_array($index)) {
			$index = array_map('lcfirst', $index);
		}
		
		$this->_validation_index = $index;
		
		return $this;
		
	}
	
	/**
	 * Get Validation Index Key
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$index
	 * @return	string 
	 */
	public function getValidationIndexKey($index = null) {
		
		if (is_null($index)) {
			$index = $this->_validation_index;
		}
		
		// Default Key is 0
		if (empty($index)) {			
			$index = array(0);
		}
		
		if (!is_array($index)) {
			$index = array($index);
		}
		
		return implode('.', $index);
		
	}
	
	/**
	 * Get Index Validation Errors
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$index
	 * @return	array
	 */
	public function getIndexValidationErrors($index = null) {
		
		$validationIndex = $this->getValidationIndexKey($index);
		
		if (empty($this->_validation_errors[$validationIndex])) {
			return null;
		}
		
		return $this->_validation_errors[$validationIndex];
		
	}
	
	/**
	 * Renders conditions
	 *
	 * @since   1.0
	 * @return  array Conditions arrays or empty array on error.
	 */
	public function rendersConditions() {
		
		if (empty($this->_fields)) {
			return array();
		}
		
		$model = !empty($this->_model) ? $this->_model : $this->Controller->modelClass;
		$this->forModel();
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$permission_conditions = $this->Permissions
			->withParentModel($parent_model)
			->forModel($model)
			->withFields($this->_fields)
			->on($this->_on)
			->requireConditions();
		
		$passed_conditions = $this->_passed_conditions;
		$this->withPassedConditions();
		
		if (!empty($passed_conditions)) {
			foreach ($passed_conditions as $field => $condition) {
				if (
					strpos($field, '.') === false &&
					!in_array($field, array('AND', 'OR')) &&
					!is_int($field) &&
					!empty($condition)
				) {
					unset($passed_conditions[$field]);
					$passed_conditions[$model . '.' . $field] = $condition;
				}
			}
		}
		
		$conditions = array();
		foreach ($permission_conditions as $field => $condition) {
			if (!array_key_exists($field, $passed_conditions)) {
				$conditions[$field] = $condition;
				continue;
			}
			if ($condition === false) {
				$condition = array();
			}
			if (is_string($passed_conditions[$field])) {
				$passed_conditions[$field] = array($passed_conditions[$field]);
			}
			if (is_array($passed_conditions[$field])) {
				$conditions[$field] = array_intersect($condition, $passed_conditions[$field]);
			}
			if (array_key_exists($field, $conditions) && count($conditions[$field]) === 1) {
				$conditions[$field] = array_shift($conditions[$field]);
			}
			if (array_key_exists($field, $conditions) && count($conditions[$field]) === 0) {
				$conditions[$field] = false;
			}
		}
		foreach ($passed_conditions as $field => $condition) {
			if (!array_key_exists($field, $conditions)) {
				$conditions[$field] = $condition;
			}
		}
		
		return $conditions;
		
	}
	
	/**
	 * Has validation errors
	 *
	 * @since   1.0
	 * @return  mixed Null on error or validations errors array.
	 */
	public function hasValidationErrors() {
		
		if (empty($this->_validation_errors)) {
			return null;
		}
		
		$validation_errors = $this->_validation_errors;
		$this->resetValidationErrors();
		
		$validation_errors = Hash::expand($validation_errors);
		
		return $validation_errors;
		
	}
	
	/**
	 * Has Required Attributes
	 *
	 * Get required attributes (the ones that were requested, otherwise the defaults)
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function hasRequiredAttributes() {
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$model = $this->_model;
		$this->forModel();
		
		$primary_model = empty($parent_model) ? true : false;

		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			$modelObject = $this->Controller->{$parent_model};
		}

		$temp_associated = $modelObject->getAssociated();
		
		$associated = array();
		if (!empty($temp_associated)) {
			foreach ($temp_associated as $association => $type) {
				$associated[] = lcfirst($association);
			}
		}
		
		$requested = $this->Query->requestedAttributes();
		
		$filtered = array();
		
		if (!empty($requested)) {
				
			foreach ($requested as $attribute) {
				
				$related = false;
				if (!empty($associated)) {
					if (in_array($attribute, $associated)) {
						$related = true;
					} else {
						foreach ($associated as $association) {
							$strlen = strlen($association) + 1;
							if (substr($attribute, 0, $strlen) === $association . '.') {
								$related = true;
								break;
							}
						}
					}
				}
				
				if (empty($parent_model)) {
					
					// Ignore Related Attributes
					if (!$related) {
						$filtered[] = $attribute;
					}
					
				}
				else {
					
					// Extract Related Attributes by Model
					if ($related) {
						
						$dotPos = strpos($attribute, '.');
						
						if ($dotPos !== false) {
							
							$attributeModel = ucfirst(substr($attribute, 0, $dotPos));
							
							if ($attributeModel === $model) {
								
								$attribute = substr($attribute, $dotPos+1);
								$filtered[] = $attribute;
							}
							
						}
						
					}
					
				}
				
			}
			
		}
		
		if (empty($filtered)) {
			
			if (empty($model)) {
				return array();
			}
			
			if ($primary_model) {
				$modelObject = $this->Controller->{$model};
			} else {
				$modelObject = $this->Controller->{$parent_model}->{$model};
			}
			
			$filtered = $modelObject->getDefaultAttributes(array(
				'primary' => $primary_model,
				'primaryClassName' => $parent_model
			));
			
		}
		
		return $filtered;
		
	}
	
	/**
	 * Has Required Relations
	 *
	 * @since   1.0
	 * @return  array Empty on error or related models array.
	 */
	public function hasRequiredRelations() {
		
		$model = !empty($this->_model) ? $this->_model : $this->Controller->modelClass;
		$this->forModel();
		
		$single_result = $this->_single_result;
		$this->_single_result = false;
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$original_requested_relations = $this->Query->requestedRelations();
		$requested_relations = !empty($original_requested_relations) ? $original_requested_relations : array();
		$requested_count = count($requested_relations);
		
		$related_models = array();
		foreach ($this->_relations as $relation) {
			if (!empty($this->{$model}->{$relation})) {
				foreach ($this->{$model}->{$relation} as $relatedModelName => $settings) {
					$related_models[$relatedModelName] = array_merge(
						array('type' => $relation),
						$settings
					);
					if (
						(
							empty($settings['request']) ||
							(
								is_string($settings['request']) &&
								!empty($single_result) &&
								$settings['request'] === 'collection'
							) ||
							(
								is_string($settings['request']) &&
								empty($single_result) &&
								$settings['request'] === 'object'
							)
						) && 
						$requested_count === 0 &&
						$original_requested_relations !== false
					) {
						$requested_relations[] = $relatedModelName;
					}
					if (
						!empty($settings['require']) && 
						(
							$original_requested_relations === false ||
							!in_array($relatedModelName, $requested_relations)
						) &&
						(
							is_bool($settings['require']) ||
							(
								is_string($settings['require']) &&
								!empty($single_result) &&
								$settings['require'] === 'object'
							) ||
							(
								is_string($settings['require']) &&
								empty($single_result) &&
								$settings['require'] === 'collection'
							)
						)
					) {
						$requested_relations[] = $relatedModelName;
					}
				}
			}
		}
		
		if (!empty($requested_relations)) {
			foreach ($requested_relations as $key => $relation) {
				$requested_relations[$key] = ucfirst($relation);
			}
			$related_models = array_intersect_key(
				$related_models, 
				Hash::normalize($requested_relations)
			);
		} else {
			$related_models = array();
		}
		
		foreach ($related_models as $relation => $settings) {
			if (!$this->{$model}->isRelationFindable($relation)) {
				unset($related_models[$relation]);
			}
		}
		
		$this->withRelatedModels($related_models);
		
		return $related_models;
		
	}
	
	/**
	 * Has Related Field Names
	 *
	 * @since   1.0
	 * @return  array Related field names array.
	 */
	public function hasRelatedFieldNames() {
		
		$field_names = array();
		if (!empty($this->_fields)) {
			$field_names = $this->_fields;
			$this->withFields();
		}
		
		$related_models = array();
		if (!empty($this->_related_models)) {
			$related_models = $this->_related_models;
			$this->withRelatedModels();
		}
		
		$related_field_names = array();
		if (!empty($related_models)) {
			foreach ($related_models as $association => $assocData) {
				if ($assocData['type'] === 'belongsTo') {
					$related_field_names = array_merge(
						$related_field_names,
						$this->{$this->Controller->modelClass}->fieldDependencies(array(
							$this->Controller->modelClass => array(
								'belongsTo' => array($association)
							)
						))
					);
				}
			}
		}
		foreach ($related_field_names as $key => $field) {
			$related_field_names[$key] = $this->Controller->modelClass . '.' . $field;
		}
		$related_field_names = array_unique($related_field_names);
		
		if (!empty($related_field_names)) {
			foreach ($related_field_names as $key => $field) {
				if (in_array($field, $field_names)) {
					unset($related_field_names[$key]);
				}
			}
			$related_field_names = array_filter($related_field_names);
		}
		
		return $related_field_names;
		
	}
	
	/**
	 * Returns Fields As Attributes
	 *
	 * @since   1.0
	 * @return  mixed Fields as attributes array or null?
	 */
	public function returnsFieldsAsAttributes() {
		
		if (empty($this->_result) || empty($this->_field_map)) {
			return $this->_result;
		}
		
		$field_map = $original_field_map = $this->_field_map;
		$this->withFieldMap();
		
		$model = !empty($this->_model) ? $this->_model : $this->Controller->modelClass;
		$this->forModel();
		
		$result = $this->_result;
		
		$result = Hash::flatten($result, '.');
		
		uksort($field_map, function($first, $second){
			return strlen($second) - strlen($first);
		});
		
		$new_row = array();
		foreach ($field_map as $field => $attribute) {
			$strlen = strlen($field);
			foreach ($result as $key => $value) {
				$key_strlen = strlen($key);
				$starts_with_field = substr($key, 0, $strlen) === $field;
				if ($starts_with_field) {
					$suffix = '';
					if ($key_strlen > $strlen) {
						$suffix = substr($key, $strlen);
					}
					$new_row[$attribute . '___' . $suffix] = $result[$key];
					unset($result[$key]);
				}
			}
		}
		
		$temp = array();
		foreach ($new_row as $key => $value) {
			list($prefix, $suffix) = explode('___', $key);
			if (empty($suffix)) {
				$new_value = $value;
			} else {
				$new_value = Hash::expand(array(substr($suffix, 1) => $value));
			}
			if (
				!array_key_exists($prefix, $temp) || 
				!is_array($temp[$prefix])
			) {
				$temp[$prefix] = $new_value;
			} else {
				$temp[$prefix] = Hash::merge($temp[$prefix], $new_value);
			}
		}
		
		$attribute_order = array_intersect_key(array_flip($original_field_map), $temp);
		
		$return = array_merge($attribute_order, $temp);
		
		return $return;
		
	}
	
	/**
	 * Returns Formatted Result
	 *
	 * @since   1.0
	 * @return  array Formatted result array.
	 */
	public function returnsFormattedResult() {
		
		$cached = array();
		
		if (empty($this->_result) || empty($this->_field_map)) {
			return $this->_result;
		}
		
		$primary_field_map = $this->_field_map;
		$this->withFieldMap();
		
		$model = !empty($this->_model) ? $this->_model : $this->Controller->modelClass;
		$this->forModel();
		
		$result = $this->_result;
		
		$is_assoc = !Hash::numeric(array_keys($result));
		
		if ($is_assoc) {
			$result = array($result);
		}
			
		$lists = array();

		$timezone = $this->Query->getTimezone();
		
		foreach ($result as $key => $data_group) {
			
			foreach ($data_group as $data_model => $fields) {
				
				$crc32 = $data_model . ':' . is_array($fields) ? serialize($fields) : 'null';
				
				if (array_key_exists($crc32, $cached)) {
					$fields = $cached[$crc32];
				}

				if (!is_null($fields) && !array_key_exists($crc32, $cached)) {
				
					if ($model === $data_model) {
						$modelObject = $this->Controller->{$model};
					} else {
						$modelObject = $this->Controller->{$model}->{$data_model};
					}

					$attributes = $modelObject->attributes();

					if ($model === $data_model) {
						$field_map = $primary_field_map;
					} else {
						$filtered_attributes = $this->Permissions
							->forModel($modelObject->name)
							->withAttributes($attributes)
							->on('read')
							->allowAttributes();
						if (empty($filtered_attributes)) {
							unset($result[$key][$data_model]);
							continue;
						}
						$field_map = $modelObject->getFieldMap($filtered_attributes);
					}

					$is_fields_assoc = !Hash::numeric(array_keys($fields));

					if ($is_fields_assoc) {
						$fields = array($fields);
					}

					$field_options = array();
					if (!empty($fields[0]) && is_array($fields[0])) {
						foreach ($fields[0] as $field => $field_value) {
							$field_options[$field] = $modelObject->getOptions($field);
						}
					}
					$attribute_options = array();
					if (!empty($attributes)) {
						foreach ($attributes as $attribute => $settings) {
							if (!empty($settings['values']['options'])) {
								$attribute_options[$attribute] = $modelObject->getOptions($settings['values']['options']);
							}
						}
					}

					foreach ($fields as $fields_key => $fields_data) {

						$fields[$fields_key] = $fields_data = $this
							->forModel($data_model)
							->withFieldMap($field_map)
							->withResult($fields_data)
							->returnsFieldsAsAttributes();

						foreach ($fields_data as $attribute => $attribute_value) {

							$field = array_search($attribute, $field_map);

							if (!empty($field_options[$field]) && !is_array($attribute_value)) {
								$fields[$fields_key][$attribute] = $fields_data[$attribute] = $attribute_value = array_key_exists($attribute_value, $field_options[$field]) ? $field_options[$field][$attribute_value] : $attribute_value;
							} elseif (!empty($attribute_options[$attribute]) && !is_array($attribute_value)) {
								$fields[$fields_key][$attribute] = $fields_data[$attribute] = $attribute_value = array_key_exists($attribute_value, $attribute_options[$attribute]) ? $attribute_options[$attribute][$attribute_value] : $attribute_value;
							}

							if (empty($attributes)) {
								continue;
							}

							if (!array_key_exists($attribute, $attributes)) {
								continue;
							}

							if (empty($attributes[$attribute]['type'])) {
								continue;
							}

							// Convert SQL date/datetime to ISO8601
							if (in_array($attributes[$attribute]['type'], array('datetime', 'date'))) {

								if (
									empty($attribute_value) ||
									!is_string($attribute_value)
								) {
									continue;
								}

								if (
									$attribute_value === '0000-00-00' ||
									$attribute_value === '0000-00-00 00:00:00'
								) {

									$attribute_value = null;

								} else {

									$is_datetime = !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $attribute_value);

									$datetime = new DateTime($attribute_value);

									$app_timezone = date_default_timezone_get();

									if (!empty($timezone) && $timezone !== $app_timezone) {
										$datetime->setTimezone(new DateTimeZone($timezone));
									}

									$format = ($is_datetime) ? DATE_ISO8601 : 'Y-m-d';

									$attribute_value = $datetime->format($format);

								}

								$fields[$fields_key][$attribute] = $fields_data[$attribute] = $attribute_value;

								continue;
							}

							// Convert Data Types if `DataTypeJuggling` Behaviors is loaded
							if ($modelObject->Behaviors->loaded('DataTypeJuggling')) {
								$attribute_value = $modelObject->convertDataType($attribute_value, $attributes[$attribute]['type']);
								$fields[$fields_key][$attribute] = $fields_data[$attribute] = $attribute_value;
							}

						}

						$fields[$fields_key] = Hash::expand($fields[$fields_key]);

					}

					if ($is_fields_assoc) {
						$fields = $fields[0];
					}
					
					$cached[$crc32] = $fields;
				
				}

				$result[$key][$data_model] = $fields;

				if ($model === $data_model) {
					
					$result[$key] = array_merge(
						$result[$key][$data_model],
						$result[$key]
					);
					unset($result[$key][$data_model]);
					
				} else {
					
					$result[$key][lcfirst($data_model)] = $result[$key][$data_model];
					unset($result[$key][$data_model]);
					
				}
				
			}
			
			if (!empty($result[$key])) {
				
				if (array_key_exists('created', $result[$key])) {
					$value = $result[$key]['created'];
					unset($result[$key]['created']);
					$result[$key]['created'] = $value;
				}
				if (array_key_exists('modified', $result[$key])) {
					$value = $result[$key]['modified'];
					unset($result[$key]['modified']);
					$result[$key]['modified'] = $value;
				}
				
			}

		}
		
		if ($is_assoc) {
			$result = $result[0];
		}
		
		return $result;
		
	}
	
	/**
	 * Returns Field Map
	 *
	 * @since   1.0
	 * @param   string $crud CRUD operation name (created, read, update, delete). 
	 * @return  array Field map array.
	 */
	public function returnsFieldMap($crud) {
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$model = $this->_model;
		$this->forModel();
		
		if (empty($model)) {
			return false;
		}
		
		$required_attributes = $this->_required_attributes;
		$this->withRequiredAttributes();
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		$attributes = $modelObject->attributes();
		
		if (!empty($required_attributes) && !empty($attributes)) {
			foreach ($attributes as $attribute => $settings) {
				if (!in_array($attribute, $required_attributes)) {
					unset($attributes[$attribute]);
				}
			}
		}
		
		$filtered_attributes = $this->Permissions
			->forModel($modelObject->name)
			->withAttributes($attributes)
			->on($crud)
			->allowAttributes();
		
		if (empty($filtered_attributes)) {
			return false;
		}
		
		return $modelObject->getFieldMap($filtered_attributes);
		
	}
	
	/**
	 * Returns API Response
	 *
	 * @since   1.0
	 * @return  array Returns API response array.
	 */
	public function returnsApiResponse() {
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$model = $this->_model;
		$this->forModel();
		
		if (empty($model)) {
			return false;
		}
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		$passed_conditions = $this->_passed_conditions;
		$this->withPassedConditions();
		
		$single_result = $this->_single_result;
		$this->_single_result = false;
		
		$required_attributes = $this
			->forModel($model)
			->withParentModel($parent_model)
			->hasRequiredAttributes();

		$field_map = $this
			->forModel($model)
			->withParentModel($parent_model)
			->withRequiredAttributes($required_attributes)
			->returnsFieldMap('read');
		
		$all_field_names = $modelObject->getAllFieldNames();
		
		if ($primary_model && empty($passed_conditions)) {
			$passed_conditions = $this->Query
				->withFieldMap($modelObject->getFieldMap($modelObject->attributes()))
				->rendersConditions();
		}
		
		$conditions = $this
			->withParentModel($parent_model)
			->forModel($model)
			->withFields($all_field_names)
			->withPassedConditions($passed_conditions)
			->on('read')
			->rendersConditions();
		
		if ($this->Controller->action === 'count') {
			
			$totalCount = $modelObject->find('count', array(
				'conditions' => $conditions,
				'contain' => false
			));
			
			return compact('totalCount');
			
		}
		
		$permissable_field_names = $modelObject->getFieldNames($field_map);
		$related_field_names = array();
		
		if ($primary_model) {
			
			if (!empty($single_result)) {
				$this->requiringASingleResult();
			}
			
			$required_relations = $this
				->forModel($model)
				->hasRequiredRelations();
			
			$related_field_names = $this
				->withFields($permissable_field_names)
				->withRelatedModels($required_relations)
				->hasRelatedFieldNames();
		}
		
		$fields = array_merge($permissable_field_names, $related_field_names);
		
		$options = array(
			'fields' => $fields,
			'callbackFields' => $required_attributes,
			'parentModel' => $parent_model
		);
		
		$options = $this->setPaginatorOptions($modelObject, $options);
		
		if (
			$primary_model && 
			!empty($single_result) && 
			$modelObject->hasField('deleted')
		) {
			$options['fields'][] = $modelObject->alias . '.deleted';
			$conditions[$modelObject->alias . '.deleted'] = array(0, 1);
		}
		
		$this->ApiPaginator->settings = $options;
		
		$results = $this->ApiPaginator->paginate($model, $conditions);
		
		if (
			$primary_model &&
			!empty($single_result) &&
			!empty($results[0][$modelObject->alias]['deleted'])
		) {
			$this->Controller->request->params['paging'][$modelObject->alias] = array_merge(
				$this->Controller->request->params['paging'][$modelObject->alias],
				array(
					'current' => 0,
					'count' => 0,
					'pageCount' => 0
				)
			);
			$this->wasDeleted(true);
			$results = array();
		}

		$metadata_fields = $modelObject->getMetadataFieldNames($field_map);
		
		$results = $this
			->forModel($model)
			->withParentModel($parent_model)
			->withMetadataFields($metadata_fields)
			->withFieldMap($field_map)
			->withResult($results)
			->returnsResultWithMetadata();
		
		if (
			empty($results) && 
			(
				$modelObject->isDefaultObjectEnabled() ||
				(
					!$primary_model && 
					(
						in_array($modelObject->alias, $this->Controller->{$parent_model}->getAssociated('belongsTo')) ||
						in_array($modelObject->alias, $this->Controller->{$parent_model}->getAssociated('hasOne'))
					)
				)
			)
		) {
			$default_object = $modelObject->getDefaultObject($parent_model);
			if (!empty($default_object)) {
				foreach ($default_object as $field => $value) {
					if (!in_array($modelObject->alias . '.' . $field, $fields)) {
						unset($default_object[$field]);
					}
				}
			}
			$results = array(
				0 => array(
					$model => $default_object
				)
			);
		}
		
		if ($primary_model) {
			
			$results = $this
				->forModel($model)
				->withRelatedModels($required_relations)
				->withRelatedFieldDependencies($related_field_names)
				->withResult($results)
				->returnsResultWithRelatedModelData();
			
			$results = $this
				->forModel($model)
				->withFieldMap($field_map)
				->withResult($results)
				->returnsFormattedResult();
				
		}
			
		if ($single_result && !empty($results)) {
			$results = array_pop($results);
		}
		
		return $results;
		
	}
	
	/**
	 * Returns Result With Metadata
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array	Results with metadata.
	 */
	public function returnsResultWithMetadata() {
		
		$parent_model = $this->_parent_model;
		$this->withParentModel();
		
		$primary_model = empty($parent_model) ? true : false;
		
		$model = $this->_model;
		$this->forModel();
		
		if (empty($model)) {
			return false;
		}
		
		$field_map = $this->_field_map;
		$this->withFieldMap();
		
		if ($primary_model) {
			$modelObject = $this->Controller->{$model};
		} else {
			$modelObject = $this->Controller->{$parent_model}->{$model};
		}
		
		$attributes = $modelObject->attributes();
		
		$results = $this->_result;
		$this->withResult();
		
		if (empty($results)) {
			return $results;
		}
		
		// Continue only if `Metadata` Behavior is loaded
		if (!$modelObject->Behaviors->loaded('Metadata')) {
			return $results;
		}
		
		$metadata_field_names = $this->_metadata_fields;
		$this->withMetadataFields();
		
		if (empty($metadata_field_names)) {
			return $results;
		}
		
		$metadata_count = count($metadata_field_names);

		if ($metadata_count === 1) {
			$metadata_field_name = $metadata_field_names[0];
		}

		foreach ($results as $key => $result) {

			$modelObject->id($result[$modelObject->alias][$modelObject->primaryKey]);

			if ($metadata_count === 1) {
				$metadata_field_name = $metadata_field_name;
				$meta = $modelObject->getMeta($metadata_field_name);
				$meta = array($metadata_field_name => $meta);
			} else {
				$meta = $modelObject->getMeta();
			}
			
			if (empty($meta)) {
				$meta = array();
			}

			$results[$key][$modelObject->alias]['Metadatum'] = Hash::flatten($meta);
			
			// Meta Key Intersection
			foreach (array_keys($results[$key][$modelObject->alias]['Metadatum']) as $meta_key) {
				
				// Exact Match
				if (in_array($meta_key, $metadata_field_names)) {
					continue;
				}
				
				// Partial Match
				foreach ($metadata_field_names as $meta_field_name) {
					if (substr($meta_key, 0, strlen($meta_field_name)) == $meta_field_name) {
						continue 2;
					}
				}
				
				// No Match
				unset($results[$key][$modelObject->alias]['Metadatum'][$meta_key]);
				
			}

			foreach ($metadata_field_names as $field_name) {
				
				// If `Metadatum.[field]` already set and not empty possible means 
				// already pulled by a `SpecialField` callback, in this case 
				// unset and continue to avoid duplication/unexpected results.
				if (!empty($results[$key][$modelObject->alias]['Metadatum.'. $field_name])) {
					unset($results[$key][$modelObject->alias]['Metadatum'][$field_name]);
					continue;
				}
				
				if (!array_key_exists($field_name, $results[$key][$modelObject->alias]['Metadatum'])) {
					$results[$key][$modelObject->alias]['Metadatum'][$field_name] = null;
				}
				
				// Convert Data Types if `DataTypeJuggling` Behaviors is loaded
				if (
					!empty($field_map['Metadatum.'. $field_name]) &&
					$modelObject->Behaviors->loaded('DataTypeJuggling')
				) {
					
					$value = $results[$key][$modelObject->alias]['Metadatum'][$field_name];
					
					$attribute = $field_map['Metadatum.'. $field_name];
					
					if (!empty($attributes[$attribute]['type'])) {
						
						$value = $modelObject->convertDataType($value, $attributes[$attribute]['type']);
						
					}
					
					$results[$key][$modelObject->alias]['Metadatum'][$field_name] = $value;
					
				}
				
			}

		}
		
		return $results;
		
	}
	
	/**
	 * Returns Result With Related Model Data
	 *
	 * @since   1.0
	 * @return  array Results with related model data array.
	 */
	public function returnsResultWithRelatedModelData() {
		
		$model = $this->_model;
		$this->forModel();
		
		if (empty($model)) {
			return false;
		}
		
		$results = $this->_result;
		$this->withResult();
		
		if (empty($results)) {
			return $results;
		}
		
		$related_models = $this->_related_models;
		$this->withRelatedModels();
		
		if (empty($related_models)) {
			return $results;
		}
		
		$related_field_dependencies = $this->_related_field_dependencies;
		$this->withRelatedFieldDependencies();
		
		$modelObject = $this->Controller->{$model};
		
		$cache = array();
			
		foreach ($results as $key => $result) {

			foreach ($related_models as $association => $assocData) {

				$continue = true;
				
				$conditions = $find = array();
				
				if ($assocData['type'] === 'belongsTo' && array_key_exists($assocData['foreignKey'], $result[$modelObject->alias])) {
					$conditions = array($association . '.' . $modelObject->{$association}->primaryKey => $result[$modelObject->alias][$assocData['foreignKey']]);
				} elseif (in_array($assocData['type'], array('hasOne', 'hasMany')) && array_key_exists($modelObject->primaryKey, $result[$modelObject->alias])) {
					$conditions = array($association . '.' . $assocData['foreignKey'] => $result[$modelObject->alias][$modelObject->primaryKey]);
				}
				
				if (!empty($conditions)) {
					if (!empty($assocData['conditions'])) {
						foreach ($assocData['conditions'] as $field => $value) {
							list($conditionModel, $conditionField) = explode('.', $field);
							if (
								$conditionModel === $modelObject->name &&
								$result[$conditionModel][$conditionField] != $value
							) {
								$continue = false;
							} else {
								unset($assocData['conditions'][$field]);
							}
						}
					}
					if ($continue) {
						$assocData['conditions'] = !empty($assocData['conditions']) ? $assocData['conditions'] : array();
						$conditions = array_merge($assocData['conditions'], $conditions);
						if (
							!empty($assocData['includeDeleted']) && 
							$modelObject->{$association}->hasField('deleted')
						) {
							$conditions[$modelObject->{$association}->alias . '.deleted'] = array(0, 1);
						}
						$crc32 = crc32($model . ':' . $association . ':' . serialize($conditions));
						if (array_key_exists($crc32, $cache)) {
							$find = $cache[$crc32];
						} else {
							$find = $this
								->forModel($association)
								->withParentModel($model)
								->withPassedConditions($conditions)
								->returnsApiResponse();
							$cache[$crc32] = $find;
						}
					}
				}
				
				if (empty($find) && $continue) {
					$find = array($association => null);
				} elseif (!empty($find)) {
					if ($assocData['type'] === 'hasMany') {
						$find = array(
							$association => (array) Set::extract('{n}.' . $association, $find)
						);
					} else {
						$find = $find[0];
					}
				}
				
				$results[$key] = array_merge($find, $results[$key]);

			}

		}

		if (!empty($related_field_dependencies)) {
			foreach ($related_field_dependencies as $key => $field) {
				$strlen = strlen($model . '.');
				$related_field_dependencies[$key] = substr($field, $strlen);
			}
			foreach ($results as $key => $result) {
				foreach ($related_field_dependencies as $field) {
					unset($results[$key][$model][$field]);
				}
			}
		}
		
		return $results;

	}
	
	/**
	 * Returns Filtered Data Fields
	 *
	 * @since   1.0
	 * @return  array
	 */
	public function returnsFilteredDataFields() {
		
		if (empty($this->_field_map)) {
			$this->_data_fields = array();
		}
		
		$field_exceptions = $this->_field_exceptions;
		$this->withFieldExceptions();
		
		if (empty($this->_data_fields) || empty($this->_field_map)) {
			return array();
		}
		
		$field_map = $this->_field_map;
		
		$field_prefixes = array();
		foreach ($field_map as $field => $attribute) {
			$field_prefixes[] = array_shift(explode('.', $field));
		}
		
		$filtered = array();
		
		foreach ($this->_data_fields as $field => $value) {
			if (in_array($field, $field_prefixes) || in_array($field, $field_exceptions)) {
				$filtered[$field] = $value;
			}
		}
		
		return $filtered;
		
	}
	
	/**
	 * Save Resource Data and Related Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data	Array of Resource Indexed Data
	 * @param	string	$this->_model	Primary Resource Name
	 * @param	int		$this->_id		Optional Resource ID
	 * @return	boolean 
	 */
	public function save() {
		
		$data = $this->_data;
		$this->withData();
		
		$parent_model = $this->_model;
		$this->forModel();
		
		$field_exceptions = $this->_field_exceptions;
		$this->withFieldExceptions();
		
		$id = $this->_id;
		$this->withId();
		
		$crud = !empty($id) ? 'update' : 'create';
		
		$validationIndex = $this->_validation_index;

		$validate_only = $this->validateOnly();
		
		$modelObject = $this->Controller->{$parent_model};
		
		if ($this->hasTransactions()) {
			$modelObject->begin();
		}
		
		// Model Data May be Empty
		$data[$parent_model] = !empty($data[$parent_model]) ? $data[$parent_model] : array();
		
		if ($validate_only) {
			$this->validateOnly(false);
		}
		
		$result = $this
			->withParentModel()
			->forModel($parent_model)
			->withId($id)
			->withData(array($parent_model => $data[$parent_model]))
			->withFieldExceptions($field_exceptions)
			->saveOne();
		
		if ($validate_only) {
			$this->validateOnly(true);
		}		
		
		if (empty($result)) {
			if ($this->hasTransactions()) {
				$modelObject->rollback();
			}
			return false;
		}
		
		$id = $modelObject->id;
		
		$associated = $modelObject->getAssociated();
		
		// Associated Models
		foreach ($data as $model => $fields) {
			
			// Skip Primary Model
			if ($model == $parent_model) {
				continue;
			}
			
			$associatedIndex = $validationIndex;
			$associatedIndex[] = $model;
			$this->setValidationIndex($associatedIndex);
			
			// Not in Associated Models
			if (!in_array($model, array_keys($associated))) {
				$this->setResponseCode(4007);
				$this->forModel($model)->setValidationErrors(
					array($model => 'Not related')
				);
				if ($this->hasTransactions()) {
					$modelObject->rollback();
				}
				return false;
			}
			
			if (!$modelObject->isRelationSaveable($model)) {
				$this->setResponseCode(4015);
				$this->forModel($model)->setValidationErrors(
					array($model => 'Related data not allowed')
				);
				if ($this->hasTransactions()) {
					$modelObject->rollback();
				}
				return false;
			}
			
			$result = false;
			
			if (in_array($associated[$model], array('hasOne', 'hasMany'))) {
				$this
					->forModel($model)
					->withParentModel($parent_model)
					->withId($id)
					->withData(array($model => $fields));
			}
			
			if ($associated[$model] === 'hasOne') {
				
				$result = $this
					->saveAssociatedHasOne();
				
			} elseif ($associated[$model] === 'hasMany') {
				
				$result = $this
					->saveAssociatedHasMany();
				
			}
			
			if (empty($result)) {
				if ($this->hasTransactions()) {
					$modelObject->rollback();
				}
				return false;
			}
			
		}
		
		if ($this->hasTransactions()) {
			$modelObject->commit();
		}
		
		$this->withId($id);
		
		return true;
		
	}
	
	/**
	 * Save All Indexed Resource Data and Related Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data	Array of Resource Indexed Data
	 * @param	string	$this->_model	Primary Resource Name
	 * @return	boolean 
	 */
	public function saveAll() {
		
		$data = $this->_data;
		$this->withData();
		
		$parent_model = $this->_model;
		$this->forModel();
		
		$field_exceptions = $this->_field_exceptions;
		$this->withFieldExceptions();
		
		$modelObject = $this->Controller->{$parent_model};
		
		$modelObject->begin();
		
		$errors = false;
		
		foreach ($data as $index => $indexedData) {
			
			$id = !empty($indexedData[$parent_model]['id']) ? $indexedData[$parent_model]['id'] : 0;

			$this->setValidationIndex(array($index));
			
			$result = $this
				->forModel($parent_model)
				->withId($id)
				->withData($indexedData)
				->withFieldExceptions($field_exceptions)
				->withTransactions(false)
				->save();
			
			if (empty($result)) {
				$errors = true;
			}
			
		}
		
		if (!empty($errors) && !$this->skipErrorsAndSave()) {
			$modelObject->rollback();
			return false;
		}
		
		if ($this->validateOnly()) {
			$modelObject->rollback();
		} else {
			$modelObject->commit();
		}
		
		return true;
		
	}
	
	/**
	 * Batch Update Resource Data, Filtering by Query String
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$this->_data	Array of Resource Indexed Data
	 * @param	string	$this->_model	Primary Resource Name
	 * @return	boolean 
	 */
	public function batchUpdate() {
		
		$data = $this->_data;
		$this->withData();
		
		$parent_model = $this->_model;
		$this->forModel();
		
		$passed_conditions = $this->_passed_conditions;
		$this->withPassedConditions();
		
		$field_exceptions = $this->_field_exceptions;
		$this->withFieldExceptions();
		
		// Cannot Update Multiple Models
		if (count(array_keys($data)) > 1) {
			$this->setResponseCode(4015);
			return false;
		}
		
		// Data Should Not Be Indexed
		if (Hash::numeric(array_keys($data[$parent_model]))) {
			$this->setResponseCode(4008);
			return false;
		}
		
		// Cannot Pass ID
		if (!empty($data[$parent_model]['id'])) {
			$this->setResponseCode(4001);
			return false;
		}

		// Field Map Based Off Read for Querying
		$field_map = $this
			->withParentModel()
			->forModel($parent_model)
			->returnsFieldMap('read');
		
		$modelObject = $this->Controller->{$parent_model};
		
		$fields = array();
		if (!empty($field_map)) {
			
			$fields = $this
				->forModel($parent_model)
				->withFieldMap($field_map)
				->withDataFields($data[$parent_model])
				->withFieldExceptions($field_exceptions)
				->returnsFilteredDataFields();
			
		}
		
		$field_names = $modelObject->getFieldNames($field_map);
		
		if (empty($passed_conditions)) {
			$passed_conditions = $this->Query
				->withFieldMap($field_map)
				->rendersConditions();
		}

		if (empty($passed_conditions)) {
			$this->setResponseCode(4017);
			return false;
		}
				
		$conditions = $this
			->withParentModel()
			->forModel($parent_model)
			->withFields($field_names)
			->withPassedConditions($passed_conditions)
			->on('update')
			->rendersConditions();
		
		if (empty($conditions)) {
			$this->setResponseCode(4017);
			return false;
		}
		
		// Find IDs of Records to Update
		$ids = $modelObject->find('list', array(
			'conditions' => $conditions,
			'fields' => array(
				$parent_model . '.id'
			),
			'contain' => false
		));
		
		if (empty($ids)) {
			return true;
		}
		
		// ID `0` Could Come from Special Cases like FormRecordFolder `Uncategorized`
		if (false !== $key = array_search(0, $ids)) {
			unset($ids[$key]);
		}
		
		if (empty($ids)) {
			return true;
		}
		
		$modelObject->begin();
		
		foreach ($ids as $id) {
			
			$modelObject->create(false);
			$modelObject->id = $id;
			$modelObject->set(array($parent_model => $fields));
			
			// Validation
			if (!$modelObject->validates() ||
				!is_null($this->getIndexValidationErrors())) {
				
				$validationErrors = $modelObject->validationErrors;
				
				$modelObject->rollback();
				$this->setResponseCode(4012);
				
				if (!empty($validationErrors)) {
					$this->forModel($parent_model)
						 ->withFieldMap($field_map)
						 ->setValidationErrors($validationErrors);
				}
				
				return false;
			}

			// Can Update
			if (!$this->Permissions
				->forModel($parent_model)
				->canUpdate($id, $fields)) {
				$this->setResponseCode(4013);
				return false;
			}
			
			$fieldList = array_keys($field_map);	
			if (!empty($field_exceptions)) {
				$fieldList = array_merge($fieldList, $field_exceptions);
			}
		
			// Save Data
			if (!$modelObject->save(array(
					$data
				),
				array(
					'fieldList' => $fieldList,
					'validate' => false
				)
			)) {
				$modelObject->rollback();
				$this->setResponseCode(5001);
				return false;
			}
			
		}
		
		$modelObject->commit();
		
		$this->Controller->request->params['paging'][$parent_model]['count'] = count($ids);
		
		return true;
				
	}
	
	/**
	 * Delete Resource Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	boolean 
	 */
	public function delete() {
		
		if (empty($this->_model)) {
			$this->setResponseCode(5002);
			return false;
		}
		
		if (empty($this->_id)) {
			$this->setResponseCode(4000);
			return false;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		$id = $this->_id;
		$this->withId();
		
		$modelObject = $this->Controller->{$model};
		
		$modelObject->id = $id;
		
		if (!$modelObject->exists()) {
			$this->setResponseCode(4004);
			return false;
		}
		
		// Can Delete
		if (!$this->Permissions->withParentModel()->forModel($model)->canDelete($id)) {
			$this->setResponseCode(4013);
			return false;
		}
		
		// Delete
		$modelObject->id = $id;
		$result = $modelObject->delete();
		
		if (empty($result)) {
			$this->setResponseCode(5002);
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Batch Delete
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	boolean 
	 */
	public function batchDelete() {
		
		if (empty($this->_model)) {
			$this->setResponseCode(5002);
			return false;
		}
		
		$parent_model = $this->_model;
		$this->forModel();
		
		$passed_conditions = $this->_passed_conditions;
		$this->withPassedConditions();

		// Field Map Based Off Read for Querying
		$field_map = $this
			->withParentModel()
			->forModel($parent_model)
			->returnsFieldMap('read');
		
		$modelObject = $this->Controller->{$parent_model};
		
		$field_names = $modelObject->getFieldNames($field_map);
		
		if (empty($passed_conditions)) {
			$passed_conditions = $this->Query
				->withFieldMap($field_map)
				->rendersConditions();
		}

		if (empty($passed_conditions)) {
			$this->setResponseCode(4017);
			return false;
		}
		
		$conditions = $this
			->withParentModel()
			->forModel($parent_model)
			->withFields($field_names)
			->withPassedConditions($passed_conditions)
			->on('delete')
			->rendersConditions();
		
		if (empty($conditions)) {
			$this->setResponseCode(4017);
			return false;
		}
		
		// Find IDs of Records to Delete
		$ids = $modelObject->find('list', array(
			'conditions' => $conditions,
			'fields' => array(
				$parent_model . '.id'
			),
			'contain' => false
		));
		
		if (empty($ids)) {
			return true;
		}
		
		// ID `0` Could Come from Special Cases like FormRecordFolder `Uncategorized`
		if (false !== $key = array_search(0, $ids)) {
			unset($ids[$key]);
		}
		
		if (empty($ids)) {
			return true;
		}
		
		$modelObject->begin();
		
		foreach ($ids as $id) {

			// Delete
			$modelObject->id = $id;
			$result = $modelObject->delete();
			
			if (empty($result)) {
				$this->setResponseCode(5002);
				return false;
			}
			
		}
		
		$modelObject->commit();
		
		$this->Controller->request->params['paging'][$parent_model]['count'] = count($ids);
		
		return true;
		
	}
	
	/**
	 * Set Paginator Options
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @param   object $modelObject
	 * @param   array  $options
	 * @return  array 
	 */
	public function setPaginatorOptions($modelObject = null, $options = array()) {
		
		$defaults = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
		);
		
		$options = array_merge($defaults, $options);
		
		if (!is_object($modelObject)) {
			return $options;
		}
		
		if ($this->ApiRequestHandler->responseType() === 'csv') {
			
			$options['limit'] = $options['maxLimit'] = PHP_INT_MAX;
		
		} elseif (!empty($options['parentModel'])) {
			
			if (!isset($this->Controller->{$options['parentModel']})) {
				$parentModelObject = ClassRegistry::init($options['parentModel']);
			} else {
				$parentModelObject = $this->Controller->{$options['parentModel']};
			}
			
			if (!empty($parentModelObject->hasMany[$modelObject->alias]['limit'])) {
				$options['limit'] = $parentModelObject->hasMany[$modelObject->alias]['limit'];
			}
		
		} elseif (empty($options['parentModel']) && !empty($modelObject->limit)) {
			
			$options['limit'] = $modelObject->limit;
		
		}
		
		if (!empty($modelObject->maxLimit)) {
			$options['maxLimit'] = $modelObject->maxLimit;
		}
		
		if (!empty($options['limit']) && $options['limit'] === 'all') {
			$options['limit'] = $options['maxLimit'] = PHP_INT_MAX;
		}
		
		return $options;
		
	}
	
	/**
	 * Was delete
	 *
	 * @since   1.0
	 * @param   boolean	$was_deleted
	 * @return  boolean
	 */
	public function wasDeleted($was_deleted = null) {
		
		if (!is_null($was_deleted)) {
			$this->_was_deleted = $was_deleted;
		}
		
		$return = $this->_was_deleted;
		
		if (is_null($was_deleted)) {
			$this->_was_deleted = null;
		}
		
		return $return;
		
	}
	
}
