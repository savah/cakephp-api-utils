<?php
/**
 * OAuth2 Authorization Behavior
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Anthony Putignano <anthony@wizehive.com>
 * @since       0.1
 * @package     OAuth2
 * @subpackage  OAuth2.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2AuthorizationBehavior extends ModelBehavior {
	
	/**
	 * Default settings
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	private $__default_settings = array(
		'fields' => array(
			'client_id' => 'oauth2_client_id',
			'user_id' => 'user_id',
			'scope' => 'scope'
		)
	);

	/**
	 * Settings
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	protected $_settings = array();
	
	/**
	 * Setup callback
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	reference	$Model
	 * @param	array		$settings 
	 * @return	void
	 */
	public function setup(&$Model, $settings = array()) {
		
		if (!isset($this->_settings[$Model->alias])) {
			
			$this->_settings[$Model->alias] = $this->__default_settings;
			
		}
		
		if (!is_array($settings)) $settings = array();

		$this->_settings[$Model->alias] = array_merge($this->_settings[$Model->alias], $settings);
		
	}
	
	/**
	 * After save callback
	 * 
	 * If this is the first time this model has seen the `client_id` and `user_id` combination which
	 * was just saved, then create an `OAuth2Authorization` for it.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @param	Model	$Model
	 * @param	boolean	$created
	 * @return	boolean
	 */
	public function afterSave(&$Model, $created = false) {
		
		if (
			empty($Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['client_id']]) ||
			empty($Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['user_id']])
		) {
			return true;
		}
		
		$scope = null;
		if (!empty($Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['scope']])) {
			$scope = $Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['scope']];
		}
		
		if (!isset($this->OAuth2Authorization)) {
			$this->OAuth2Authorization = ClassRegistry::init('OAuth2.OAuth2Authorization');
		}
		
		if (!$this->OAuth2Authorization->upsert(
			$Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['client_id']],
			$Model->data[$Model->alias][$this->_settings[$Model->alias]['fields']['user_id']],
			$scope
		)) {
			return false;
		}
		
		return true;
		
	}
	
}
