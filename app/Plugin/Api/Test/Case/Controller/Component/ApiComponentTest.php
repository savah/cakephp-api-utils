<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ApiQueryComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiInputDataComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiPermissionsComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiComponent', 'Api.Controller' . DS . 'Component');

/**
 * Api Component Test
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
class ApiComponentTest extends CakeTestCase {
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function setUp() {

		parent::setUp();
		
		$this->Controller = $this->getMock('Controller', array(
			'_abort',
			'set'
		));
		
		$this->Controller->request = $this->getMock('CakeRequest');
		
		$this->Controller->response = $this->getMock('CakeResponse');
		
		$this->Controller->params = array();
		
		$this->ComponentCollection = $this->getMock(
			'ComponentCollection',
			array('getController')
		);
		
		$this->Controller->modelClass = 'Model';
		
		$this->ComponentCollection->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
			
		$this->Api = new ApiComponent($this->ComponentCollection);
		
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
		
		// Test Components
		
		$components = Hash::normalize($this->Api->components);

		$this->assertArrayHasKey('Query', $components);
		
		$this->assertArrayHasKey('InputData', $components);
		
		$this->assertArrayHasKey('Permissions', $components);
		
		$this->assertArrayHasKey('Resource', $components);

		$this->assertArrayHasKey('ApiPaginator', $components);
		
		$this->assertArrayHasKey('ApiRequestHandler', $components);

	}
	
	/**
	 * Test Constructor
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testContructor() {
		
		$this->assertEquals(
			$this->Controller,
			$this->Api->Controller
		);
		
	}
	
	/**
	 * Test Constructor - With Permissions Behavior
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testContructorWithPermissionsBehavior() {
		
		$settings = array(
			'permissionsBehavior' => 'WorkspacePermissions'
		);
		
		$this->Api->Permissions = $this->getMock(
			'ApiPermissionsComponent', 
			array('withPermissionsBehavior'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->Permissions
			->expects($this->once())
			->method('withPermissionsBehavior')
			->with($settings['permissionsBehavior'])
			->will($this->returnValue(true));
			
		$this->Api->__construct($this->ComponentCollection, $settings);
		
	}
	
	/**
	 * Test Startup - For `AppError` 
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testStartupForAppError() {
	
		$this->Api->Query = $this->getMock(
			'ApiQueryComponent', 
			array('onModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->InputData = $this->getMock(
			'ApiInputDataComponent', 
			array('forModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->Query
			->expects($this->never())
			->method('onModel');
		
		$this->Api->Query
			->expects($this->never())
			->method('prepare');
		
		$this->Api->InputData
			->expects($this->never())
			->method('forModel');
		
		$this->Api->InputData
			->expects($this->never())
			->method('prepare');
		
		$this->Controller->name = 'AppError';
		
		$this->assertTrue($this->Api->startup($this->Controller));
		
	}
	
	/**
	 * Test Startup - With Exempt Actions
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testStartupWithExemptActions() {
	
		$this->Api->Query = $this->getMock(
			'ApiQueryComponent', 
			array('onModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->InputData = $this->getMock(
			'ApiInputDataComponent', 
			array('forModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->Query
			->expects($this->never())
			->method('onModel');
		
		$this->Api->Query
			->expects($this->never())
			->method('prepare');
		
		$this->Api->InputData
			->expects($this->never())
			->method('forModel');
		
		$this->Api->InputData
			->expects($this->never())
			->method('prepare');
		
		$action = 'index';
		
		$this->Controller->action = 'index';
		
		$this->assertEquals(
			array($action),
			$this->Api->exemptActions($action)
		);
		
		$this->assertTrue($this->Api->startup($this->Controller));
		
	}
	
	/**
	 * Test Startup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testStartup() {
	
		$this->Api->Query = $this->getMock(
			'ApiQueryComponent', 
			array('onModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->InputData = $this->getMock(
			'ApiInputDataComponent', 
			array('forModel', 'prepare'), 
			array($this->ComponentCollection)
		);
		
		$this->Api->Query
			->expects($this->once())
			->method('onModel')
			->with()
			->will($this->returnValue($this->Api->Query));
		
		$this->Api->Query
			->expects($this->once())
			->method('prepare');
		
		$this->Api->InputData
			->expects($this->once())
			->method('forModel')
			->with($this->equalTo('Model'))
			->will($this->returnValue($this->Api->InputData));
		
		$this->Api->InputData
			->expects($this->once())
			->method('prepare');
		
		$this->assertTrue($this->Api->startup($this->Controller));
		
	}
	
	/**
	 * Test Code
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testCode() {
		
		$test_code = 9999;
		
		$this->assertEquals(
			$this->Api->_code,
			$this->Api->code()
		);
		
		$this->assertEquals(
			$test_code,
			$this->Api->code($test_code)
		);
		
		$this->assertEquals(
			$test_code,
			$this->Api->_code
		);
		
	}
	
	/**
	 * Test Status Code
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testStatusCode() {
		
		$test_code = 500;
		
		$this->assertEquals(
			$this->Api->_status_code,
			$this->Api->statusCode()
		);
		
		$this->assertEquals(
			$test_code,
			$this->Api->statusCode($test_code)
		);
		
		$this->assertEquals(
			$test_code,
			$this->Api->_status_code
		);
		
	}
	
	/**
	 * Test Action Errors
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testActionErrors() {
		
		$this->assertEquals(
			$this->Api->_action_errors,
			$this->Api->actionErrors()
		);
		
		$errors = 'add';
		
		$this->assertEquals(
			$this->Api->_action_errors[$errors],
			$this->Api->actionErrors($errors)
		);
		
		$errors = 'test_non_pre_defined_action_error_string';
		
		$this->assertEquals('', $this->Api->actionErrors($errors));
		
		$errors = array(
			'test1' => 'Test error #1',
			'test2' => 'Test error #2'
		);
		
		$expected = array_merge($this->Api->_action_errors, $errors);
		
		$this->assertEquals($this->Api->actionErrors($errors), $expected);
		
		$this->assertEquals($this->Api->_action_errors, $expected);
		
	}
	
	/**
	 * Test Set App Code - With Invalid Code
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetAppCodeWithInvalidCode() {
		
		$test_code = 999999999999999999999999999;
		
		$this->Api->ApiResponseCode = $this->getMock('ApiResponseCode', array('findById'));
		
		$this->Api->ApiResponseCode
			->expects($this->once())
			->method('findById')
			->with($test_code)
			->will($this->returnValue(false));
			
		$this->assertFalse($this->Api->setResponseCode($test_code));
		
	}
	
	/**
	 * Test Set App Code - With Valid Code
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetAppCodeWithValidCode() {
		
		$test_code = 4001;
		
		$test_action = 'edit';
		
		$test_app_code = array(
			'id' => $test_code,
			'httpCode' => 403,
			'message' => 'Cannot pass ID'
		);
		
		$this->Api->ApiResponseCode = $this->getMock('ApiResponseCode', array('findById'));
		
		$this->Api->ApiResponseCode
			->expects($this->once())
			->method('findById')
			->with($test_code)
			->will($this->returnValue(array('ApiResponseCode'=>$test_app_code)));
		
		$this->Api->Controller->request->action = $test_action;
		
		$expected = array(
			'code' => $test_code,
			'status' => $test_app_code['httpCode'],
			'userMessage' => $this->Api->_action_errors[$test_action],
			'developerMessage' => $test_app_code['message']
		);
			
		$this->assertTrue($this->Api->setResponseCode($test_code));
		
		$result = $this->Api->specialViewVars();
		
		$this->assertEquals($expected, $result);
		
	}

	/**
	 * Test Has App Code - Returns False
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testHasAppCodeFalse() {
		
		$test = $this->Api->hasResponseCode();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Has App Code - Returns Code
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testHasAppCode() {
		
		$this->Api->specialViewVars(array('code' => 2000));
		
		$test = $this->Api->hasResponseCode();
		
		$this->assertEqual($test, 2000);
		
	}
	
	/**
	 * Test Special View Vars
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSpecialViewVars() {
		
		$result = $this->Api->specialViewVars();
		
		$expected = array();
		
		$this->assertEquals($expected, $result);
		
		$result = $this->Api->specialViewVars(array(
			'one' => 'first'
		));
		
		$expected = array(
			'one' => 'first'
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->Api->specialViewVars(array(
			'two' => 'second'
		));
		
		$expected = array(
			'one' => 'first',
			'two' => 'second'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Parse View Vars - With All Params
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsAllParams() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
			
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with();
			
		$test_special_view_vars = array(
			'status' => 200,
			'code' => 2000,
			'developerMessage' => 'Oh cool!',
			'systemMessage' => 'meant for debugging',
			'userMessage' => 'Awesome!',
		);
		
		$test_view_vars = array(
			'someOtherVar' => 'someOtherValue'
		);
		
		$test_count = 100;
		
		$test_limit = 100;
		
		$test_page = 0;
		
		$test_paging_params = array(
			$this->Controller->modelClass => array(
				'count' => $test_count,
				'limit' => $test_limit,
				'page' => $test_page
			)
		);
		
		$this->Api->Controller->request->params['paging'] = $test_paging_params;
		
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->Controller->viewVars = $test_view_vars;
		
		$this->Api->parseViewVars();
		
		$expected = array(
			'data' => array_merge(
				$test_special_view_vars,
				array(
					'totalCount' => $test_count,
					'limit' => $test_limit,
					'offset' => $test_page - 1
				),
				array(
					'data' => array('someOtherVar'=>'someOtherValue')
				)
			),
			'_serialize' => 'data'
		);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Parse View Vars - Some Params
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsSomeParams() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
			
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with();
			
		$test_special_view_vars = array(
			'status' => 200,
			'code' => 2000,
			'developerMessage' => 'Oh cool!',
			'userMessage' => 'Awesome!'
		);
		
		$test_limit = 100;
		
		$test_paging_params = array(
			$this->Controller->modelClass => array(
				'limit' => $test_limit,
			)
		);

		$this->Api->Controller->request->params['paging'] = $test_paging_params;
		
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->Controller->viewVars = array();
		
		$this->Api->parseViewVars();

		$expected = array(
			'data' => array_merge(
				$test_special_view_vars,
				array(
					'limit' => $test_limit
				)
			),
			'_serialize' => 'data'
		);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Parse View Vars - Validation Errors On Multi Or Batch Actions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsValidationErrorsOnMultiOrBatchActions() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
		
		$validation_errors = array(
			'attribute1' => array(
				'Error 1'
			)
		);
		
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with()
			->will($this->returnValue($validation_errors));
			
		$test_special_view_vars = array(
			'status' => 403,
			'code' => 4003,
			'userMessage' => 'Awesome!',
			'developerMessage' => 'Oh cool!',
			'validationErrors' => $validation_errors
		);
		
		$test_limit = 100;
		
		$test_paging_params = array(
			$this->Controller->modelClass => array(
				'limit' => $test_limit,
			)
		);

		$this->Api->Controller->request->params['paging'] = $test_paging_params;
		
		$this->Api->Controller->request->params['action'] = 'add_multiple';
		
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->Controller->viewVars = array();
		
		$this->Api->parseViewVars();

		$expected = array(
			'data' => array_merge(
				$test_special_view_vars,
				array(
					'limit' => $test_limit
				)
			),
			'_serialize' => 'data'
		);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Parse View Vars - Validation Errors On Single Actions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsValidationErrorsOnSingleActions() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
		
		$validation_errors = array(
			array(
				'attribute1' => array(
					'Error 1'
				)
			)
		);
		
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with()
			->will($this->returnValue($validation_errors));
			
		$test_special_view_vars = array(
			'status' => 403,
			'code' => 4003,
			'userMessage' => 'Awesome!',
			'developerMessage' => 'Oh cool!',
			'validationErrors' => $validation_errors,
		);
		
		$this->Api->Controller->request->params['action'] = 'add';
		
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->Controller->viewVars = array();
		
		$this->Api->parseViewVars();

		$expected = array(
			'data' => $test_special_view_vars,
			'_serialize' => 'data'
		);
		
		$expected['data']['validationErrors'] = array_shift($validation_errors);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Parse View Vars - Count Action
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsCountAction() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
			
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with();

		$test_special_view_vars = array(
			'status' => 200,
			'code' => 2000
		);

		$count = 999;
		
		$this->Api->Controller->viewVars['totalCount'] = $count;
	
		$this->Api->Controller->action = 'count';
			
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->parseViewVars();

		$expected = array(
			'data' => array_merge(
				$test_special_view_vars,
				array(
					'totalCount' => $count
				)
			),
			'_serialize' => 'data'
		);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Parse View Vars - Count Action With Limit As PHP_INT_MAX
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testParseViewVarsCountActionWithLimitAsPHPIntMax() {
		
		$this->Api->Resource = $this->getMock(
			'Resource',
			array('forModel', 'hasValidationErrors')
		);
			
		$this->Api->Resource
			->expects($this->once())
			->method('hasValidationErrors')
			->with();

		$test_special_view_vars = array(
			'status' => 200,
			'code' => 2000
		);

		$count = 101;
		
		$test_paging_params = array(
			$this->Controller->modelClass => array(
				'limit' => PHP_INT_MAX,
				'count' => $count
			)
		);

		$this->Api->Controller->request->params['paging'] = $test_paging_params;
		
		$this->Api->Controller->action = 'index';
			
		$this->Api->specialViewVars($test_special_view_vars);
		
		$this->Api->parseViewVars();

		$expected = array(
			'data' => array_merge(
				$test_special_view_vars,
				array(
					'totalCount' => $count,
					'limit' => $count
				)
			),
			'_serialize' => 'data'
		);
		
		$results = $this->Api->Controller->viewVars;
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Is Single Action
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsSingleAction() {
		
		$this->Api->Controller->request->params['action'] = '';
		$this->assertFalse($this->Api->isSingleAction());
		
		$this->Api->Controller->request->params['action'] = 'add_multiple';
		$this->assertFalse($this->Api->isSingleAction());
		
		$this->Api->Controller->request->params['action'] = 'add';
		$this->assertTrue($this->Api->isSingleAction());
		
		$this->Api->Controller->request->params['action'] = 'edit';
		$this->assertTrue($this->Api->isSingleAction());
	}
	
	/**
	 * Test Format Csv Response
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatCsvResponse() {
		
		$test_data_value = uniqid();
		
		$data = array(
			array(
				'id' => 1,
				'metadata' => array(
					'width' => 100,
					'height' => 100
				),
				'settings' => array(
					'level1' => array(
						'level2' => 'value'
					)
				)
			),
			array(
				'id' => 2,
				'metadata' => array(
					'width' => 100,
					'height' => 100
				),
				'settings' => array(
					'level1' => array(
						'level2' => array('value1', 'value2')
					)
				)
			)
		);
		
		$flattened = array(
			array(
				'id' => 1,
				'metadata.width' => 100,
				'metadata.height' => 100,
				'settings.level1.level2' => 'value'
			),
			array(
				'id' => 2,
				'metadata.width' => 100,
				'metadata.height' => 100,
				'settings.level1.level2' => '["value1","value2"]'
			)
		);
		
		$this->Api->Controller->viewVars = $data;
		
		$this->Api->formatCsvResponse();
		
		$results = $this->Api->Controller->viewVars;
		
		$expected = array(
			'data' => $flattened,
			'_header' => array_keys($flattened[0]),
			'_serialize' => 'data'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Format Csv Response - With Single Response
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatCsvResponseWithSingleResponse() {
		
		$test_data_value = uniqid();
		
		$data = array(
			'id' => 1,
			'metadata' => array(
				'width' => 100,
				'height' => 100
			),
			'settings' => array(
				'level1' => array(
					'level2' => array('value1', 'value2')
				)
			)
		);
		
		$flattened = array(
			array(
				'id' => 1,
				'metadata.width' => 100,
				'metadata.height' => 100,
				'settings.level1.level2' => '["value1","value2"]'
			)
		);
		
		$this->Api->Controller->viewVars = $data;
		
		$this->Api->formatCsvResponse();
		
		$results = $this->Api->Controller->viewVars;
		
		$expected = array(
			'data' => $flattened,
			'_header' => array_keys($flattened[0]),
			'_serialize' => 'data'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Format Csv Response - With Empty Response
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatCsvResponseWithEmptyResponse() {
		
		$test_data_value = uniqid();
		
		$data = array();
		
		$this->Api->Controller->viewVars = $data;
		
		$this->Api->formatCsvResponse();
		
		$results = $this->Api->Controller->viewVars;
		
		$expected = array(
			'data' => array(0 => array()),
			'_header' => array(),
			'_serialize' => 'data'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Format Xml Response
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatXmlResponse() {
		
		$test_data_value = uniqid();
		
		$this->Api->Controller->viewVars = array('data'=>$test_data_value);
		
		$this->Api->formatXmlResponse();
		
		$results = $this->Api->Controller->viewVars;
		
		$expected = array(
			'data' => array(
				'response' => $test_data_value
			),
			'_serialize' => 'data'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Format Json Reponse
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatJsonResponse() {
		
		$this->Api = $this->getMock(
			'ApiComponent',
			array('setJsonPrettyPrint', 'formatJsonpResponse'),
			array($this->ComponentCollection)
		);
		
		$this->Api
			->expects($this->once())
			->method('setJsonPrettyPrint')
			->with();
			
		$this->Api
			->expects($this->once())
			->method('formatJsonpResponse')
			->with();
			
		$this->Api->formatJsonResponse();
		
	}
	
	/**
	 * Test Set Json Pretty Print - If Requested
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetJsonPrettyPrintIfRequested() {
		
		Configure::write('jsonPretty', false);
			
		$this->Api->Controller->request
			->expects($this->once())
			->method('query')
			->with('pretty')
			->will($this->returnValue(true));
		
		$this->Api->setJsonPrettyPrint();
		
		$this->assertTrue(Configure::read('jsonPretty'));
		
	}
	
	/**
	 * Test Set Json Pretty Print - Not Requested
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetJsonPrettyPrintNotRequested() {
		
		Configure::write('jsonPretty', false);
		
		$this->Api->Controller->request
			->expects($this->once())
			->method('query')
			->with('pretty')
			->will($this->returnValue(false));
						
		$this->Api->setJsonPrettyPrint();
		
		$this->assertFalse(Configure::read('jsonPretty'));
		
	}
	
	/**
	 * Test Format Jsonp Response - With Empty Query Array
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatJsonpResponseWithEmptyQueryArray() {
		
		$this->Api->Controller->request->query = array();
		
		$this->Api
			->Controller
			->expects($this->never())
			->method('set');
		
		$this->Api->formatJsonpResponse();
		
	}
	
	/**
	 * Test Format Jsonp Response - With Valid Callback
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatJsonpResponseWithValidCallback() {
		
		$this->Api->Controller->request->query = array('callback'=>'callback');
		
		$this->Api
			->Controller
			->expects($this->once())
			->method('set')
			->with('callbackFunc', 'callback');
			
		$this->Api->formatJsonpResponse();
		
	}
	
	/**
	 * Test Format Jsonp Response - Prevented Request
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFormatJsonpResponsePreventedRequest() {
		
		$message = 'Prevented request. Your callback is vulnerable to XSS attacks.';
		
		$this->Api = $this->getMock(
			'ApiComponent',
			array('_abort'),
			array($this->ComponentCollection)
		);
		
		$this->Api
			->Controller
			->expects($this->once())
			->method('_abort')
			->with($this->equalTo($message))
			->will($this->returnValue($message));
			
		$this->Api->Controller->request->query = array('callback'=>'$callback');
		
		$this->Api
			->Controller
			->expects($this->never())
			->method('set');
			
		$this->assertEquals(
			$message,
			$this->Api->formatJsonpResponse()
		);
		
	}
	
}
