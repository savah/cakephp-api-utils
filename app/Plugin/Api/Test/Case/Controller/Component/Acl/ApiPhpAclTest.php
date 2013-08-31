<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');
App::uses('AclInterface', 'Controller' . DS . 'Component' . DS . 'Acl');
App::uses('AclComponent', 'Controller' . DS . 'Component');
App::uses('ApiPhpAcl', 'Api.Controller' . DS . 'Component' . DS . 'Acl');

/**
 * ApiPhpAcl Class Test
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
 * @subpackage  Api.Test.Case.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiPhpAclTest extends CakeTestCase {
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function setUp() {

		parent::setUp();

	}

	/**
	 * Teardown
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function tearDown() {

		parent::tearDown();

	}

	/**
	 * Test initialize
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testInitialize() {
		
		$test_config = array(
			'map' => array(
				'User' => 'User/id',
				'Role' => 'User/role_id'
			),
			'alias' => array(
				'Role/public' => 'Public',
				'Role/1' => 'Default',
				'Role/2' => 'Admin',
				'Role/999' => 'Root'
			),
			'roles' => array(
				'Public' => NULL,
				'Default' => 'Public',
				'Admin' => 'Default',
				'Root' => NULL
			),
			'rules' => array(
				'allow' => array(
					'*' => 'Root'
				),
				'deny' => array()
			)
		);
		
		$test_config_file = APP . 'Plugin' . DS . 'Api' . DS . 'Config' . DS . 'Acl' . DS . '_bootstrap_models.php';
		
		$test_models = array(0 => 'Model', 1 => 'Model_Stuff');
		
		$test_adapter = array('someTestAdapter' => 'someTestAdapter');
		
		$Controller = new Controller();
		
		$Controller->name = 'Tests';
		
		$Controller->modelClass = 'Model';
		
		$Controller->Model = $this->getMock('Model', array(
			'getAssociated'
		));
		
		$Controller->Model->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array('Stuff' => 'belongsTo')));
			
		$Collection = new ComponentCollection();
		$Collection->init($Controller);
		
		$Component = $this->getMock(
			'Component',
			array('getController'),
			array($Collection)
		);
		
		$Component->settings['adapter'] = $test_adapter;
		
		$ApiPhpAcl = $this->getMock(
			'ApiPhpAcl',
			array('build', 'configFilename', 'loadConfig'),
			array($Component)
		);
		
		$ApiPhpAcl->expects($this->once())
			->method('configFilename')
			->with($test_models)
			->will($this->returnValue($test_config_file));
		
		$ApiPhpAcl->expects($this->once())
			->method('loadConfig')
			->with($test_models)
			->will($this->returnValue($test_config));
			
		$ApiPhpAcl->expects($this->once())
			->method('build')
			->with($test_config);
			
		$ApiPhpAcl->initialize($Component);
		
		$this->assertEquals(
			$test_config_file,
			$ApiPhpAcl->options['config']
		);
		
		$this->assertEquals(
			constant('PhpAcl::DENY'),
			$ApiPhpAcl->options['policy']
		);
		
		$this->assertEquals(
			$test_adapter['someTestAdapter'],
			$ApiPhpAcl->options['someTestAdapter']
		);
		
	}
	
	/**
	 * Test initialize with AppError controller
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testInitializeWithAppErrorController() {
		
		$Controller = new Controller();
		
		$Controller->name = 'AppError';
		
		$Collection = new ComponentCollection();
		$Collection->init($Controller);
		
		$Component = $this->getMock(
			'Component',
			array('getController'),
			array($Collection)
		);
		
		$ApiPhpAcl = new ApiPhpAcl($Component);
		
		$this->assertTrue($ApiPhpAcl->initialize($Component));
		
	}
	
	/**
	 * Test config filename
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testConfigFilename() {
		
		$ApiPhpAcl = new ApiPhpAcl();
		
		$this->assertEquals(
			APP . 'Config' . DS . 'Acl' . DS . '_base_acl.php',
			$ApiPhpAcl->configFilename(array())
		);
		
		$this->assertEquals(
			APP . 'Plugin' . DS . 'Api' . DS . 'Config' . DS . 'Acl' . DS . '_bootstrap_models.php',
			$ApiPhpAcl->configFilename(array('Model'))
		);
		
	}
	
	/**
	 * Test load config
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testLoadConfig() {
		
		$config_file = APP . 'Plugin' . DS . 'Api' . DS . 'Test' . DS . 'Lib' . DS . '_base_acl.php';
		
		$ApiPhpAcl = new ApiPhpAcl();
		
		$ApiPhpAcl->options['config'] = $config_file;
		
		require $config_file;
		
		$results = $ApiPhpAcl->loadConfig(array());
		
		$this->assertEquals($results, $config);
		
	}
	
}