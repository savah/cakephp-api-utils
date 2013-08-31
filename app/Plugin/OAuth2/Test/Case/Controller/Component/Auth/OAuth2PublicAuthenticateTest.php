<?php
App::uses('OAuth2PublicAuthenticate', 'OAuth2.Controller' . DS . 'Component' . DS . 'Auth');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('AppTestCase', 'Test' . DS . 'Lib');

/**
 * OAuth2PublicAuthenticate Test
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
 * @package     OAuth2
 * @subpackage  OAuth2.Test.Case.Controller.Component.Auth
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2PublicAuthenticateTest extends CakeTestCase {

	public $fixtures = array();

	/**
	 * setup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return void
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->Collection = $this->getMock('ComponentCollection');
		$this->auth = $this->getMock('OAuth2PublicAuthenticate', null, array(
			$this->Collection,
			array()
		));
		$this->request = $this->getMock('CakeRequest');
		$this->response = $this->getMock('CakeResponse');
		
	}
	
	/**
	 * Test `authenticate`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testAuthenticate() {
		
		$result = $this->auth->authenticate($this->request, $this->response);
		
		$expected = array(
			'id' => 'public',
			'admin_level' => 'public',
			'Role' => array(
				'id' => 'public',
				'slug' => 'public',
				'name' => 'Public'
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test `getUser`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetUser() {
		
		$result = $this->auth->getUser($this->request);
		
		$expected = array(
			'id' => 'public',
			'admin_level' => 'public',
			'Role' => array(
				'id' => 'public',
				'slug' => 'public',
				'name' => 'Public'
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
}
