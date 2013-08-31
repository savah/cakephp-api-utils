<?php
App::uses('OAuth2AppModel', 'OAuth2.Model');
App::uses('OAuth2RefreshToken', 'OAuth2.Model');

/**
 * OAuth2 Refresh Token Model Tests
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
class OAuth2RefreshTokenTest extends CakeTestCase {

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
	 * @since   1.0
	 * @return  void
	 */
	public function testInstanceSetup() {
		
		$this->OAuth2RefreshToken = new OAuth2RefreshToken();
		
		// Test Required Model
		
		$this->assertEquals(
			'oauth2_refresh_tokens',
			$this->OAuth2RefreshToken->useTable
		);
		
		// Test Behaviors
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Hash',
			$this->OAuth2RefreshToken->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array('refresh_token')
			),
			$this->OAuth2RefreshToken->actsAs['OAuth2.OAuth2Hash']
		);
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Authorization',
			$this->OAuth2RefreshToken->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array(
					'client_id' => 'oauth2_client_id',
					'user_id' => 'user_id',
					'scope' => 'scope'
				)
			),
			$this->OAuth2RefreshToken->actsAs['OAuth2.OAuth2Authorization']
		);
		
		// Test Belongs To Associations
		
		$this->assertArrayHasKey(
			'OAuth2Client',
			$this->OAuth2RefreshToken->belongsTo
		);
		
		$this->assertEquals(
			'OAuth2.OAuth2Client',
			$this->OAuth2RefreshToken->belongsTo['OAuth2Client']['className']
		);
		
		$this->assertEquals(
			'oauth2_client_id',
			$this->OAuth2RefreshToken->belongsTo['OAuth2Client']['foreignKey']
		);

	}
	
	/**
	 * Test Get Refresh Token - Empty Api Key
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testGetRefreshTokenEmptyApiKey() {
		
		$refresh_token = uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array('find')
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2RefreshToken->alias . '.refresh_token' => $refresh_token
			),
			'fields' => array(
				'user_id',
				'expires',
				'scope'
			),
			'contain' => array(
				'OAuth2Client' => array(
					'fields' => array(
						'api_key'
					)
				)
			)
		);
		
		$find_results = false;
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
			
		$this->assertNull($this->OAuth2RefreshToken->getRefreshToken($refresh_token));
		
	}
	
	/**
	 * Test Get Refresh Token
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testGetRefreshToken() {
		
		$refresh_token = uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array('find')
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2RefreshToken->alias . '.refresh_token' => $refresh_token
			),
			'fields' => array(
				'user_id',
				'expires',
				'scope'
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
			$this->OAuth2RefreshToken->alias => array(
				'id' => 1,
				'refresh_token' => $refresh_token,
				'user_id' => 1,
				'expires' => strtotime('+1 hour'),
				'scope' => 'root'
			)
		);
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
			
		$expected = array(
			'refresh_token' => $find_results[$this->OAuth2RefreshToken->alias]['refresh_token'],
			'client_id' => $find_results['OAuth2Client']['api_key'],
			'user_id' => $find_results[$this->OAuth2RefreshToken->alias]['user_id'],
			'expires' => $find_results[$this->OAuth2RefreshToken->alias]['expires'],
			'scope' => $find_results[$this->OAuth2RefreshToken->alias]['scope']
		);
		
		$this->assertEquals(
			$expected,
			$this->OAuth2RefreshToken->getRefreshToken($refresh_token)
		);
		
	}
	
	/**
	 * Test Set Refresh Token - Empty Client Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetRefreshTokenEmptyClientId() {
		
		$refresh_token
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2RefreshToken = new OAuth2RefreshToken();
		
		$this->OAuth2RefreshToken->OAuth2Client = $this->getMock(
			'OAuth2RefreshToken',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id
		);
		
		$field_results = false;
		
		$this->OAuth2RefreshToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
			
		$this->assertFalse($this->OAuth2RefreshToken->setRefreshToken(
			$refresh_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Refresh Token - Save Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetRefreshTokenSaveFail() {
		
		$refresh_token
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2RefreshToken->OAuth2Client = $this->getMock(
			'OAuth2RefreshToken',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id
		);
		
		$field_results = $client_id;
		
		$this->OAuth2RefreshToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('create')
			->with(false)
			->will($this->returnValue(true));
			
		$save_options = array(
			$this->OAuth2RefreshToken->alias => array(
				'refresh_token' => $refresh_token,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$save_results = false;
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue($save_results));
			
		$this->assertFalse($this->OAuth2RefreshToken->setRefreshToken(
			$refresh_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Refresh Token - Save Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetRefreshTokenSaveSuccess() {
		
		$refresh_token
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2RefreshToken->OAuth2Client = $this->getMock(
			'OAuth2RefreshToken',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id
		);
		
		$field_results = $client_id;
		
		$this->OAuth2RefreshToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('create')
			->with(false)
			->will($this->returnValue(true));
			
		$save_options = array(
			$this->OAuth2RefreshToken->alias => array(
				'refresh_token' => $refresh_token,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$save_results = true;
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue($save_results));
			
		$this->assertTrue($this->OAuth2RefreshToken->setRefreshToken(
			$refresh_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Unset Refresh Token - Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testUnsetRefreshTokenFail() {
		
		$refresh_token = uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array('updateAll')
		);
		
		$update_all_arg1 = array(
			$this->OAuth2RefreshToken->alias . '.expires' => 0,
			$this->OAuth2RefreshToken->alias . '.modified' => "'" . date('Y-m-d H:i:s') . "'"
		);
		
		$update_all_arg2 = array(
			$this->OAuth2RefreshToken->alias . '.refresh_token' => $refresh_token
		);
		
		$update_results = false;
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('updateAll')
			->with($update_all_arg1, $update_all_arg2)
			->will($this->returnValue($update_results));
			
		$this->assertFalse($this->OAuth2RefreshToken->unsetRefreshToken($refresh_token));
		
	}

	/**
	 * Test Unset Refresh Token - Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testUnsetRefreshTokenSuccess() {
		
		$refresh_token = uniqid();
		
		$this->OAuth2RefreshToken = $this->getMock(
			'OAuth2RefreshToken',
			array('updateAll')
		);
		
		$update_all_arg1 = array(
			$this->OAuth2RefreshToken->alias . '.expires' => 0,
			$this->OAuth2RefreshToken->alias . '.modified' => "'" . date('Y-m-d H:i:s') . "'"
		);
		
		$update_all_arg2 = array(
			$this->OAuth2RefreshToken->alias . '.refresh_token' => $refresh_token
		);
		
		$update_results = true;
		
		$this->OAuth2RefreshToken
			->expects($this->once())
			->method('updateAll')
			->with($update_all_arg1, $update_all_arg2)
			->will($this->returnValue($update_results));
			
		$this->assertTrue($this->OAuth2RefreshToken->unsetRefreshToken($refresh_token));
		
	}
	
}
