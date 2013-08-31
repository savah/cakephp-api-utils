<?php
App::uses('Component', 'Controller');
App::uses('CakeTime', 'Utility');

/**
 * Api Input Data Component
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
class ApiInputDataComponent extends Component {
	
	/**
	 * Stores Temporary Settings About Saveall Keys 
	 * 
	 * @since   1.0
	 * @var     array
	 */
	protected $_save_all_keys = array();
	
	/**
	 * Attributes
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_attributes = array();
	
	/**
	 * Timezone
	 *
	 * @since   1.0
	 * @var     mixed
	 */
	protected $_timezone = array();
	
	/**
	 * Model
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_model = null;
	
	/**
	 * Model object
	 *
	 * @since   1.0
	 * @var     Model
	 */
	protected $_ModelObject = null;
	
	/**
	 * Related
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_related = array();
	
	/**
	 * Primary associations
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_primary_associations = null;
	
	/**
	 * Components
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $components = array(
		'Query' => array(
			'className' => 'Api.ApiQuery'
		),
		'Resource' => array(
			'className' => 'Api.ApiResource'
		)
	);
	
	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @param   ComponentCollection $collection Collection object.
	 * @param   array $settings Component settings.
	 * @return  void
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		
		parent::__construct($collection, $settings);

		$this->Controller = $collection->getController();
		
	}
	
	/**
	 * For Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	string	$model	
	 * @return  object
	 */
	public function forModel($model = null) {
		
		$this->_model = $model;
		
		return $this;
		
	}
	
	/**
	 * Is Save All
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return  boolean
	 */
	public function isSaveAll($data = array()) {
		
		if (empty($data)) {
			$data = $this->Controller->request->data;
		}
		
		return Hash::numeric(array_keys($data));
		
	}
	
	/**
	 * Recursively fix POST and PUT underscores
	 *
	 * This method is meant to private in nature (only used by `fixPostAndPutUnderscore`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data	
	 * @return  array
	 */
	public function __fixPostAndPutUnderscoresRecursive($data = array()) {
		
		if (empty($data)) {
			return $data;
		}
		
		foreach ($data as $key => $value) {
			if (strpos($key, '_') !== false) {
				unset($data[$key]);
				$key = str_replace('_', '.', $key);
				$key = str_replace('tmp.name', 'tmp_name', $key); // for file uploads
				$data[$key] = $value;
			}
			if (is_array($data[$key])) {
				$data[$key] = $this->__fixPostAndPutUnderscoresRecursive($data[$key]);
			}
		}
		
		return $data;
		
	}
	
	/**
	 * Fix POST and PUT underscores
	 *
	 * PHP converts dots in POST and PUT keys to underscores.
	 * This method corrects that behavior.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function fixPostAndPutUnderscores() {
		
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		$this->Controller->request->data = $this->__fixPostAndPutUnderscoresRecursive($this->Controller->request->data);
		
		return $this;
		
	}
	
	/**
	 * Convert Datetime String from Iso8601 to Sql Format
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array
	 */
	public function convertIso8601ToSqlDatetime($data = array()) {
		
		foreach ($data as $attribute => $value) {

			if (
				empty($this->_attributes['original'][$attribute]) ||
				is_array($value) || 
				empty($this->_attributes['original'][$attribute]['type']) ||
				$this->_attributes['original'][$attribute]['type'] !== 'datetime'
			) {
				continue;
			}

			$field = $this->_attributes['original'][$attribute];

			$data[$attribute] = CakeTime::toServer($value, $this->_timezone);

		}
		
		return $data;
		
	}
	
	/**
	 * Convert Boolean Literals to `0` or `1`
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array 
	 */
	public function convertBooleanLiterals($data = array()) {
		
		foreach ($data as $attribute => $value) {

			if (
				empty($this->_attributes['original'][$attribute]) ||
				empty($this->_attributes['original'][$attribute]['type']) ||
				$this->_attributes['original'][$attribute]['type'] !== 'boolean'
			) {
				continue;
			}
			
			if (
				$value === 'true' ||
				$value === true
			) {
				$value = 1;
			} elseif(
				$value === 'false' ||
				$value === false
			) {
				$value = 0;
			}
			
			$data[$attribute] = $value;

		}
		
		return $data;
		
	}
	
	/**
	 * Convert String 'null' to null datatype
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array
	 */
	public function convertStringNulls($data = array()) {
		
		foreach ($data as $attribute => $value) {

			if ($value === 'null') {
				$data[$attribute] = null;
			}

		}
		
		return $data;
		
	}
	
	/**
	 * Integrate Route Parent - Parent Hasmany and Primary Belongsto
	 *
	 * Scenario where the parent hasMany of the primary model, 
	 * and the primary model belongsTo the parent
	 *
	 * Used within this component by `integrateRouteParent()`.
	 *
	 * 
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @param	array	$options	
	 *					`primaryModelName`					string
	 *					`primaryModelAlias`					string
	 *					`primary_model_primary_key_name`	string
	 *					`primary_model_id`					integer
	 *					`parentModelAlias`					string
	 *					`parentModelName`					string
	 *					`parent_model_primary_key_name`		string
	 *					`parent_model_id`					integer
	 *					`foreign_key_field`					string
	 *					`foreign_key_attribute				string
	 *					`foreign_conditions`				array
	 * @return  array
	 */
	public function integrateRouteParentHasManyIntoPrimaryBelongsTo($data = array(), $options = array()) {
		
		$options = array_merge(
			array(
				'primaryModelName' => null,
				'primary_model_primary_key_name' => null,
				'parentModelAlias' => null,
				'parentModelName' => null,
				'parent_model_primary_key_name' => null,
				'parent_model_id' => null,
				'foreign_key_field' => null,
				'foreign_key_attribute' => null,
				'foreign_conditions' => array()
			),
			$options
		);
		extract($options);
		
		$data[$primaryModelName][0] = $this->setPolymorphicRelatedModel($data[$primaryModelName][0], $parentModelName);
		$data[$primaryModelName][0][$foreign_key_attribute] = $parent_model_id;
		
		return $data;
		
	}
	
	/**
	 * Integrate Route Parent - Parent Hasone and Primary Belongsto
	 *
	 * Scenario where the parent hasOne of the primary model, 
	 * and the primary model belongsTo the parent
	 *
	 * Used within this component by `integrateRouteParent()`.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @param	array	$options	
	 *					`primaryModelName`					string
	 *					`primaryModelAlias`					string
	 *					`primary_model_primary_key_name`	string
	 *					`primary_model_id`					integer
	 *					`parentModelAlias`					string
	 *					`parentModelName`					string
	 *					`parent_model_primary_key_name`		string
	 *					`parent_model_id`					integer
	 *					`foreign_key_field`					string
	 *					`foreign_key_attribute				string
	 *					`foreign_conditions`				array
	 * @return  array
	 */
	public function integrateRouteParentHasOneIntoPrimaryBelongsTo($data = array(), $options = array()) {
		
		$data = $this->integrateRouteParentHasManyIntoPrimaryBelongsTo($data, $options); // works the same way...
		
		// ... but also needs to integrate in existing ID, if it exists
		
		$options = array_merge(
			array(
				'primaryModelName' => null,
				'primary_model_primary_key_name' => null,
				'parentModelAlias' => null,
				'parentModelName' => null,
				'parent_model_primary_key_name' => null,
				'parent_model_id' => null,
				'foreign_key_field' => null,
				'foreign_key_attribute' => null,
				'foreign_conditions' => array()
			),
			$options
		);
		extract($options);
	
		$primary_model_record = $this->{$primaryModelName}->find('first', array(
			'fields' => array($primaryModelName . '.' . $primary_model_primary_key_name),
			'conditions' => array_merge(
				array($primaryModelName . '.' . $foreign_key_field => $parent_model_id),
				$foreign_conditions
			)
		));
		
		if (!empty($primary_model_record)) {
			$data[$primaryModelName][0][$primary_model_primary_key_name] = $primary_model_record[$primaryModelName][$primary_model_primary_key_name];
		}
		
		return $data;
		
	}
	
	/**
	 * Integrate Route Parent - Parent Belongsto and Primary Hasmany
	 *
	 * Scenario where the parent belongsTo the primary model, 
	 * and the primary model hasMany of the parent
	 *
	 * Used within this component by `integrateRouteParent()`.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @param	array	$options	
	 *					`primaryModelName`					string
	 *					`primaryModelAlias`					string
	 *					`primary_model_primary_key_name`	string
	 *					`primary_model_id`					integer
	 *					`parentModelAlias`					string
	 *					`parentModelName`					string
	 *					`parent_model_primary_key_name`		string
	 *					`parent_model_id`					integer
	 *					`foreign_key_field`					string
	 *					`foreign_key_attribute				string
	 *					`foreign_conditions`				array
	 * @return  array
	 */
	public function integrateRouteParentBelongsToIntoPrimaryHasMany($data = array(), $options = array()) {
		
		$options = array_merge(
			array(
				'primaryModelName' => null,
				'primary_model_primary_key_name' => null,
				'parentModelAlias' => null,
				'parentModelName' => null,
				'parent_model_primary_key_name' => null,
				'parent_model_id' => null,
				'foreign_key_field' => null,
				'foreign_key_attribute' => null,
				'foreign_conditions' => array()
			),
			$options
		);
		extract($options);
		
		// get the 
		$foreign_record = $this->{$primaryModelName}->{$parentModelAlias}->find('first', array(
			'fields' => array($parentModelAlias . '.' . $foreign_key_field),
			'conditions' => array_merge(
				array($parentModelAlias . '.' . $parent_model_primary_key_name => $parent_model_id),
				$foreign_conditions
			)
		));
		
		unset($data[$parentModelAlias]);
		
		if (!empty($foreign_record[$parentModelAlias][$foreign_key_field])) {
			$data[$primaryModelName][0][$primary_model_primary_key_name] = $foreign_record[$parentModelAlias][$foreign_key_field];
		} else {
			$data[$parentModelAlias] = array(0 => array($parent_model_primary_key_name => $parent_model_id));
		}
		
		return $data;
		
	}
	
	/**
	 * Integrate route parent - parent belongsTo and primary hasOne
	 *
	 * Scenario where the parent belongsTo the primary model, 
	 * and the primary model hasOne of the parent
	 * 
	 * Used within this component by `integrateRouteParent()`.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @param	array	$options	
	 *					`primaryModelName`					string
	 *					`primaryModelAlias`					string
	 *					`primary_model_primary_key_name`	string
	 *					`primary_model_id`					integer
	 *					`parentModelAlias`					string
	 *					`parentModelName`					string
	 *					`parent_model_primary_key_name`		string
	 *					`parent_model_id`					integer
	 *					`foreign_key_field`					string
	 *					`foreign_key_attribute				string
	 *					`foreign_conditions`				array
	 * @return  array
	 */
	public function integrateRouteParentBelongsToIntoPrimaryHasOne($data = array(), $options = array()) {
		
		// works the same way...
		return $this->integrateRouteParentBelongsToIntoPrimaryHasMany($data, $options);
		
	}
	
	/**
	 * Integrate Parent Model if This Is a Subresource
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function integrateRouteParent() {
		
		$model = $this->_model;
		$this->forModel();
		
		if (empty($model)) {
			return $this;
		}
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		extract($this->Query->getParent());
		
		if (
			empty($parentModelAlias) || 
			empty($parent_model_id) || 
			!method_exists($this->{$model}, 'getAssociated') ||
			!isset($this->{$model}->{$parentModelAlias}) ||
			!method_exists($this->{$model}->{$parentModelAlias}, 'getAssociated') ||
			empty($this->Controller->request->params['modelAlias']) ||
			empty($this->Controller->request->data) || 
			!is_array($this->Controller->request->data)
		) {
			return $this;
		}
		
		if (is_null($this->_primary_associations)) {
			$this->_primary_associations = $this->{$model}->getAssociated();
		}
		
		if (!array_key_exists($parentModelAlias, $this->_primary_associations)) {
			return $this;
		}
		
		$primary_type = $this->_primary_associations[$parentModelAlias];
		
		$parent_associated = $this->{$model}->{$parentModelAlias}->getAssociated();
		
		$parentModelName = $this->{$model}->{$parentModelAlias}->name;
		
		if (!array_key_exists($this->Controller->request->params['modelAlias'], $parent_associated)) {
			return $this;
		}
		
		$parent_type = $parent_associated[$this->Controller->request->params['modelAlias']];
		
		$methodName = 'integrateRouteParent' . ucfirst($parent_type) . 'IntoPrimary' . ucfirst($primary_type);
		
		foreach ($this->Controller->request->data as $key => $data) {
			
			// TODO: What about `conditions` scenarios?
			
			if ($primary_type === 'belongsTo') {
				
				$field_map = $this->{$model}->getFieldMap($this->{$model}->attributes());
				$foreign_key = $this->{$model}->{$primary_type}[$parentModelAlias]['foreignKey'];
				$foreign_conditions = $this->{$model}->{$primary_type}[$parentModelAlias]['conditions'];
				
			} else {
				
				$field_map = $this->{$model}->{$parentModelAlias}->getFieldMap($this->{$model}->{$parentModelAlias}->attributes());
				$foreign_key = $this->{$model}->{$parentModelAlias}->{$parent_type}[$this->Controller->request->params['modelAlias']]['foreignKey'];
				$foreign_conditions = $this->{$model}->{$parentModelAlias}->{$parent_type}[$this->Controller->request->params['modelAlias']]['conditions'];
				
			}
			
			$this->Controller->request->data[$key] = $this->{$methodName}(
				$data,
				array(
					'primaryModelName' => $model,
					'primary_model_primary_key_name' => $this->{$model}->primaryKey,
					'parentModelAlias' => $parentModelAlias,
					'parentModelName' => $parentModelName,
					'parent_model_primary_key_name' => $this->{$model}->{$parentModelAlias}->primaryKey,
					'parent_model_id' => $parent_model_id,
					'foreign_key_field' => $foreign_key,
					'foreign_key_attribute' => $field_map[$foreign_key],
					'foreign_conditions' => !empty($foreign_conditions) ? $foreign_conditions : array()
				)
			);
			
		}
		
		return $this;
		
	}
	
	/**
	 * Normalize Response Data to Cake Data Structure
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 *						id
	 *						name
	 *						Profile
	 *							fname
	 * @return	array	$data
	 *						User
	 *							id
	 *							name
	 *						Profile
	 *							fname
	 */
	public function normalize() {
  	
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		$this->Controller->request->data = Hash::expand($this->Controller->request->data);
		
		$saveAll = $this->isSaveAll();
		
		if (!$saveAll) {
			$this->Controller->request->data = array($this->Controller->request->data);
		}
		
		foreach ($this->Controller->request->data as $key => $data) {
			
			$this->Controller->request->data[$key] = $this->normalizeModel($model, $data);
			
		}
		
		if (!$saveAll) {
			$this->Controller->request->data = array_shift($this->Controller->request->data);
		}
		
		return $this;
		
	}
	
	/**
	 * Normalize Data for a Single Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	string	$model	
	 * @param	array	$data
	 * @return  array
	 */
	public function normalizeModel($model = null, $data = array()) {
		
		if (is_null($this->_primary_associations)) {
		
			if (
				empty($this->{$model}) ||
				!is_object($this->{$model})
			) {
				$this->{$model} = ClassRegistry::init($model);
			}

			$this->_primary_associations = $this->{$model}->getAssociated();
		
		}
		
		$associated = array();
		if (!empty($this->_primary_associations)) {
			foreach ($this->_primary_associations as $association => $type) {
				if ($type !== 'belongsTo') {
					$associated[] = lcfirst($association);
				}
			}
		}
		
		$relation_data = array();
		
		foreach ($data as $key => $value) {
			
			if (in_array($key, $associated) && is_array($value)) {
				$relation_data[ucfirst($key)] = $value;
				unset($data[$key]);
			}
			
		}
		
		$primary_data = $data;
		
		$return = array($model => $primary_data);
		
		if (!empty($relation_data)) {
			foreach ($relation_data as $model => $data) {
				$return[$model] = $data;
			}
		}
		
		return $return;
		
	}
	
	/**
	 * Set polymorphic related model to parent model on associated data if no value is already set
	 * 
	 * @author	Paul Smith <paul@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array
	 */
	public function setPolymorphicRelatedModel($data = array(), $modelName) {
		
		if (empty($modelName)) {
			return $data;
		}
		
		foreach ($this->_attributes['original'] as $fieldName => $fieldOptions) {
			if (
				!empty($fieldOptions['polymorphic_model']) && 
				(
					empty($fieldOptions['polymorphic_exclusions']) ||
					!in_array($modelName, $fieldOptions['polymorphic_exclusions'])
				)
			) {
				$data[$fieldName] = $modelName;
			}
		}
		
		return $data;
		
	}
	
	/**
	 * Convert Attribute Options
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array
	 */
	public function convertAttributeOptions($data = array()) {

		foreach ($data as $attribute => $value) {

			if (empty($this->_attributes['original'][$attribute]) || is_array($value)) {
				continue;
			}

			if (!empty($this->_attributes['original'][$attribute]['values']['options'])) {
				$attribute_options = $this->_ModelObject->getOptions($this->_attributes['original'][$attribute]['values']['options']);
			} elseif (!empty($this->_attributes['original'][$attribute]['field'])) {
				$attribute_options = $this->_ModelObject->getOptions($this->_attributes['original'][$attribute]['field']);
			}

			if (empty($attribute_options)) {
				continue;
			}

			if (in_array($value, $attribute_options)) {
				$data[$attribute] = array_search($value, $attribute_options);
			}

		}
		
		return $data;
		
	}
	
	/**
	 * Convert all to saveAll
	 * 
	 * Temporarily converts all data to saveAll format for easier processing
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	array
	 */
	public function convertAllToSaveAll() {
		
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		if ($this->isSaveAll($this->Controller->request->data)) {
			$this->_save_all_keys['_main'] = true;
		} else {
			$this->_save_all_keys['_main'] = false;
			$this->Controller->request->data = array($this->Controller->request->data);
		}
		
		foreach ($this->Controller->request->data as $key => $group) {
		
			$models = array_keys($group);

			foreach ($models as $current_model) {

				if ($this->isSaveAll($group[$current_model])) {
					$this->_save_all_keys[$current_model][$key] = true;
				} else {
					$this->_save_all_keys[$current_model][$key] = false;
					$this->Controller->request->data[$key][$current_model] = array($group[$current_model]);
				}

			}
		
		}
		
		return $this;
		
	}
	
	/**
	 * Convert singles back to normal
	 * 
	 * Takes temporary saveAll formats and converts them back to single format
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	array
	 */
	public function convertSinglesBackToNormal() {
		
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		foreach ($this->Controller->request->data as $key => $group) {
		
			$models = array_keys($group);

			foreach ($models as $current_model) {

				if (empty($this->_save_all_keys[$current_model][$key])) {
					$this->Controller->request->data[$key][$current_model] = array_shift($group[$current_model]);
				}

			}
		
		}
		
		if (empty($this->_save_all_keys['_main'])) {
			$this->Controller->request->data = array_shift($this->Controller->request->data);
		}
		
		$this->_save_all_keys = array();
		
		return $this;
		
	}
	
	/**
	 * Normalize attributes
	 * 
	 * Creates a hybrid flattened/expanded array. Attributes become flattened, while
	 * the values underneath them (whether strings or arrays) are fully expanded.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$data
	 * @return	array
	 */
	public function normalizeAttributes($data = array()) {

		$flattened = Hash::flatten($data);

		$filtered = array();

		foreach ($this->_attributes['sorted'] as $attribute) {
			$strlen = strlen($attribute);
			foreach ($flattened as $flattened_key => $value) {
				$flattened_key_strlen = strlen($flattened_key);
				$starts_with_attribute = substr($flattened_key, 0, $strlen) === $attribute;
				if ($starts_with_attribute) {
					$suffix = '';
					if ($flattened_key_strlen > $strlen) {
						$suffix = substr($flattened_key, $strlen);
					}
					$filtered[$attribute . '___' . $suffix] = $value;
					unset($flattened[$flattened_key]);
				}
			}
		}

		$temp = array();
		
		foreach ($filtered as $filtered_key => $value) {
			
			list($prefix, $suffix) = explode('___', $filtered_key);
			
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

		$attribute_order = array_intersect_key(array_flip($this->_attributes['unsorted']), $temp);

		$return = array_merge($attribute_order, $temp);
		
		return $return;
		
	}
	
	/**
	 * Convert attributes to fields
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function convertAttributesToFields() {
		
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		foreach ($this->Controller->request->data as $key => $model_group) {
			
			foreach ($model_group as $current_model => $save_all_data) {
				
				if (empty($this->{$model}->{$current_model})|| !is_object($this->{$model}->{$current_model})) {
					$modelObject = $this->{$model};
				} else {
					$modelObject = $this->{$model}->{$current_model};
				}
				
				$field_map = $modelObject->getFieldMap($modelObject->attributes());
				
				foreach ($save_all_data as $save_all_key => $data) {
		
					$new_data = array();
					
					foreach ($field_map as $field => $attribute) {
						
						if (array_key_exists($attribute, $data)) {
							$temp = Hash::expand(array($field => $data[$attribute]));
							$new_data = Hash::merge(
								$new_data,
								$temp
							);
						}

					}
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = $new_data;
				
				}
				
			}
			
		}
		
		return $this;
		
	}
	
	/**
	 * Denormalized Fields
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function denormalizedFields() {
		
		if (
			empty($this->Controller->request->data) ||
			!is_array($this->Controller->request->data)
		) {
			return $this;
		}
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$denormalized_field_names = array();
		
		foreach ($this->Controller->request->data as $key => $model_group) {
			
			foreach ($model_group as $current_model => $save_all_data) {
				
				if (
					empty($this->{$model}->{$current_model}) ||
					!is_object($this->{$model}->{$current_model})
				) {
					$modelObject = $this->{$model};
				} else {
					$modelObject = $this->{$model}->{$current_model};
				}
				
				foreach ($save_all_data as $save_all_key => $data) {
						
					$denormalized_fields = $modelObject->getDenormalizedFields($data);
					
					if (is_array($denormalized_fields)) {
						$denormalized_field_names = array_merge(
							$denormalized_field_names,
							array_keys($denormalized_fields)
						);
					}
					
					$new_data = Hash::merge($data, $denormalized_fields);
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = $new_data;
				
				}
				
			}
			
		}
		
		if (!empty($denormalized_field_names)) {
			$this->Resource->withFieldExceptions($denormalized_field_names);
		}
		
		return $this;
		
	}
	
	/**
	 * Add relation to stateful list
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	string	$relation
	 * @return  void
	 */
	public function addRelated($relation = null) {
		
		if (!empty($relation) && !in_array($relation, $this->_related)) {
			$this->_related[] = $relation;
		}
		
	}
	
	/**
	 * Get or set list of saved relations
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$related
	 * @return  array
	 */
	public function related($related = null) {
		
		if (!is_null($related)) {
			$this->_related = $related;
		}
		
		return $this->_related;
		
	}
	
	/**
	 * Prepare normalized data loops
	 *
	 * Loop through each iteration of data and apply various normalization filters to it
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  object
	 */
	public function prepareNormalizedDataLoops() {
		
		if (empty($this->Controller->request->data) || !is_array($this->Controller->request->data)) {
			return $this;
		}
		
		if (empty($this->_model)) {
			return $this;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		if (!isset($this->{$model})) {
			$this->{$model} = ClassRegistry::init($model);
		}
		
		$this->_timezone = $this->Query->getTimezone();
		
		foreach ($this->Controller->request->data as $key => $model_group) {
			
			foreach ($model_group as $current_model => $save_all_data) {
				
				if (empty($this->{$model}->{$current_model})|| !is_object($this->{$model}->{$current_model})) {
					$this->_ModelObject = $this->{$model};
					$relatedModel = false;
				} else {
					$this->_ModelObject = $this->{$model}->{$current_model};
					$this->addRelated($current_model);
					$relatedModel = true;
				}
				
				$this->_attributes = array('original' => $this->_ModelObject->attributes());

				if (empty($this->_attributes['original'])) {
					continue;
				}
				
				$this->_attributes['sorted'] = $this->_attributes['unsorted'] = array_keys($this->_attributes['original']);
				
				usort($this->_attributes['sorted'], function($first, $second){
					return strlen($second) - strlen($first);
				});
				
				foreach ($save_all_data as $save_all_key => $data) {
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = 
						$this->forModel($model)
							->normalizeAttributes($this->Controller->request->data[$key][$current_model][$save_all_key]);
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = 
						$this->forModel($model)
							->convertAttributeOptions($this->Controller->request->data[$key][$current_model][$save_all_key]);
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = 
						$this->forModel($model)
							->convertIso8601ToSqlDatetime($this->Controller->request->data[$key][$current_model][$save_all_key]);
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = 
						$this->forModel($model)
							->convertBooleanLiterals($this->Controller->request->data[$key][$current_model][$save_all_key]);
					
					$this->Controller->request->data[$key][$current_model][$save_all_key] = 
						$this->forModel($model)
							->convertStringNulls($this->Controller->request->data[$key][$current_model][$save_all_key]);
					
					if ($relatedModel) {
						$this->Controller->request->data[$key][$current_model][$save_all_key] = 
							$this->forModel($model)
								->setPolymorphicRelatedModel($this->Controller->request->data[$key][$current_model][$save_all_key], $model);
					}
				
				}
				
			}
			
		}
		
		return $this;
		
	}
	
	/**
	 * Prepare Input Data
	 *
	 * Used in `ApiComponent::startup()`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  array
	 */
	public function prepare() {
		
		if (empty($this->_model)) {
			return $this->Controller->request->data;
		}
		
		$model = $this->_model;
		$this->forModel();
		
		$this
			->fixPostAndPutUnderscores()
			->forModel($model)
				->normalize()
			->convertAllToSaveAll()
			->forModel($model)
				->prepareNormalizedDataLoops()
			->forModel($model)
				->integrateRouteParent()
			->forModel($model)
				->convertAttributesToFields()
			->forModel($model)
				->denormalizedFields()
			->convertSinglesBackToNormal();
		
		return $this->Controller->request->data;
		
	}
	
}
