<?php
App::uses('PhpAcl', 'Controller' . DS . 'Component' . DS . 'Acl');

/**
 * ApiPhpAcl Class
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
 * @subpackage  Api.Controller.Component.Acl
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiPhpAcl extends PhpAcl {

	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function __construct() {
		$this->options = array(
			'policy' => self::DENY,
			'config' => null,
		);
	}
	
	/**
	 * Initialize
	 *
	 * @since   1.0
	 * @param   object $Component Component object. 
	 * @return  void
	 */
	public function initialize(Component $Component) {
		
		$Controller = $Component->_Collection->getController();
		
		if ($Controller->name === 'AppError') {
			return true;
		}
		
		$related = array();
		$associated = $Controller->{$Controller->modelClass}->getAssociated();
		if (!empty($associated)) {
			foreach ($associated as $model => $type) {
				$related[] = $Controller->modelClass . '_' . $model;
			}
		}
		
		$models = array_merge(
			array($Controller->modelClass),
			$related
		);
			
		$this->options['config'] = $this->configFilename($models);
		
		if (!empty($Component->settings['adapter'])) {
			$this->options = array_merge(
				$this->options,
				$Component->settings['adapter']
			);
		}
		
		$config = $this->loadConfig($models);
		
		$this->build($config);
		
		$Component->Aco = $this->Aco;
		$Component->Aro = $this->Aro;
		
	}
	
	/**
	 * Get Config Filename
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @param   array $models Models array. 
	 * @return  string Filename with full path.
	 */
	public function configFilename($models = array()) {
		
		$file = (!empty($models) ? '_bootstrap_models' : '_base_acl') . '.php';
		
		$plugin_path = !empty($models) ? 'Plugin' . DS . 'Api' . DS : '';

		$config_file = APP . $plugin_path . 'Config' . DS . 'Acl' . DS . $file;
		
		return $config_file;
		
	}
	
	/**
	 * Load config
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @param   array $models Models array. 
	 * @return  array Configuration array.
	 */
	public function loadConfig($models = array()) {
		
		require $this->options['config'];
		
		return $config;
		
	}
	
}
