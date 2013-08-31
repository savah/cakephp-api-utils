<?php
App::uses('ModelBehavior', 'Model');

/**
 * Api Behavior
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
class ApiBehavior extends ModelBehavior {

	/**
	 * Setup Callback
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	array $settings
	 * @return	void
	 */
	public function setup(Model $Model, $settings = array()) {
		
		if (!isset($Model->_user_id)) {
			$Model->_user_id = null;
		}
		
		if (!isset($Model->_user_role)) {
			$Model->_user_role = null;
		}
		
		if (!isset($Model->_attributes)) {
			$Model->_attributes = array();
		}
		
		if (!isset($Model->defaultObject)) {
			$Model->defaultObject = false;
		}
		
	}
	
	/**
	 * Is relation saveable or findeable?
	 *
	 * Checks to see if relation can be saved or fetched along with primary model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string	$relation	Model alias of relation
	 * @param	string	$operation	`saveable` or `findable`
	 * @return	boolean
	 */
	private function __isRelationSaveableOrFindable(Model $Model, $relation = null, $operation = 'saveable') {
		
		$associations = $Model->getAssociated();
		
		if (empty($associations[$relation])) {
			return false;
		}
		
		$settings = $Model->{$associations[$relation]}[$relation];
		
		if (
			!array_key_exists($operation, $settings) ||
			!empty($settings[$operation])
		) {
			return true;
		}
		
		return false;
		
	}
	
	/**
	 * Set/Get Model ID
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	mixed $id Id to set 
	 * @return	object
	 */
	public function id(Model $Model, $id = 0) {
		
		if (empty($id)) {
			return $Model;
		}
		
		$Model->id = $id;
		
		return $Model;
		
	}
	
	/**
	 * Set/Get User ID
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	integer $user_id
	 * @return	mixed
	 */
	public function userId(Model $Model, $user_id = null) {
		
		$stored_user_id = Configure::read('_user_id');
		
		$stateful_user_id = $Model->_user_id;
		
		if (empty($stored_user_id)) {
			$stored_user_id = null;
		}
		
		if (empty($stateful_user_id)) {
			$stateful_user_id = null;
		}
		
		if (is_null($user_id)) {
			
			if (!empty($stateful_user_id)) {
				return $stateful_user_id;
			} else {
				return $stored_user_id;
			}
			
		}
		
		Configure::write('_user_id', $user_id);
		
		$Model->_user_id = $user_id;
		
		return $Model;
		
	}
	
	/**
	 * Set/Get User's Role
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	integer $user_role
	 * @return	mixed
	 */
	public function userRole(Model $Model, $user_role = null) {
		
		$stored_user_role = Configure::read('_user_role');
		
		$stateful_user_role = $Model->_user_role;
		
		if (empty($stored_user_role)) {
			$stored_user_role = null;
		}
		
		if (empty($stateful_user_role)) {
			$stateful_user_role = null;
		}
		
		if (is_null($user_role)) {
			
			if (!empty($stateful_user_role)) {
				return $stateful_user_role;
			} else {
				return $stored_user_role;
			}
			
		}
		
		Configure::write('_user_role', $user_role);
		
		$this->_user_role = $user_role;
		
		return $Model;
		
	}
	
	/**
	 * Attributes
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	array $attributes Attributes array. 
	 * @return	array
	 */
	public function attributes(Model $Model, $attributes = null) {
		
		if (!is_null($attributes)) {
			$Model->_attributes = $attributes;
		}
		
		$attributes = Hash::normalize($Model->_attributes);
		
		if (!empty($attributes)) {
			foreach ($attributes as $attribute => $settings) {
				if (empty($settings['field'])) {
					$attributes[$attribute]['field'] = $settings['field'] = $attribute;
				}
				if (empty($settings['type']) && !empty($settings['field'])) {
					if (empty($schema)) {
						$schema = $Model->schema();
					}
					if (!empty($schema[$settings['field']]['type'])) {
						$type = $schema[$settings['field']]['type'];
						if ($type === 'integer') {
							$type = 'int';
						}
						$attributes[$attribute]['type'] = $type;
					}
				}
			}
		}
		
		return $attributes;
		
	}
	
	/**
	 * Has Unique ID
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string $model Model name. 
	 * @return	boolean
	 */
	public function hasUniqueID(Model $Model, $model = '') {
	
		if (empty($Model->uniqueID) || empty($Model->uniqueID[$model])) {
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * Is default object enabled?
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @return	boolean
	 */
	public function isDefaultObjectEnabled(Model $Model) {
		
		return $Model->defaultObject;
		
	}
	
	/**
	 * Is relation saveable?
	 *
	 * Checks to see if relation can be saved along with primary model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string	$relation	Model alias of relation
	 * @return	boolean
	 */
	public function isRelationSaveable(Model $Model, $relation = null) {
		
		return $this->__isRelationSaveableOrFindable($Model, $relation, 'saveable');
		
	}
	
	/**
	 * Is relation findable?
	 *
	 * Checks to see if relation can be fetched along with primary model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string	$relation	Model alias of relation
	 * @return	boolean
	 */
	public function isRelationFindable(Model $Model, $relation = null) {
		
		return $this->__isRelationSaveableOrFindable($Model, $relation, 'findable');
		
	}
	
	/**
	 * Get default object
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string	$parent_model
	 * @return	array
	 */
	public function getDefaultObject(Model $Model, $parent_model = null) {
		
		$schema = $Model->schema();
		
		$object = array();
		
		foreach ($schema as $field => $settings) {
			$object[$field] = $settings['default'];
		}
		
		return $object;
		
	}
	
	/**
	 * Get Unique Conditions
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	string $model Model name.
	 * @param	integer $foreign_id Foreign Id.
	 * @param	array $data Data array. 
	 * @return	array
	 */
	public function getUniqueConditions(Model $Model, $model = null, $foreign_id = null, $data = null) {
		
		if (!$model || !$foreign_id || !$data) {
			return false;
		}
		
		if (!$this->hasUniqueID($Model, $model)) {
			return false;
		}
		
		$uniqueConditions = array();

		$uniqueID = Hash::normalize($Model->uniqueID[$model]);

		// Build Unique Conditions Based on Data
		foreach ($uniqueID as $uniqueField => $uniqueValue) {
			
			if ($uniqueValue === 'foreign_id') {
				$uniqueValue = $foreign_id;
			}
			elseif (in_array($uniqueField, array_keys($data))) {
				$uniqueValue = $data[$uniqueField];
			}
			else {
				// Unique Field Not in Data
				return false;
			}
			
			$uniqueConditions[$uniqueField] = $uniqueValue;
			
		}
		
		return $uniqueConditions;
		
	}
	
	/**
	 * Get Unique ID
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	array $conditions Conditions array. 
	 * @return	mixed False on empty. Id if found.
	 */
	public function getUniqueID(Model $Model, $conditions = array()) {
		
		if (empty($conditions)) {
			return false;
		}

		return $Model->field('id', array(
			$conditions
		));
		
	}
	
	/**
	 * Get Belongs To Field
	 * 
	 * Gets the belongsTo field which has a value that is implicitly known based on the parent model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model	$Model
	 * @param	boolean	$primaryClassName	Primary or child context?
	 * @return	string
	 */
	public function getBelongsToField(Model $Model, $primaryClassName = null) {
		
		if (empty($primaryClassName)) {
			return null;
		}
		
		if (!empty($Model->belongsTo)) {
			foreach ($Model->belongsTo as $relation => $settings) {
				if (
					$settings['className'] === $primaryClassName &&
					!empty($settings['foreignKey'])
				) {
					return $settings['foreignKey'];
				}
			}
		}
		
		return null;
		
	}
	
	/**
	 * Get Default Attributes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model	$Model
	 * @param	array	$options
	 *					`primary`			boolean		Primary or child context?
	 *					`primaryClassName`	string		Primary model's class name
	 * @param	boolean	$primary	Primary or child context?
	 * @return	array
	 */
	public function getDefaultAttributes(Model $Model, $options = array()) {
		
		$options = array_merge(
			array(
				'primary' => true,
				'primaryClassName' => null
			),
			$options
		);
		extract($options);
		
		$attributes = $this->attributes($Model);
		
		$belongsToField = $primary ? null : $this->getBelongsToField($Model, $primaryClassName);
		
		$defaults = array();
		foreach ($attributes as $attribute => $settings) {
			
			if (
				!empty($settings['field']) &&
				$belongsToField === $settings['field']
			) {
				continue;
			}
			
			if (
				empty($settings['request']) ||
				(
					$primary &&
					$settings['request'] === 'relation'
				) ||
				(
					!$primary &&
					$settings['request'] === 'primary'
				)
			) {
				$defaults[] = $attribute;
			}
			
		}
		
		return $defaults;
		
	}
	
	/**
	 * Get Field Map
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	array $attributes Attributes array. 
	 * @return	array
	 */
	public function getFieldMap(Model $Model, $attributes = array()) {
		
		if (empty($attributes)) {
			return array();
		}
		
		$map = array();
		foreach ($attributes as $attribute => $settings) {
			if (empty($settings['field'])) {
				$settings['field'] = $attribute;
			}
			$map[$settings['field']] = $attribute;
		}
		
		return $map;
		
	}
	
	/**
	 * Get A Model's List of Options for a Specific Property.
	 *
	 * @since	1.0
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @param	Model $Model
	 * @param	string	Options prefix
	 * @return	array
	 */
	public function getOptions(Model $Model, $options_prefix = '') {

		$attr = "_{$options_prefix}_options";
		
		if (property_exists($Model, $attr) && is_array($Model->{$attr})) {
			return $Model->{$attr};
		}
		
		$method_prefix = lcfirst(Inflector::camelize($options_prefix));
		
		$method = "{$method_prefix}OptionsList";
		
		if (method_exists($Model, $method)) {
			return call_user_func(array($Model, $method));
		}
		
		return array();
		
	}
	
	/**
	 * Get All Field Names
	 *
	 * Return a list of all field names, regardless of field map settings, and
	 * including virtual fields.
	 * 
	 * @since	1.0
	 * @param	Model $Model
	 * @return	array
	 */
	public function getAllFieldNames(Model $Model) {
		
		$schema = $Model->schema();
		
		$virtualFields = $Model->getVirtualField();
		
		if (empty($virtualFields)) {
			$virtualFields = array();
		}
		
		$all_fields = array_merge($schema, $virtualFields);
		
		$return = array();
		if (!empty($all_fields)) {
			foreach ($all_fields as $field => $settings) {
				$return[] = $Model->alias . '.' . $field;
			}
		}
		
		return $return;
		
	}

	/**
	 * Get Field Names
	 *
	 * @since   1.0
	 * @param	Model $Model
	 * @param   array $field_map Field map array. 
	 * @return  array
	 */
	public function getFieldNames(Model $Model, $field_map = array()) {
		
		if (empty($field_map)) {
			return array();
		}
		
		$field_names = array();
		foreach ($field_map as $field => $attribute) {
			if (substr($field, 0, 10) === 'Metadatum.') {
				continue;
			}
			$field_names[] = $Model->alias . '.' . $field;
		}
		
		return $field_names;
		
	}
	
	/**
	 * Get Metadata Field Names
	 *
	 * @since	1.0
	 * @param	Model $Model
	 * @param	array $field_map Field map array. 
	 * @return	array
	 */
	public function getMetadataFieldNames(Model $Model, $field_map = array()) {
		
		if (empty($field_map)) {
			return array();
		}
		
		$metadata_field_names = array();
		
		foreach ($field_map as $field => $attribute) {
			if (substr($field, 0, 10) === 'Metadatum.') {
				$metadata_field_names[] = substr($field, 10);
			}
		}
		
		return $metadata_field_names;
		
	}
	
	/**
	 * Get Denormalized Fields
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	Model $Model
	 * @param	$data Request data to search foreign keys
	 * @return	array
	 */
	public function getDenormalizedFields(Model $Model, $data = array()) {
		
		if (empty($Model->_denormalized_fields)) {
			return false;
		}
		
		$denormalized_fields = array();
		
		foreach ($Model->_denormalized_fields as $denormalized_field => $settings) {
			
			if (
				is_callable($settings) &&
				is_object($settings) &&
				$settings instanceof Closure 
			) {
				$settings = $settings($data);
			}
			
			if (empty($settings)) {
				continue;
			}
			
			if (!Hash::numeric(array_keys($settings))) {
				$settings = array($settings);
			}
			
			$values = array();
			
			$last = count($settings) - 1;
			
			foreach ($settings as $index => $set) {
				
				if (
					empty($set['field']) ||
					empty($set['conditions']) ||
					!is_array($set['conditions'])
				) {
					continue;
				}

				if (strpos($set['field'], '.')) {
					list(, $field_name) = explode('.', $set['field']);
				} else {
					$field_name = $set['field'];
				}
				
				$field_conditions = array();

				foreach ($set['conditions'] as $field => $value) {
				
					if (!is_string($field)) {
						continue;
					}
				
					$field_model = $Model->alias;
					
					if (strpos($field, '.') !== false) {
						list($field_model, $field) = explode('.', $field);
					}
				
					if ($field_model === $Model->alias) {
						$modelObject = $Model;
					} elseif (isset($Model->{$field_model})) {
						$modelObject = $Model->{$field_model};
					} else {
						$modelObject = ClassRegistry::init($field_model);
					}

					$value_model = $Model->alias;
					
					if (strpos($value, '.') !== false) {
						list($value_model, $value) = explode('.', $value);
					}
				
					if (
						$value_model === $Model->alias &&
						!empty($data[$value])
					) {
						$value = $data[$value];
					} elseif (
						$value_model !== $Model->alias &&
						!empty($values[$value_model .'.'. $value])
					) {
						$value = $values[$value_model .'.'. $value];
					}
				
					$field_conditions[] = array(
						$field_model .'.'. $field => $value
					);
				
				}
			
				$field_value = $modelObject->field($field_name, $field_conditions);
				
				if ($index !== $last && empty($field_value)) {
					continue 3;
				} elseif ($index === $last) {
					$denormalized_fields[$denormalized_field] = $field_value;
				} else {
					$values[$set['field']] = $field_value;
				}
				
			}
			
		}
		
		return $denormalized_fields;
		
	}
	
}
