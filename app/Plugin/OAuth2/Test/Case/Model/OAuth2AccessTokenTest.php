<?php
App::uses('OAuth2AppModel', 'OAuth2.Model');
App::uses('OAuth2Client', 'OAuth2.Model');
App::uses('OAuth2AccessToken', 'OAuth2.Model');

/**
 * OAuth2 Access Token Model Tests
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
class OAuth2AccessTokenTest extends CakeTestCase {

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

		$this->OAuth2AccessToken = new OAuth2AccessToken();
		
		// Test Required Model
		
		$this->assertEquals(
			'oauth2_access_tokens',
			$this->OAuth2AccessToken->useTable
		);
		
		// Test Behaviors
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Hash',
			$this->OAuth2AccessToken->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array('access_token')
			),
			$this->OAuth2AccessToken->actsAs['OAuth2.OAuth2Hash']
		);
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Authorization',
			$this->OAuth2AccessToken->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array(
					'client_id' => 'oauth2_client_id',
					'user_id' => 'user_id',
					'scope' => 'scope'
				)
			),
			$this->OAuth2AccessToken->actsAs['OAuth2.OAuth2Authorization']
		);
		
		// Test Belongs To Associations
		
		$this->assertArrayHasKey(
			'OAuth2Client',
			$this->OAuth2AccessToken->belongsTo
		);
		
		$this->assertEquals(
			'OAuth2.OAuth2Client',
			$this->OAuth2AccessToken->belongsTo['OAuth2Client']['className']
		);
		
		$this->assertEquals(
			'oauth2_client_id',
			$this->OAuth2AccessToken->belongsTo['OAuth2Client']['foreignKey']
		);
		
	}
	
	/**
	 * Test Get Access Token - Empty Api Key
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetAccessTokenEmptyApiKey() {
		
		$oauth_token = uniqid();
		
		$this->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array(
				'find'
			)
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2AccessToken->alias . '.access_token' => $oauth_token
			),
			'fields' => array(
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
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue(false));
			
		$this->assertNull($this->OAuth2AccessToken->getAccessToken($oauth_token));
		
	}
	
	/**
	 * Test Get Access Token
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetAccessToken() {
		
		$oauth_token = uniqid();
		
		$this->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array(
				'find'
			)
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2AccessToken->alias . '.access_token' => $oauth_token
			),
			'fields' => array(
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
			$this->OAuth2AccessToken->alias => array(
				'expires' => strtotime('+1 hour'),
				'scope' => 'admin'
			)
		);
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
			
		$expected = array(
			'client_id' => $find_results['OAuth2Client']['api_key'],
			'expires' => $find_results[$this->OAuth2AccessToken->alias]['expires'],
			'scope' => $find_results[$this->OAuth2AccessToken->alias]['scope']
		);
		
		$this->assertEquals(
			$expected,
			$this->OAuth2AccessToken->getAccessToken($oauth_token)
		);
		
	}
	
	/**
	 * Test Set Access Token - Empty Client Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAccessTokenEmptyClientId() {
		
		$oauth_token 
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AccessToken = new OAuth2AccessToken();
		
		$this->OAuth2AccessToken->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AccessToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue(null));
		
		$this->assertFalse($this->OAuth2AccessToken->setAccessToken(
			$oauth_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Access Token - Save Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAccessTokenSaveFail() {
		
		$oauth_token 
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2AccessToken->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AccessToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue($client_id));
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('create')
			->with(false);

		$save_options = array(
			$this->OAuth2AccessToken->alias => array(
				'access_token' => $oauth_token,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue(false));
			
		$this->assertFalse($this->OAuth2AccessToken->setAccessToken(
			$oauth_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}
	
	/**
	 * Test Set Access Token - Save Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSetAccessTokenSaveSuccess() {
		
		$oauth_token 
			= $client_id
			= $user_id
			= $expires
			= $scope
			= uniqid();
		
		$this->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array(
				'create',
				'save'
			)
		);
		
		$this->OAuth2AccessToken->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'field'
			)
		);
		
		$this->OAuth2AccessToken->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', array('api_key' => $client_id))
			->will($this->returnValue($client_id));
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('create')
			->with(false);

		$save_options = array(
			$this->OAuth2AccessToken->alias => array(
				'access_token' => $oauth_token,
				'oauth2_client_id' => $client_id,
				'user_id' => $user_id,
				'expires' => $expires,
				'scope' => $scope
			)
		);
		
		$this->OAuth2AccessToken
			->expects($this->once())
			->method('save')
			->with($save_options)
			->will($this->returnValue(true));
			
		$this->assertTrue($this->OAuth2AccessToken->setAccessToken(
			$oauth_token,
			$client_id,
			$user_id,
			$expires,
			$scope
		));
		
	}

}
