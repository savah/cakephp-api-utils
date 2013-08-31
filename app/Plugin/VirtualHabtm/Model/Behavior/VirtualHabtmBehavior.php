<?php
App::uses('ModelBehavior', 'Model');
App::uses('Hash', 'Utility');

/**
 * Virtual Habtm Behavior
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
 * @subpackage  Api.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class VirtualHabtmBehavior extends ModelBehavior {
	
	/**
	 * Settings
	 *
	 * @since   1.0
	 * @var	    array
	 */
	private $__settings = array();
	
	/**
	 * Default Settings
	 *
	 * @since   1.0
	 * @var	    array
	 */
	private $__default_settings = array();
	
	/**
	 * Setup
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model		$Model
	 * @param	array		$settings 
	 * @return	void
	 */
	public function setup(Model $Model, $settings = array()) {
		
		if (!isset($this->__settings[$Model->alias])) {
			
			$this->__settings[$Model->alias] = $this->__default_settings;
			
		}
		
		if (!is_array($settings)) $settings = array();

		$this->__settings[$Model->alias] = array_merge($this->__settings[$Model->alias], $settings);
		
		$this->setupHabtmRelations($Model);
		$this->setupHabtmValidationRules($Model);
		
	}
	
	/**
	 * Setup HABTM relations
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model		$Model
	 * @return	void
	 */
	public function setupHabtmRelations(Model $Model) {
		
		$associated = $Model->getAssociated();
		
		foreach ($this->__settings[$Model->alias]['fields'] as $field => $settings) {
			
			extract($settings);
			
			if (!array_key_exists($joinModel, $associated)) {
			
				// This relation takes care of deletions for us
				$Model->bindModel(array(
					'hasMany' => array(
						$joinModel => array(
							'className' => $joinModel,
							'foreignKey' => $foreignKey,
							'dependent' => true,
							'request' => true
						)
					)
				));
			
			}
			
		}
		
	}
	
	/**
	 * Setup HABTM validation rules
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model		$Model
	 * @return	void
	 */
	public function setupHabtmValidationRules(Model $Model) {
		
		foreach ($this->__settings[$Model->alias]['fields'] as $field => $settings) {
			
			if (!array_key_exists($field, $Model->validate)) {
			
				$Model->validate[$field] = array(
					'validateHabtmRelation' => array(
						'rule' => 'validateHabtmRelation',
						'allowEmpty' => true,
						'last' => true,
						'message' => 'Please enter a valid list of IDs'
					)
				);
			
			}
			
		}
		
	}
	
	/**
	 * After Save Callback
	 *
	 * Save HABTM relations
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model		$Model
	 * @param	boolean		$created
	 * @return	boolean
	 */
	public function afterSave(Model $Model, $created) {
		
		if (empty($Model->data[$Model->alias])) {
			return true;
		}
		
		foreach ($this->__settings[$Model->alias]['fields'] as $field => $settings) {
			
			if (array_key_exists($field, $Model->data[$Model->alias])) {
				
				extract($settings);
				
				if (!isset($Model->{$joinModel})) {
					$Model->{$joinModel} = ClassRegistry::init($joinModel);
				}
				
				if (!$created) {
					if (!$Model->{$joinModel}->deleteAll(array(
						$Model->{$joinModel}->alias . '.' . $foreignKey => $Model->id
					), false, false)) {
						return false;
					}
				}
				
				$association_ids = $this->getHabtmAssociationIdArray($Model, $Model->data[$Model->alias][$field]);
				
				if (empty($association_ids)) {
					return true;
				}
				
				$data = array();
				foreach ($association_ids as $association_id) {
					$data[][$Model->{$joinModel}->alias] = array(
						$foreignKey => $Model->id,
						$associationKey => $association_id
					);
				}
				if (!$Model->{$joinModel}->saveMany($data, array(
					'validate' => false,
					'atomic' => false,
					'callbacks' => false
				))) {
					return false;
				}
				
			}
			
		}
		
		return true;
		
	}
	
	/**
	 * Validation rule for HABTM relations
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model		$Model
	 * @param	mixed		$check
	 * @return	boolean
	 */
	public function validateHabtmRelation(Model $Model, $check = array()) {
		
		foreach ($this->__settings[$Model->alias]['fields'] as $field => $settings) {
			
			if (array_key_exists($field, $check)) {
				
				$ids = array_shift($check);
				$ids = $this->getHabtmAssociationIdArray($Model, $ids);
				
				if (!Hash::numeric($ids)) {
					return false;
				}
				
				extract($settings);
				
				if (!isset($Model->{$foreignModel})) {
					$Model->{$foreignModel} = ClassRegistry::init($foreignModel);
				}
				
				$relations = array();
				
				foreach ($ids as $id) {
					if (array_key_exists($id, $relations)) {
						return false;
					}
					$relations[$id] = true;
					if (!$Model->{$foreignModel}->exists($id)) {
						return false;
					}
				}
				
				return true;
				
			}
			
		}
		
		return false;
		
	}
	
	/**
	 * Get association ID array from mixed input
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Model	$Model
	 * @param	mixed	$input	A list that is either: an array, JSON encoded, comma delimited
	 * @return	array	Array of IDs
	 */
	public function getHabtmAssociationIdArray(Model $Model, $input = null) {
		
		$association_ids = $input;
				
		if (!is_array($association_ids)) {
			$association_ids = json_decode($input);
		}
		
		if (
			is_array($association_ids) 
			&& count($association_ids) === 1 && 
			isset($association_ids[0]) && 
			is_string($association_ids[0]) &&
			strpos($association_ids[0], ',') !== false
		) {
			$association_ids = $input = $association_ids[0];
		}

		if (!is_array($association_ids)) {
			$association_ids = explode(',', $input);
		}
		
		if (
			is_array($association_ids) &&
			count($association_ids) === 1 &&
			isset($association_ids[0]) &&
			empty($association_ids[0])
		) {
			$association_ids = array();
		}
		
		return $association_ids;
		
	}
	
}
