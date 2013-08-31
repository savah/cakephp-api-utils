<?php
App::uses('OAuth2AppModel', 'OAuth2.Model');
App::uses('OAuth2AuthCode', 'OAuth2.Model');

/**
 * OAuth2 Auth Code Model Tests
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
 * @subpackage  OAuth2.Test.Case.Model
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2AuthCodeTest extends CakeTestCase {

	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function setUp() {

		parent::setUp();

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

		$this->OAuth2AuthCode = new OAuth2AuthCode();
		
		// Test Required Model
		
		$this->assertEquals(
			'oauth2_auth_codes',
			$this->OAuth2AuthCode->useTable
		);
		
		// Test Behaviors
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Hash',
			$this->OAuth2AuthCode->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array('auth_code')
			),
			$this->OAuth2AuthCode->actsAs['OAuth2.OAuth2Hash']
		);
		
		// Test Belongs To Associations
		
		$this->assertArrayHasKey(
			'OAuth2Client',
			$this->OAuth2AuthCode->belongsTo
		);
		
		$this->assertEquals(
			'OAuth2.OAuth2Client',
			$this->OAuth2AuthCode->belongsTo['OAuth2Client']['className']
		);
		
		$this->assertEquals(
			'oauth2_client_id',
			$this->OAuth2AuthCode->belongsTo['OAuth2Client']['foreignKey']
		);
		
	}

	/**
	 * Test Get Authorization Code - Empty Api Key
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetAuthorizationCodeEmptyApiKey() {
		
		$code = uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'find'
			)
		);
		
		$find_options = array(
			'conditions' => array(
				'auth_code' => $code
			),
			'fields' => array(
				'redirect_uri',
				'expires',
				'user_id'
			),
			'contain' => array(
				'OAuth2Client' => array(
					'fields' => array(
						'api_key'
					)
				)
			)
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue(false));
			
		$this->assertNull($this->OAuth2AuthCode->getAuthorizationCode($code));
		
	}
	
	/**
	 * Test Get Authorization Code
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetAuthorizationCode() {
		
		$code = uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'find'
			)
		);
		
		$find_options = array(
			'conditions' => array(
				'auth_code' => $code
			),
			'fields' => array(
				'redirect_uri',
				'expires',
				'user_id'
			),
			'contain' => array(
				'OAuth2Client' => array(
					'fields' => array(
						'api_key'
					)
				)
			)
		);
		
		$find_results = array(
			'OAuth2Client' => array(
				'api_key' => uniqid()
			),
			$this->OAuth2AuthCode->alias => array(
				'expires' => strtotime('+1 hour'),
				'scope' => 'admin'
			)
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
			
		$expected = array(
			'client_id' => $find_results['OAuth2Client']['api_key'],
			'expires' => $find_results[$this->OAuth2AuthCode->alias]['expires'],
			'scope' => $find_results[$this->OAuth2AuthCode->alias]['scope']
		);
		
		$this->assertEquals(
			$expected,
			$this->OAuth2AuthCode->getAuthorizationCode($code)
		);
		
	}
	
	/**
	 * Test Set Authorization Code - Empty Client Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAuthorizationCodeEmptyClientId() {
		
		$code
			= $client_id
			= $user_id
			= $redirect_uri
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AuthCode = new OAuth2AuthCode();
		
		$this->OAuth2AuthCode->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AuthCode->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue(null));
		
		$this->assertFalse($this->OAuth2AuthCode->setAuthorizationCode(
			$code,
			$client_id,
			$user_id,
			$redirect_uri,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Authorization Code - Save Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAuthorizationCodeSaveFail() {
		
		$code
			= $client_id
			= $user_id
			= $redirect_uri
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2AuthCode->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AuthCode->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue($client_id));
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('create')
			->with(false);

		$save_options = array(
			$this->OAuth2AuthCode->alias => array(
				'auth_code' => $code,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'redirect_uri' => $redirect_uri,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue(false));
			
		$this->assertFalse($this->OAuth2AuthCode->setAuthorizationCode(
			$code,
			$client_id,
			$user_id,
			$redirect_uri,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Authorization Code - Save Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAuthorizationCodeSaveSuccess() {
		
		$code
			= $client_id
			= $user_id
			= $redirect_uri
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2AuthCode->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AuthCode->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue($client_id));
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('create')
			->with(false);

		$save_options = array(
			$this->OAuth2AuthCode->alias => array(
				'auth_code' => $code,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'redirect_uri' => $redirect_uri,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue(true));
			
		$this->assertTrue($this->OAuth2AuthCode->setAuthorizationCode(
			$code,
			$client_id,
			$user_id,
			$redirect_uri,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Expire Authorization Code - Save Fail
	 *
	 * @todo This test may fail because the `date()` call
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testExpireAuthorizationCodeSaveFail() {
		
		$code = uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'updateAll'
			)
		);
		
		$update_all_options_arg1 = array(
			$this->OAuth2AuthCode->alias . '.expires' => 0,
			$this->OAuth2AuthCode->alias . '.modified' => "'" . date('Y-m-d H:i:s') . "'"
		);
		
		$update_all_options_arg2 = array(
			$this->OAuth2AuthCode->alias . '.auth_code' => $code
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('updateAll')
			->with(
				$update_all_options_arg1,
				$update_all_options_arg2
			)
			->will($this->returnValue(false));
			
		$this->assertFalse($this->OAuth2AuthCode->expireAuthorizationCode($code));
		
	}
	
	/**
	 * Test Expire Authorization Code - Save Success
	 *
	 * @todo This test may fail because the `date()` call
	 *
 	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testExpireAuthorizationCodeSaveSuccess() {
		
		$code = uniqid();
		
		$this->OAuth2AuthCode = $this->getMock(
			'OAuth2AuthCode',
			array(
				'updateAll'
			)
		);
		
		$update_all_options_arg1 = array(
			$this->OAuth2AuthCode->alias . '.expires' => 0,
			$this->OAuth2AuthCode->alias . '.modified' => "'" . date('Y-m-d H:i:s') . "'"
		);
		
		$update_all_options_arg2 = array(
			$this->OAuth2AuthCode->alias . '.auth_code' => $code
		);
		
		$this->OAuth2AuthCode
			->expects($this->once())
			->method('updateAll')
			->with(
				$update_all_options_arg1,
				$update_all_options_arg2
			)
			->will($this->returnValue(true));
			
		$this->assertTrue($this->OAuth2AuthCode->expireAuthorizationCode($code));
		
	}
	
}
