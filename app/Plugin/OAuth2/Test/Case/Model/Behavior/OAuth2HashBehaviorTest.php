<?php
App::uses('AppModel', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('OAuth2HashBehavior', 'OAuth2.Model/Behavior');

/**
 * OAuth2 Hash Behavior Double
 */
class OAuth2HashBehaviorDouble extends OAuth2HashBehavior {
	
	public $_settings = array();
	
}

/**
 * OAuth2 Hash Behavior Test Model
 */
class OAuth2HashBehaviorTestModel extends AppModel {}

/**
 * OAuth2 Hash Behavior Tests
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
 * @subpackage  OAuth2.Test.Case.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2HashBehaviorTest extends CakeTestCase {

	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function setUp() {

		parent::setUp();
		
		$this->Model = $this->getMock('OAuth2HashBehaviorTestModel');

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

		unset($this->OAuth2HashBehavior);
		
		unset($this->Model);
		
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
			'fields' => array(
				'password'
			)
		);
		
		$this->OAuth2HashBehavior = new OAuth2HashBehaviorDouble();
		
		$this->OAuth2HashBehavior->setup($this->Model, $settings);
		
		$this->assertEquals(
			$settings['fields'],
			$this->OAuth2HashBehavior->_settings[$this->Model->alias]['fields']
		);

	}

	/**
	 * Test OAuth2 Hash - SHA512
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testOAuth2HashSHA512() {
		
		$string = 'test';
		
		$this->OAuth2HashBehavior = new OAuth2HashBehavior();
		
		$this->OAuth2HashBehavior->setup($this->Model);
		
		$results = $this->OAuth2HashBehavior->oAuth2Hash(
			$this->Model,
			$string
		);
		
		$string_and_salt = Configure::read('Security.salt') . $string;
		
		$expected = null;
		
		if (function_exists('mhash')) {
			$expected = bin2hex(mhash(MHASH_SHA512, $string_and_salt));
		}
		
		if (function_exists('hash')) {
			$expected = hash('sha512', $string_and_salt);
		}
		
		$this->assertEquals($expected, $results);
		
	}

	/**
	 * Test OAuth2 Hash - Other (MD5)
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testOAuth2HashOther() {
		
		$string = 'test';
		
		$settings = array('type' => 'md5');
		
		$this->OAuth2HashBehavior = new OAuth2HashBehavior();
		
		$this->OAuth2HashBehavior->setup($this->Model, $settings);
		
		$results = $this->OAuth2HashBehavior->oAuth2Hash(
			$this->Model,
			$string
		);
		
		$expected = Security::hash($string, $settings['type'], true);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Before Save
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testBeforeSave() {
		
		$settings = array(
			'type' => 'md5',
			'fields' => array(
				'password',
				'some_hash'
			)
		);
		
		$data = array(
			$this->Model->alias => array(
				'password' => uniqid(),
				'some_hash' => uniqid()
			)
		);

		$this->Model->data = $data;
		
		$options = array();
		
		$this->OAuth2HashBehavior = new OAuth2HashBehavior();
		
		// Somehow `$this->Model` is converted to an array and becomes a problem
		// to compare the params for `$this->oAuth2Hash()`
		
		// $this->OAuth2HashBehavior = $this->getMock(
		// 	'OAuth2HashBehavior',
		// 	array('oAuth2Hash')
		// );
		// 
		// $this->OAuth2HashBehavior
		// 	->expects($this->at(0))
		// 	->method('oAuth2Hash')
		// 	->with(
		// 		$this->Model,
		// 		$data[$this->Model->alias]['password']
		// 	)
		// 	->will(
		// 		$this->returnValue(md5($data[$this->Model->alias]['password']))
		// 	);
		// 
		// $this->OAuth2HashBehavior
		// 	->expects($this->at(1))
		// 	->method('oAuth2Hash')
		// 	->with(
		// 		$this->Model,
		// 		$data[$this->Model->alias]['some_hash']
		// 	)
		// 	->will(
		// 		$this->returnValue(md5($data[$this->Model->alias]['some_hash']))
		// 	);
			
		$this->OAuth2HashBehavior->setup($this->Model, $settings);
		
		$this->assertTrue(
			$this->OAuth2HashBehavior->beforeSave($this->Model, $options)
		);
		
		$expected = Security::hash($data[$this->Model->alias]['password'], 'md5', true);
		
		$this->assertEquals($expected, $this->Model->data[$this->Model->alias]['password']);
		
		$expected = Security::hash($data[$this->Model->alias]['some_hash'], 'md5', true);
		
		$this->assertEquals($expected, $this->Model->data[$this->Model->alias]['some_hash']);
		
	}
	
	/**
	 * Test Before Find
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testBeforeFind() {
		
		$settings = array(
			'type' => 'md5',
			'fields' => array(
				'password',
				'some_hash'
			)
		);
		
		$query_data = array(
			'conditions' => array(
				'password' => uniqid(),
				$this->Model->alias .'.some_hash' => uniqid()
			)
		);

		$this->OAuth2HashBehavior = new OAuth2HashBehavior();
		
		$this->OAuth2HashBehavior->setup($this->Model, $settings);
		
		$results = $this->OAuth2HashBehavior->beforeFind($this->Model, $query_data);
		
		$expected = Security::hash($query_data['conditions']['password'], 'md5', true);
		
		$this->assertEquals($expected, $results['conditions']['password']);
		
		$expected = Security::hash($query_data['conditions'][$this->Model->alias .'.some_hash'], 'md5', true);

		$this->assertEquals($expected, $results['conditions'][$this->Model->alias .'.some_hash']);
		
	}
	
	
}
