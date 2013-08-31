<?php
App::uses('Component', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('OAuth2Storage', 'OAuth2.Lib');
App::uses('OAuth2Component', 'OAuth2.Controller' . DS . 'Component');

/**
 * OAuth2 Component Tests
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Everton Yoshitani <everton@wizehive.com>
 * @since       0.1
 * @package     OAuth2
 * @subpackage  OAuth2.Test.Case.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2ComponentTest extends CakeTestCase {

	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function setUp() {

		parent::setUp();

		$this->Controller = $this->getMock('Controller');
		
		$this->Controller->request = $this->getMock('CakeRequest');
		
		$this->Controller->response = $this->getMock('CakeResponse');
		
		$this->ComponentCollection = $this->getMock(
			'ComponentCollection',
			array('getController')
		);

		$this->ComponentCollection->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
			
	}

	/**
	 * Tear Down
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
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
	 * @since   0.1
	 * @return  void
	 */
	public function testInstanceSetup() {
		
		$settings = array(
			'token_type' => 'bearer',
			'access_lifetime' => 3600, // 1 hour
			'refresh_token_lifetime' => rand(1209600, 1509600), // this will be tested
			'www_realm' => 'Service',
			'token_param_name' => 'access_token',
			'token_bearer_header_name' => 'Bearer',
			'enforce_state' => true,
			'allow_implicit' => true
		);
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection, $settings);
		
		$this->assertEquals(
			$settings['refresh_token_lifetime'],
			$this->OAuth2Component->settings['refresh_token_lifetime']
		);
		
		foreach (array_keys($settings) as $setting) {
			
			$this->assertArrayHasKey(
				$setting,
				$this->OAuth2Component->settings
			);
			
		}
		
	}
	
	/**
	 * Test Call - With Available Method
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCallWithAvailableMethod() {
		
		$test_method = 'testMethod';
		
		$test_method_arguments = array('arg1', 'arg2');
		
		$test_method_return_value = String::uuid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Server = $this->getMock('OAuth2_Server', array('testMethod'));
		
		$this->OAuth2Component->availableMethods = array($test_method => 'test');
		
		$this->OAuth2Component->Server
			->expects($this->once())
			->method('testMethod')
			->with($test_method_arguments[0], $test_method_arguments[1])
			->will($this->returnValue($test_method_return_value));
			
		$results = $this->OAuth2Component->__call($test_method, $test_method_arguments);
		
		$this->assertEquals($test_method_return_value, $results);
		
	}
	
	/**
	 * Test Call - With Available Method Type `request`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCallWithAvailableMethodTypeRequest() {
		
		$test_method = 'testMethod';
		
		$test_method_arguments = array('arg1', 'arg2');
		
		$test_method_arguments_unshifted = $test_method_arguments;
		
		$test_method_return_value = String::uuid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Server = $this->getMock('OAuth2_Server', array('testMethod'));
		
		$this->OAuth2Component->Request = new OAuth2_Request();
		
		$this->OAuth2Component->availableMethods = array($test_method => 'request');
		
		array_unshift($test_method_arguments_unshifted, $this->OAuth2Component->Request);
		
		$this->OAuth2Component->Server
			->expects($this->once())
			->method('testMethod')
			->with(
				$test_method_arguments_unshifted[0],
				$test_method_arguments_unshifted[1],
				$test_method_arguments_unshifted[2]
			)
			->will($this->returnValue($test_method_return_value));
			
		$results = $this->OAuth2Component->__call($test_method, $test_method_arguments);
		
		$this->assertEquals($test_method_return_value, $results);
		
	}
	
	/**
	 * Test Call - Without An Available Method
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCallWithoutAnAvailableMethod() {
		
		$test_method = 'testMethod';
		
		$test_method_arguments = array('arg1', 'arg2');
		
		$test_method_return_value = String::uuid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Server = $this->getMock('OAuth2_Server', array('testMethod'));
		
		$this->OAuth2Component->Server
			->expects($this->never())
			->method('testMethod');
			
		$this->assertNull($this->OAuth2Component->__call($test_method, $test_method_arguments));
		
	}
	
	/**
	 * Test Initialize
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testInitialize() {
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->initialize($this->Controller);
		
		$availableMethods = array(
			'__construct' => null,
			'getAccessController' => null,
			'getAuthorizeController' => null,
			'getGrantController' => null,
			'getDefaultResponseTypes' => null,
			'getDefaultGrantTypes' => null,
			'handleGrantRequest' => 'request',
			'grantAccessToken' => 'request',
			'getClientCredentials' => 'request',
			'handleAuthorizeRequest' => 'request',
			'validateAuthorizeRequest' => 'request',
			'verifyAccessRequest' => 'request',
			'getAccessTokenData' => 'request',
			'addGrantType' => null,
			'addStorage' => null,
			'addResponseType' => null,
			'setScopeUtil' => null,
			'getResponse' => null
		);
		
		$this->assertEquals(
			$availableMethods,
			$this->OAuth2Component->availableMethods
		);
		
	}
	
	/**
	 * Test Error Response
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testErrorResponse() {
		
		$statusCode = '404';
		$error = 'Test Error';
		$errorDescription = 'Test Error Description';
		$errorUri = 'Test Error Uri';
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$results = $this->OAuth2Component->errorResponse(
			$statusCode,
			$error,
			$errorDescription,
			$errorUri
		);
		
		// Can't use an `assertEquals` the returned object comes with protected attributes
		
		$this->assertObjectHasAttribute('version', $results);
		
		$this->assertObjectHasAttribute('statusCode', $results);
		
		$this->assertObjectHasAttribute('statusText', $results);
		
		$this->assertObjectHasAttribute('parameters', $results);
		
		$this->assertObjectHasAttribute('httpHeaders', $results);
		
	}
	
	/**
	 * Test Get Client Id - With PHP_AUTH_USER
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientIdWithPHP_AUTH_USER() {
		
		$PHP_AUTH_USER = uniqid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Request = $this->getMock(
			'OAuth2_Request',
			array('headers')
		);
		
		$this->OAuth2Component->Request
			->expects($this->exactly(2))
			->method('headers')
			->with('PHP_AUTH_USER')
			->will($this->returnValue($PHP_AUTH_USER));
		
		$results = $this->OAuth2Component->getClientId();
			
		$this->assertEquals($PHP_AUTH_USER, $results);
		
	}
	
	/**
	 * Test Get Client Id - With `client_id` In Request
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientIdWithClientIdInRequest() {
		
		$client_id = uniqid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Request = $this->getMock(
			'OAuth2_Request',
			array(
				'headers',
				'request'
			)
		);
		
		$this->OAuth2Component->Request
			->expects($this->once())
			->method('headers')
			->with('PHP_AUTH_USER')
			->will($this->returnValue(null));

		$this->OAuth2Component->Request
			->expects($this->exactly(2))
			->method('request')
			->with('client_id')
			->will($this->returnValue($client_id));
		
		$results = $this->OAuth2Component->getClientId();
			
		$this->assertEquals($client_id, $results);
		
	}
	
	/**
	 * Test Get Client Id - With `client_id` In Query
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientIdWithClientIdInQuery() {
		
		$client_id = uniqid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Request = $this->getMock(
			'OAuth2_Request',
			array(
				'headers',
				'request',
				'query'
			)
		);
		
		$this->OAuth2Component->Request
			->expects($this->once())
			->method('headers')
			->with('PHP_AUTH_USER')
			->will($this->returnValue(null));

		$this->OAuth2Component->Request
			->expects($this->once())
			->method('request')
			->with('client_id')
			->will($this->returnValue(null));
			
		$this->OAuth2Component->Request
			->expects($this->exactly(2))
			->method('query')
			->with('client_id')
			->will($this->returnValue($client_id));
		
		$results = $this->OAuth2Component->getClientId();
			
		$this->assertEquals($client_id, $results);
		
	}
	
	/**
	 * Test Get Client Id - Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientIdFail() {
		
		$error_response = uniqid();
		
		$this->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array('errorResponse'),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Component->Request = $this->getMock(
			'OAuth2_Request',
			array(
				'headers',
				'request',
				'query'
			)
		);
		
		$this->OAuth2Component->Request
			->expects($this->once())
			->method('headers')
			->with('PHP_AUTH_USER')
			->will($this->returnValue(null));

		$this->OAuth2Component->Request
			->expects($this->once())
			->method('request')
			->with('client_id')
			->will($this->returnValue(null));
			
		$this->OAuth2Component->Request
			->expects($this->once())
			->method('query')
			->with('client_id')
			->will($this->returnValue(null));
		
		$this->OAuth2Component
			->expects($this->once())
			->method('errorResponse')
			->with(
				400,
				'invalid_client',
				'Client credentials were not found in the headers or body'
			)
			->will($this->returnValue($error_response));
			
		$this->assertNull($this->OAuth2Component->getClientId());
		
		$this->assertEquals($error_response, $this->OAuth2Component->getResponse());
		
	}
	
	/**
	 * Test Check Client Credentials - Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCheckClientCredentialsSuccess() {
		
		$client_id = uniqid();
		
		$client_secret = uniqid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Storage = $this->getMock(
			'OAuth2Storage',
			array('checkClientCredentials'),
			array(array())
		);
		
		$this->OAuth2Component->Storage
			->expects($this->once())
			->method('checkClientCredentials')
			->with($client_id, $client_secret)
			->will($this->returnValue(true));
			
		$this->assertTrue($this->OAuth2Component->checkClientCredentials($client_id, $client_secret));
		
	}
	
	/**
	 * Test Check Client Credentials - Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCheckClientCredentialsFail() {
		
		$client_id = uniqid();
		
		$client_secret = uniqid();
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Storage = $this->getMock(
			'OAuth2Storage',
			array('checkClientCredentials'),
			array(array())
		);
		
		$this->OAuth2Component->Storage
			->expects($this->once())
			->method('checkClientCredentials')
			->with($client_id, $client_secret)
			->will($this->returnValue(false));
			
		$this->assertFalse($this->OAuth2Component->checkClientCredentials($client_id, $client_secret));
		
	}
	
	/**
	 * Test Get Response - With `$this->__response` Property
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetResponseWithResponseProperty() {
		
		// Test with `testGetClientIdFail()`
		
	}
	
	/**
	 * Test Get Response - With `$this->Server->getResponse()`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetResponseWithServerGetResponse() {
		
		$test_response = 'Test Response';
		
		$this->OAuth2Component = new OAuth2Component($this->ComponentCollection);
		
		$this->OAuth2Component->Server = $this->getMock(
			'OAuth2_Server',
			array('getResponse')
		);
		
		$this->OAuth2Component->Server
			->expects($this->once())
			->method('getResponse')
			->with()
			->will($this->returnValue($test_response));
			
		$this->assertEquals($test_response, $this->OAuth2Component->getResponse());
		
	}

}
