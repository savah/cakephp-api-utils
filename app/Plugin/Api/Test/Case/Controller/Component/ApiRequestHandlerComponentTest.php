<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('RequestHandlerComponent', 'Controller' . DS . 'Component');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ApiRequestHandlerComponent', 'Api.Controller' . DS . 'Component');

/**
 * Api Request Handler Component Test
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
class ApiRequestHandlerComponentTest extends CakeTestCase {

	/**
	 * Setup
	 *
	 * @author  Mike Carson <mikec@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function setUp() {
		
		$this->Controller = $this->getMock('Controller');
		
		$this->Controller->request = new CakeRequest();
		
		$this->Controller->response = $this->getMock('CakeResponse');
		
		$this->ComponentCollection = $this->getMock('ComponentCollection', array(
			'getController'
		));
		
		$this->ComponentCollection->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
			
		$this->ApiRequestHandler = new ApiRequestHandlerComponent($this->ComponentCollection);
		
	}
	
	/**
	 * Tear Down
	 *
	 * @author  Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function tearDown() {

		parent::tearDown();
		
		ClassRegistry::flush();

	}
	
	/**
	 * The Problem:
	 * 
	 * There is logic in controllers that uses `$this->request->data` 
	 * with `beforeFilter()` callback and CakePHP set `$this->request->data`
	 * with `RequestHandler::startup()` that executes after `beforeFilter()`
	 * this problem occurs when data/payload is sent with `Content-Type: application/json`
	 * 
	 * The Workaround:
	 * 
	 * Execute `RequestHandler::startup()` on `__construct()` for json requests
	 * and by pass it on `startup()`
	 *
	 *
	 * Test `__construct` - Call Parent Startup For Json Requests
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	*/
	public function testCallParentStartupOnConstructForJsonRequests() {
		
		$this->ApiRequestHandler = $this->getMock(
			'ApiRequestHandlerComponent',
			array(
				'__callParentStartup',
				'requestedWith'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiRequestHandler
			->expects($this->once())
			->method('requestedWith')
			->with('json')
			->will($this->returnValue(true));
			
		$this->ApiRequestHandler
			->expects($this->once())
			->method('__callParentStartup')
			->with($this->Controller);
			
		$this->ApiRequestHandler->__construct($this->ComponentCollection);
		
	}
	
	/**
	 * Test `__construct` - Don't Call Parent Startup For Requests Other Than Json
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testDontParentCallStartupOnConstructForRequestsOtherThanJson() {
		
		$this->ApiRequestHandler = $this->getMock(
			'ApiRequestHandlerComponent',
			array(
				'__callParentStartup',
				'requestedWith'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiRequestHandler
			->expects($this->once())
			->method('requestedWith')
			->with('json')
			->will($this->returnValue(false));
			
		$this->ApiRequestHandler
			->expects($this->never())
			->method('__callParentStartup');
			
		$this->ApiRequestHandler->__construct($this->ComponentCollection);
		
	}
	
	/**
	 * Test `startup` - Don't Call Parent Startup For Json Requests
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	*/
	public function testDontCallParentStartupOnStartupForJsonRequests() {
		
		$this->ApiRequestHandler = $this->getMock(
			'ApiRequestHandlerComponent',
			array(
				'__callParentStartup',
				'requestedWith',
				'__processFiles'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiRequestHandler
			->expects($this->once())
			->method('requestedWith')
			->with('json')
			->will($this->returnValue(true));
			
		$this->ApiRequestHandler
			->expects($this->never())
			->method('__callParentStartup');

		$this->ApiRequestHandler
			->expects($this->once())
			->method('__processFiles')
			->with();
			
		$this->ApiRequestHandler->startup($this->Controller);
		
	}
	
	/**
	 * Test Startup - Call Parent Startup For Requests Other Than Json
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	*/
	public function testCallParentStartupOnStartupForRequestsOtherThanJson() {
		
		$this->ApiRequestHandler = $this->getMock(
			'ApiRequestHandlerComponent',
			array(
				'__callParentStartup',
				'requestedWith',
				'__processFiles'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiRequestHandler
			->expects($this->once())
			->method('requestedWith')
			->with('json')
			->will($this->returnValue(false));
			
		$this->ApiRequestHandler
			->expects($this->once())
			->method('__callParentStartup')
			->with($this->Controller);

		$this->ApiRequestHandler
			->expects($this->once())
			->method('__processFiles')
			->with();
			
		$this->ApiRequestHandler->startup($this->Controller);
		
	}
	
	/**
	 * Test Startup - With One File Dimension
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testStartupWithOneFileDimension() {
		
		$_FILES = array(
			'file_key' => array(
				'name' => 'some_name.png',
				'type' => 'image/png',
				'tmp_name' => 'some_tmp_name',
				'error' => 0
			)
		);
		
		$this->Controller->request->data = array();
		
		$this->ApiRequestHandler->startup($this->Controller);
		
		$result = $this->Controller->request->data;
		
		$expected = array(
			'file_key' => array(
				'name' => 'some_name.png',
				'type' => 'image/png',
				'tmp_name' => 'some_tmp_name',
				'error' => 0
			)
		);
		
		$this->assertEquals($expected, $result);
		
		unset($_FILES);
		
	}
	
	/**
	 * Test Startup - With Multiple File Dimensions
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testStartupWithMultipleFileDimensions() {
		
		$_FILES = array(
			'multi' => array(
				'name' => array(
					'dimensional' => array(
						'key1' => 'some_name.png',
						'key2' => 'some_other_name.png'
					)
				),
				'type' => array(
					'dimensional' => array(
						'key1' => 'image/png',
						'key2' => 'image/png'
					)
				),
				'tmp_name' => array(
					'dimensional' => array(
						'key1' => 'some_tmp_name',
						'key2' => 'some_other_tmp_name'
					)
				),
				'error' => array(
					'dimensional' => array(
						'key1' => 0,
						'key2' => 0
					)
				)
			),
			'alternate_multi' => array(
				'name' => array(
					'key3' => 'some_final_name.png'
				),
				'type' => array(
					'key3' => 'image/png'
				),
				'tmp_name' => array(
					'key3' => 'some_final_tmp_name'
				),
				'error' => array(
					'key3' => 0
				)
			)
		);
		
		$this->Controller->request->data = array(
			'existing' => 'data'
		);
		
		$this->ApiRequestHandler->startup($this->Controller);
		
		$result = $this->Controller->request->data;
		
		$expected = array(
			'existing' => 'data',
			'multi' => array(
				'dimensional' => array(
					'key1' => array(
						'name' => 'some_name.png',
						'type' => 'image/png',
						'tmp_name' => 'some_tmp_name',
						'error' => 0
					),
					'key2' => array(
						'name' => 'some_other_name.png',
						'type' => 'image/png',
						'tmp_name' => 'some_other_tmp_name',
						'error' => 0
					)
				)
			),
			'alternate_multi' => array(
				'key3' => array(
					'name' => 'some_final_name.png',
					'type' => 'image/png',
					'tmp_name' => 'some_final_tmp_name',
					'error' => 0
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
		unset($_FILES);
		
	}
	
	/**
	 * Test Get Client IP - Empty
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetClientIPNull() {
		
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$previous_remote_addr = $_SERVER['REMOTE_ADDR'];
		}
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$previous_http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		$_SERVER['REMOTE_ADDR'] = '';
		
		$this->assertEquals('', $this->ApiRequestHandler->getClientIP());
		
		if (!empty($previous_remote_addr)) {
			$_SERVER['REMOTE_ADDR'] = $previous_remote_addr;
		} else {
			unset($_SERVER['REMOTE_ADDR']);
		}
		
		if (!empty($previous_http_x_forwarded_for)) {
			$_SERVER['HTTP_X_FORWARDED_FOR'] = $previous_http_x_forwarded_for;
		}
		
	}
	
	/**
	 * Test Get Client IP
	 *
	 * @author  Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetClientIP() {

		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$previous_remote_addr = $_SERVER['REMOTE_ADDR'];
		}
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$previous_http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		
		$_SERVER['REMOTE_ADDR'] = '127.2.3.4';
		
		$client_ip = $this->ApiRequestHandler->getClientIP();
		
		$this->assertEquals('127.2.3.4', $client_ip);
		
		$_SERVER['REMOTE_ADDR'] = '127.5.6.7 , 127.8.9.0';
		
		$client_ip = $this->ApiRequestHandler->getClientIP();
		
		$this->assertEquals('127.5.6.7', $client_ip);
		
		$_SERVER['REMOTE_ADDR'] = 'unknown, 88.88.88.88';

		$client_ip = $this->ApiRequestHandler->getClientIP();

		$this->assertEquals('88.88.88.88', $client_ip);
		
		unset($_SERVER['REMOTE_ADDR']);
		
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '127.2.3.4';
		
		$client_ip = $this->ApiRequestHandler->getClientIP();
		
		$this->assertEquals('127.2.3.4', $client_ip);
		
		$client_ip = $this->ApiRequestHandler->getClientIP(true);
		
		$this->assertNotEquals('127.2.3.4', $client_ip);

		if (!empty($previous_remote_addr)) {
			$_SERVER['REMOTE_ADDR'] = $previous_remote_addr;
		} else {
			unset($_SERVER['REMOTE_ADDR']);
		}
		
		if (!empty($previous_http_x_forwarded_for)) {
			$_SERVER['HTTP_X_FORWARDED_FOR'] = $previous_http_x_forwarded_for;
		} else {
			unset($_SERVER['HTTP_X_FORWARDED_FOR']);
		}

	}
	
	/**
	 * Test Get Subdomain
	 *
	 * @author  Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetSubdomain() {
		
		// Test 1
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://somerootdomain.com'
		);
		
		$this->assertFalse(
			$subdomain,
			'http://somerootdomain.com should not return a subdomain'
		);
		
		// Test 2
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'ftp://www.somerootdomain.com'
		);
		
		$this->assertFalse(
			$subdomain,
			'ftp://www.somerootdomain.com should not return a subdomain'
		);
		
		// Test 3
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://www.somerootdomain.com'
		);
		
		$this->assertEquals(
			'www',
			$subdomain,
			'http://www.somerootdomain.com should return the subdomain "www"'
		);
		
		// Test 4
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'https://www.somerootdomain.com'
		);
		
		$this->assertEquals(
			'www',
			$subdomain,
			'https://www.somerootdomain.com should return the subdomain "www"'
		);
		
		// Test 5
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://subdomain.somerootdomain.com'
		);
		
		$this->assertEquals(
			'subdomain',
			$subdomain,
			'http://subdomain.somerootdomain.com should return the subdomain "subdomain"'
		);
		
		// Test 6
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://subdomain.somerootdomain.com?some=get.param'
		);
		
		$this->assertEquals(
			'subdomain',
			$subdomain,
			'http://subdomain.somerootdomain.com?some=get.param should return the subdomain "subdomain"'
		);
		
		// Test 7
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://subdomain.somerootdomain.com/directory?some=get.param'
		);
		
		$this->assertEquals(
			'subdomain',
			$subdomain,
			'http://subdomain.somerootdomain.com/directory?some=get.param should return the subdomain "subdomain"'
		);
		
		// Test 8
		$subdomain = $this->ApiRequestHandler->getSubdomain(
			'http://tiered.subdomain.somerootdomain.com/directory?some=get.param'
		);
		
		$this->assertEquals(
			'tiered.subdomain',
			$subdomain, 
			'http://tiered.subdomain.somerootdomain.com/directory?some=get.param should return the subdomain "tiered.subdomain"'
		);
		
		// Test 9 (this could be improved for an exact match)
		$subdomain = $this->ApiRequestHandler->getSubdomain();
		
		if (is_bool($subdomain)) {
			$this->assertFalse($subdomain);
		} else {
			$this->assertContains($subdomain, FULL_BASE_URL);
		}

	}
	
	/**
	 * Test Header
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testHeader() {
		
		$header = 'WWW-Authenticate: Negotiate';
		
		$this->assertNull($this->ApiRequestHandler->header($header));
		
		$set = false;
		
		foreach (headers_list() as $header_set) {
			
			if ($header_set === $header) {
				$set = true;
				break;
			}
			
		}
		
		$this->assertTrue($set);
		
	}

}
