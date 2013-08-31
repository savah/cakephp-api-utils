<?php
App::uses('ApiCorsPreflightDispatcher', 'Api.Lib' . DS . 'Routing' . DS . 'Filter');
App::uses('CakeEvent', 'Event');

/**
 * Api Cors Preflight Dispatcher Cake Response Class
 *
 */
class ApiCorsPreflightDispatcherCakeResponse {
	public $_headers = array();
	public function header($header) {
		$this->_headers[] = $header;
	}
}

/**
 * Api Cors Preflight Dispatcher Test
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
 * @subpackage  Api.Test.Case.Lib.Routing.Filter
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiCorsPreflightDispatcherTest extends CakeTestCase {
	
	/**
	 * Setup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->ApiCorsPreflightDispatcher = new ApiCorsPreflightDispatcher();
		
	}

	/**
	 * Teardown
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function tearDown() {
	
		unset($this->ApiCorsPreflightDispatcher);
		
		parent::tearDown();
	
	}
	
	/**
	 * Test Instance Setup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testInstanceSetup() {
		
		$this->assertInstanceOf('DispatcherFilter', $this->ApiCorsPreflightDispatcher);
		
		$this->assertEquals($this->ApiCorsPreflightDispatcher->priority, 1);
		
	}
	
	/**
	 * Test Before Dispatch
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testBeforeDispatch() {
		
		$original_server = $_SERVER;
		
		$_SERVER['HTTP_ORIGIN'] = 'http://www.example.org';
		$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] = 'x-something-custom';
		
		$this->CakeEvent = $this->getMock('CakeEvent', array(), array(
			'Test',
			null,
			array(
				'request' => $this->getMock('CakeRequest', array(
					'is'
				)),
				'response' => new ApiCorsPreflightDispatcherCakeResponse()
			)
		), '', true);
		
		$this->CakeEvent->data['request']
			->expects($this->once())
			->method('is')
			->with($this->equalTo('OPTIONS'))
			->will($this->returnValue(true));
		
		$result = $this->ApiCorsPreflightDispatcher->beforeDispatch($this->CakeEvent);
		
		$_SERVER = $original_server;
		
		$this->assertInstanceOf('ApiCorsPreflightDispatcherCakeResponse', $result);
		
		$caching_policy = defined('ENVIRONMENT') && ENVIRONMENT === 'development' ? 'no-cache' : 'max-age=3600';
		
		$expected = array(
			'Access-Control-Allow-Origin: http://www.example.org',
			'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS',
			'Access-Control-Allow-Headers: x-something-custom',
			'Cache-Control: ' . $caching_policy
		);
		
		$result = $this->CakeEvent->data['response']->_headers;
		
		$this->assertEquals($expected, $result);
		
	}
	
}
