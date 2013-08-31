<?php
App::uses('ApiCustomHeadersDispatcher', 'Api.Lib' . DS . 'Routing' . DS . 'Filter');
App::uses('CakeEvent', 'Event');

/**
 * Api Custom Headers Dispatcher Cake Response Class
 *
 */
class ApiCustomHeadersDispatcherCakeResponse {
	public $_headers = array();
	public function header($header) {
		$this->_headers[] = $header;
	}
}

/**
 * Api Custom Headers Dispatcher Test
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
class ApiCustomHeadersDispatcherTest extends CakeTestCase {
	
	/**
	 * Setup
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->ApiCustomHeadersDispatcher = new ApiCustomHeadersDispatcher();
		
	}

	/**
	 * Teardown
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function tearDown() {
	
		unset($this->ApiCustomHeadersDispatcher);
		
		parent::tearDown();
	
	}
	
	/**
	 * Test Instance Setup
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testInstanceSetup() {
		
		$this->assertInstanceOf('DispatcherFilter', $this->ApiCustomHeadersDispatcher);
		
		$this->assertEquals($this->ApiCustomHeadersDispatcher->priority, 1);
		
	}
	
	/**
	 * Test After Dispatch - With X-Response-Time
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testBeforeDispatchWithXResponseTime() {
		
		if (!defined('X_RESPONSE_TIME')) {
			define('X_RESPONSE_TIME', true);
		}
		
		if (!defined('TIME_START')) {
			define('TIME_START', microtime(true));
		}
		
		$this->CakeEvent = $this->getMock('CakeEvent', array(), array(
			'Test',
			null,
			array(
				'request' => $this->getMock('CakeRequest', array(
					'is'
				)),
				'response' => new ApiCustomHeadersDispatcherCakeResponse()
			)
		), '', true);
		
		$result = $this->ApiCustomHeadersDispatcher->afterDispatch($this->CakeEvent);
		
		$this->assertRegExp(
			'/^X-Response-Time: [0-9(.{1})]/',
			$result->_headers[0]
		);
		
	}
	
}
