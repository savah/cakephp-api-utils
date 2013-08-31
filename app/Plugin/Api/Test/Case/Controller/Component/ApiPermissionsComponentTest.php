<?php
App::uses('AppModel', 'Model');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('AuthComponent', 'Controller' . DS . 'Component');
App::uses('AclComponent', 'Controller' . DS . 'Component');
App::uses('ApiPermissionsComponent', 'Api.Controller' . DS . 'Component');

/**
 * Thing Model
 *
 */
if (!class_exists('Thing')) {
	class Thing extends AppModel {}
}

/**
 * Api Permissions Component Test
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
class ApiPermissionsComponentTest extends ControllerTestCase {
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function setUp() {

		parent::setUp();
		
		$this->Controller = $this->getMock('TestController');
		
		$this->Controller->name = 'Things';
		
		$this->Controller->modelClass = 'Thing';
		
		$this->Controller->Thing = $this->getMock('Thing');
		
		$this->Controller->request = $this->getMock('CakeRequest');
		
		$this->Controller->response = $this->getMock('CakeResponse');
		
		$this->ComponentCollection = $this->getMock(
			'ComponentCollection',
			array('getController')
		);
		
		$this->ComponentCollection
			->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
			
		$this->Permissions = new ApiPermissionsComponent($this->ComponentCollection);

		$this->Permissions->modelClass = 'Thing';
		
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
		
		ClassRegistry::flush();

	}
	
	/**
	 * Test Instance Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function testInstanceSetup() {
		
		$components = Hash::normalize($this->Permissions->components);
		
		$this->assertArrayHasKey('Auth', $components);
		
		$this->assertInstanceOf('AuthComponent', $this->Permissions->Auth);
		
		$this->assertArrayHasKey('Acl', $components);
		
		$this->assertInstanceOf('AclComponent', $this->Permissions->Acl);
		
	}

	/**
	 * Test For Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testForModel() {
		
		$this->assertEquals(
			$this->Permissions->forModel(),
			$this->Permissions
		);
		
		$model = 'ModelForTest';
		
		$this->Permissions->forModel($model);
		
		$this->assertEquals(
			$model,
			$this->Permissions->_model
		);
		
	}
	
	/**
	 * Test With Attributes
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithAttributes() {
		
		$this->assertEquals(
			$this->Permissions->withAttributes(),
			$this->Permissions
		);
		
		$attributes = array(
			'field1' => 'attribute1',
			'field2' => 'attribute2',
			'field3' => 'attribute3'
		);
		
		$this->Permissions->forModel('Thing');
		
		$this->assertInstanceOf(
			'Component',
			$this->Permissions->withAttributes($attributes)
		);
		
		$this->assertEquals(
			$this->Permissions->_attributes,
			$attributes
		);
		
	}
	
	/**
	 * Test With Fields
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithFields() {
		
		$this->assertEquals(
			$this->Permissions->withFields(),
			$this->Permissions
		);
		
		$this->Permissions->forModel('Thing');
		
		$fields = array('field1', 'field2', 'field3');
		
		$this->assertInstanceOf(
			'Component',
			$this->Permissions->withFields($fields)
		);
		
		$this->assertEquals(
			$this->Permissions->_fields,
			$fields
		);
		
	}
	
	/**
	 * Test On
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testOn() {
		
		$this->assertEquals(
			$this->Permissions->on(),
			$this->Permissions
		);
		
		$this->Permissions->forModel('Thing');
		
		$this->assertInstanceOf(
			'Component',
			$this->Permissions->on()
		);
		
		$this->assertEquals(
			$this->Permissions->_on,
			'read'
		);
		
		$this->assertInstanceOf(
			'Component',
			$this->Permissions->on('update')
		);
		
		$this->assertEquals(
			$this->Permissions->_on,
			'update'
		);
		
	}
	
	/**
	 * Test Can Create - Empty Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanCreateEmptyModel() {
		
		$this->assertFalse($this->Permissions->canCreate());
		
	}
	
	/**
	 * Test Can Create - Method `isUserAuthorizedToCreate` Not Exists
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanCreateMethodIsUserAuthorizedToCreateNotExists() {
		
		$test_user_id = 1;
		
		$test_resource_data = array(
			'stuff' => true
		);
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'userRole'
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);

		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canCreate($test_resource_data));
		
	}
	
	/**
	 * Test Can Create - Permissions Behavior Not Loaded
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanCreatePermissionsBehaviorNotLoaded() {
		
		$test_user_id = 1;
		
		$test_resource_data = array(
			'stuff' => true
		);
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'userRole'
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);
		
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canCreate($test_resource_data));
		
	}
	
	/**
	 * Test Can Create - Returning False
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanCreateFalse() {
		
		$test_user_id = 1;
		$test_resource_data = array(
			'stuff' => true
		);
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'userRole',
				'isUserAuthorizedToCreate'
			)
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToCreate')
			->with($test_resource_data)
			->will($this->returnValue(false));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canCreate($test_resource_data));
		
	}
	
	/**
	 * Test Can Create - Returning True
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanCreateTrue() {
		
		$test_user_id = 1;
		$test_resource_data = array(
			'stuff' => true
		);
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array('userId', 'userRole', 'isUserAuthorizedToCreate')
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToCreate')
			->with($test_resource_data)
			->will($this->returnValue(true));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertTrue($this->Permissions->canCreate($test_resource_data));
		
	}
	
	/**
	 * Test Can Update - Empty Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdateEmptyId() {
		
		$id = false;
		
		$this->assertFalse($this->Permissions->canUpdate($id));
		
	}
	
	/**
	 * Test Can Update - Empty Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdateEmptyModel() {
		
		$id = 1;
		
		$this->assertFalse($this->Permissions->canUpdate($id));
		
	}
	
	/**
	 * Test Can Update - Method `isUserAuthorizedToUpdate` Not Exists
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdateMethodIsUserAuthorizedToUpdateNotExists() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'id',
				'userRole',
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('id');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);
			
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canUpdate($test_resource_id));
		
	}
	
	/**
	 * Test Can Update - Permissions Behavior Not Loaded
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdatePermissionsBehaviorNotLoaded() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'id',
				'userRole',
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('id');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);
		
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canUpdate($test_resource_id));
		
	}
	
	/**
	 * Test Can Update - Returning False
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdateFalse() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array('userId', 'id', 'userRole', 'isUserAuthorizedToUpdate')
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('id')
			->with($test_resource_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToUpdate')
			->with()
			->will($this->returnValue(false));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canUpdate($test_resource_id));
		
	}
	
	/**
	 * Test Can Update - Returning True
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanUpdateTrue() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array('userId', 'id', 'userRole', 'isUserAuthorizedToUpdate')
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('id')
			->with($test_resource_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToUpdate')
			->with()
			->will($this->returnValue(true));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertTrue($this->Permissions->canUpdate($test_resource_id));
		
	}
	
	/**
	 * Test Can Delete - Empty Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeleteEmptyId() {
		
		$id = false;
		
		$this->assertFalse($this->Permissions->canDelete($id));
		
	}
	
	/**
	 * Test Can Delete - Empty Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeleteEmptyModel() {
		
		$id = 1;
		
		$this->assertFalse($this->Permissions->canDelete($id));
		
	}
	
	/**
	 * Test Can Delete - Method `isUserAuthorizedToDelete` Not Exists
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeleteMethodIsUserAuthorizedToDeleteNotExists() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'id',
				'userRole',
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('id');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);
		
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canDelete($test_resource_id));
		
	}
	
	/**
	 * Test Can Delete - Permissions Behavior Not Loaded
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeletePermissionsBehaviorNotLoaded() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'id',
				'userRole',
			)
		);
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userId');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('id');
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userRole');
			
		$this->Permissions->withPermissionsBehavior($test_resource_id);
			
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canDelete($test_resource_id));
		
	}
	
	/**
	 * Test Can Delete - Returning False
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeleteFalse() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array('userId', 'id', 'userRole', 'isUserAuthorizedToDelete')
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('id')
			->with($test_resource_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToDelete')
			->with()
			->will($this->returnValue(false));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertFalse($this->Permissions->canDelete($test_resource_id));
		
	}
	
	/**
	 * Test Can Delete - Returning True
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCanDeleteTrue() {
		
		$test_user_id = 1;
		
		$test_resource_id = 2;
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array('userId', 'id', 'userRole', 'isUserAuthorizedToDelete')
		);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userId')
			->with($test_user_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('id')
			->with($test_resource_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('isUserAuthorizedToDelete')
			->with()
			->will($this->returnValue(true));
			
		$this->Permissions->forModel('Thing');
		
		$this->assertTrue($this->Permissions->canDelete($test_resource_id));
		
	}
	
	/**
	 * Test Require Conditions - With A Not Allowed Crud Operation
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRequireConditionsWithANotAllowedCrudOperation() {
		
		$crud = 'create';
		
		$this->assertEquals(
			array(),
			$this->Permissions
				->forModel('Test')
				->on('create')
				->requireConditions()
		);
		
	}
	
	/**
	 * Test Require Conditions - Missing Params/Conditions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRequireConditionsEmptyArrays() {
		
		$this->assertEquals(array(), $this->Permissions->requireConditions());
		
		$this->Permissions->forModel('Thing');
		
		$this->assertEquals(array(), $this->Permissions->requireConditions());
		
		$this->Permissions->on('update');
		
		$this->assertEquals(array(), $this->Permissions->requireConditions());		
		
	}
	
	/**
	 * Test Require Conditions - Results
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRequireConditionsResults() {
		
		$test_id = 1;
		
		$test_fields = array(
			'Thing.id',
			'Thing.some_id',
			'Thing.workspace_permissions_foreign_id',
			'Thing.email',
			'Thing.username',
			'Thing.password',
			'Thing.address',
			'field_without_model_prefix'
		);
		
		$test_permissions_behavior = 'TestPermissions';
		
		$this->Permissions->Auth = $this->getMock(
			'Auth',
			array('user')
		);
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->will($this->returnCallback(function(){
				$arg = array_shift(func_get_args());
				switch ($arg) {
					case 'id':
						$return = 1;
						break;
					case 'Role.slug':
						$return = 'default';
						break;
					default:
						$return = null;
						break;
				}
				return $return;
			}));
			
		$this->Controller->Thing = $this->getMockForModel(
			'Thing',
			array(
				'userId',
				'userRole',
				'userIsAuthorizedToReadIds',
				'userIsAuthorizedToReadSomeIds',
				'userIsAuthorizedToReadEmails',
				'userIsAuthorizedToReadUsernames',
				'userIsAuthorizedToReadPasswords',
				'userIsAuthorizedToUpdateIds',
				'userIsAuthorizedToUpdateOtherFields',
				'userIsAuthorizedToDeleteIds',
				'userIsAuthorizedToDeleteOtherFields',
				'userIsAuthorizedToReadForeignKeys',
				'userIsAuthorizedToUpdateForeignKeys',
				'userIsAuthorizedToDeleteForeignKeys',
				'permissionsSettings'
			)
		);
		
		$this->Controller->Thing->Behaviors = $this->getMock('ModelBehavior', array('enabled'));
		
		$this->Controller->Thing->Behaviors
			->expects($this->exactly(3))
			->method('enabled')
			->with($test_permissions_behavior)
			->will($this->returnValue(true));
		
		$this->Controller->Thing
			->expects($this->exactly(3))
			->method('permissionsSettings')
			->with()
			->will($this->returnValue(array('foreignKey' => 'workspace_permissions_foreign_id')));
			
		$this->Controller->Thing
			->expects($this->any())
			->method('userId')
			->with($test_id)
			->will($this->returnValue($this->Controller->Thing));
		
		$this->Controller->Thing
			->expects($this->any())
			->method('userRole')
			->with('default')
			->will($this->returnValue($this->Controller->Thing));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadIds')
			->with()
			->will($this->returnValue(array(1, 2)));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadSomeIds')
			->with()
			->will($this->returnValue('*'));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadEmails')
			->with()
			->will($this->returnValue(array('john@example.org')));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadUsernames')
			->with()
			->will($this->returnValue(array('john')));
			
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadPasswords')
			->with()
			->will($this->returnValue(array()));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToReadForeignKeys')
			->with()
			->will($this->returnValue(array(4, 5, 6)));
			
		$this->Permissions->withPermissionsBehavior($test_permissions_behavior);
		
		$this->Permissions->forModel('Thing');
		
		$this->Permissions->withFields($test_fields);
		
		$results = $this->Permissions->requireConditions();
		
		$expected = array(
			'Thing.id' => array(1, 2),
			'Thing.workspace_permissions_foreign_id' => array(4, 5, 6),
			'Thing.email' => array('john@example.org'),
			'Thing.username' => array('john'),
			'Thing.password' => array(),
		);
		
		$this->assertEquals($expected, $results);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToUpdateIds')
			->with()
			->will($this->returnValue(array(1, 2, 3)));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToUpdateForeignKeys')
			->with()
			->will($this->returnValue(array(4, 5, 6)));
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userIsAuthorizedToUpdateOtherFields');
		
		$this->Permissions->forModel('Thing');
		
		$this->Permissions->on('update');
		
		$this->Permissions->withFields($test_fields);
		
		$results = $this->Permissions->requireConditions();
		
		$expected = array(
			'Thing.id' => array(1, 2, 3),
			'Thing.workspace_permissions_foreign_id' => array(4, 5, 6)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToDeleteIds')
			->with()
			->will($this->returnValue(array(1, 2, 3)));
		
		$this->Controller->Thing
			->expects($this->once())
			->method('userIsAuthorizedToDeleteForeignKeys')
			->with()
			->will($this->returnValue(array(4, 5, 6)));
		
		$this->Controller->Thing
			->expects($this->never())
			->method('userIsAuthorizedToDeleteOtherFields');
		
		$this->Permissions->forModel('Thing');
		
		$this->Permissions->on('delete');
		
		$this->Permissions->withFields($test_fields);
		
		$results = $this->Permissions->requireConditions();
		
		$expected = array(
			'Thing.id' => array(1, 2, 3),
			'Thing.workspace_permissions_foreign_id' => array(4, 5, 6)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Allow Attributes - Missing Params/Conditions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testAllowAttributesMissingParams() {
		
		$test_model = 'Thing';
		
		$this->assertEquals(array(), $this->Permissions->allowAttributes());
		
		$this->Permissions->forModel($test_model);
		
		$this->assertEquals(array(), $this->Permissions->allowAttributes());
		
	}
	
	/**
	 * Test Allow Attributes - Results With Empty Role Id (Defaults To "public").
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testAllowAttributesResultsWithEmptyRoleId() {
		
		$test_crud = $this->Permissions->_on;
		
		$test_model = 'Thing';
		
		$test_attributes = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1',
			'attribute2' => 'settings2'
		);
		
		$test_role_name = 'Role/public';
		
		$test_check_prefix = sprintf('crud/%s/%s/', $test_model, $test_crud);
		
		$test_acl_checks_with_value = array(
			$test_check_prefix . 'attribute0',
			$test_check_prefix . 'attribute1',
			$test_check_prefix . 'attribute2'
		);
		
		$test_acl_checks_will_value = array(
			$this->returnValue(true),   // attribute0
			$this->returnValue(true),   // attribute1
			$this->returnValue(false),  // attribute2
		);
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->with('Role.id')
			->will($this->returnValue(null));
			
		$this->Permissions->Acl = $this->getMock('Acl', array('check'));
		
		$this->Permissions->Acl
			->expects($this->at(0))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[0])
			->will($test_acl_checks_will_value[0]);

		$this->Permissions->Acl
			->expects($this->at(1))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[1])
			->will($test_acl_checks_will_value[1]);

		$this->Permissions->Acl
			->expects($this->at(2))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[2])
			->will($test_acl_checks_will_value[2]);
		
		$this->Permissions->forModel($test_model);
		
		$this->Permissions->withAttributes($test_attributes);
		
		$results = $this->Permissions->allowAttributes();
		
		$expected = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Allow Attributes - Results With Role Id Zero
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testAllowAttributesResultsWithRoleIdZero() {
		
		$test_role_id = 0;
		
		$test_crud = $this->Permissions->_on;
		
		$test_model = 'Thing';
		
		$test_attributes = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1',
			'attribute2' => 'settings2'
		);
		
		$test_role_name = 'Role/' . $test_role_id;
		
		$test_check_prefix = sprintf('crud/%s/%s/', $test_model, $test_crud);
		
		$test_acl_checks_with_value = array(
			$test_check_prefix . 'attribute0',
			$test_check_prefix . 'attribute1',
			$test_check_prefix . 'attribute2'
		);
		
		$test_acl_checks_will_value = array(
			$this->returnValue(true),   // attribute0
			$this->returnValue(true),   // attribute1
			$this->returnValue(false),  // attribute2
		);
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->with('Role.id')
			->will($this->returnValue($test_role_id));
			
		$this->Permissions->Acl = $this->getMock('Acl', array('check'));
		
		$this->Permissions->Acl
			->expects($this->at(0))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[0])
			->will($test_acl_checks_will_value[0]);

		$this->Permissions->Acl
			->expects($this->at(1))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[1])
			->will($test_acl_checks_will_value[1]);

		$this->Permissions->Acl
			->expects($this->at(2))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[2])
			->will($test_acl_checks_will_value[2]);
		
		$this->Permissions->forModel($test_model);
		
		$this->Permissions->withAttributes($test_attributes);
		
		$results = $this->Permissions->allowAttributes();
		
		$expected = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test allow Attributes - Results With Private Role
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testAllowAttributesResultsWithPrivateRole() {
		
		$test_role_id = 'private';
		
		$test_crud = $this->Permissions->_on;
		
		$test_model = 'Thing';
		
		$test_attributes = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1',
			'attribute2' => 'settings2'
		);
		
		$test_role_name = 'Role/' . $test_role_id;
		
		$test_check_prefix = sprintf('crud/%s/%s/', $test_model, $test_crud);
		
		$test_acl_checks_with_value = array(
			$test_check_prefix . 'attribute0',
			$test_check_prefix . 'attribute1',
			$test_check_prefix . 'attribute2'
		);
		
		$test_acl_checks_will_value = array(
			$this->returnValue(true),   // attribute0
			$this->returnValue(true),   // attribute1
			$this->returnValue(false),  // attribute2
		);
		
		$this->Permissions->Auth = $this->getMock('Auth', array('user'));
		
		$this->Permissions->Auth
			->expects($this->any())
			->method('user')
			->with('Role.id')
			->will($this->returnValue($test_role_id));
			
		$this->Permissions->Acl = $this->getMock('Acl', array('check'));
		
		$this->Permissions->Acl
			->expects($this->at(0))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[0])
			->will($test_acl_checks_will_value[0]);

		$this->Permissions->Acl
			->expects($this->at(1))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[1])
			->will($test_acl_checks_will_value[1]);

		$this->Permissions->Acl
			->expects($this->at(2))
			->method('check')
			->with($test_role_name, $test_acl_checks_with_value[2])
			->will($test_acl_checks_will_value[2]);
		
		$this->Permissions->forModel($test_model);
		
		$this->Permissions->withAttributes($test_attributes);
		
		$results = $this->Permissions->allowAttributes();
		
		$expected = array(
			'attribute0' => 'settings0',
			'attribute1' => 'settings1'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
}
