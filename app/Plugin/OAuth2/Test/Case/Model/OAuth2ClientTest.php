<?php
App::uses('OAuth2AppModel', 'OAuth2.Model');
App::uses('OAuth2AuthCode', 'OAuth2.Model');
App::uses('OAuth2AccessToken', 'OAuth2.Model');
App::uses('OAuth2RefreshToken', 'OAuth2.Model');
App::uses('OAuth2Authorization', 'OAuth2.Model');
App::uses('OAuth2Client', 'OAuth2.Model');

/**
 * OAuth2 Client Model Tests
 *
 * @todo This is actually hiting the DB, in the validation tests
 * would be nice to make the mocks to avoid it.
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
class OAuth2ClientTest extends CakeTestCase {

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

		$this->OAuth2Client = new OAuth2Client();
		
		// Test Required Model
		
		$this->assertEquals(
			'oauth2_clients',
			$this->OAuth2Client->useTable
		);
		
		// Test Behaviors
		
		$this->assertArrayHasKey(
			'OAuth2.OAuth2Hash',
			$this->OAuth2Client->actsAs
		);
		
		$this->assertEquals(
			array(
				'fields' => array('api_secret')
			),
			$this->OAuth2Client->actsAs['OAuth2.OAuth2Hash']
		);
		
		// Test Has Many Associations
		
		$associations = array(
			'OAuth2Authorization',
			'OAuth2AuthCode',
			'OAuth2AccessToken',
			'OAuth2RefreshToken'
		);
		
		foreach ($associations as $association) {
			
			$this->assertArrayHasKey(
				$association,
				$this->OAuth2Client->hasMany
			);

			$this->assertEquals(
				'OAuth2.'. $association,
				$this->OAuth2Client->hasMany[$association]['className']
			);

			$this->assertEquals(
				'oauth2_client_id',
				$this->OAuth2Client->hasMany[$association]['foreignKey']
			);
			
			$this->assertTrue(
				$this->OAuth2Client->hasMany[$association]['dependent']
			);
			
		}
		
		
	}

	/**
	 * Test Validate App Name - Not Empty
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testValidateAppNameNotEmpty() {
		
		$expected = array('Please enter a valid name');
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->data = array(
			$this->OAuth2Client->alias => array(
				
			)
		);
		
		$invalid_fields = $this->OAuth2Client->invalidFields();
		
		$this->assertEquals($expected, $invalid_fields['app_name']);
		
	}
	
	/**
	 * Test Validate App Name - Max Length
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testValidateAppNameMaxLength() {
		
		$expected = array('Name has a max length of 50 characters');
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->data = array(
			$this->OAuth2Client->alias => array(
				'app_name' => str_pad('', 51, 'x')
			)
		);
		
		$invalid_fields = $this->OAuth2Client->invalidFields();
		
		$this->assertEquals($expected, $invalid_fields['app_name']);
		
	}
	
	/**
	 * Test Validate Redirect Url - Not Empty
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testValidateRedirectUrlNotEmpty() {
		
		$expected = array('Please enter a valid redirect URI');
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->data = array(
			$this->OAuth2Client->alias => array(
				
			)
		);
		
		$invalid_fields = $this->OAuth2Client->invalidFields();
		
		$this->assertEquals($expected, $invalid_fields['redirect_uri']);
		
	}
	
	/**
	 * Test Validate Redirect Url - Url
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testValidateRedirectUrlUrl() {
		
		$expected = array('Please enter a valid redirect URI');
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->data = array(
			$this->OAuth2Client->alias => array(
				'redirect_uri' => 'www.domain'
			)
		);
		
		$invalid_fields = $this->OAuth2Client->invalidFields();
		
		$this->assertEquals($expected, $invalid_fields['redirect_uri']);
		
	}
	
	/**
	 * Test Before Save - With Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testBeforeSaveWithId() {
		
		$id = 1;
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'generateRandomString',
				'oAuth2Hash',
				'__parentBeforeSave'
			)
		);
		
		$this->OAuth2Client
			->expects($this->never())
			->method('generateRandomString');
			
		$this->OAuth2Client
			->expects($this->never())
			->method('oAuth2Hash');
		
		$this->OAuth2Client
			->expects($this->once())
			->method('__parentBeforeSave')
			->with(array())
			->will($this->returnValue(true));
		
		$this->OAuth2Client->id = $id;
			
		$this->assertTrue($this->OAuth2Client->beforeSave());
		
	}
	
	/**
	 * Test Before Save - With Empty Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testBeforeSaveWithEmptyId() {
		
		$id = null;
		
		$api_key = uniqid();
		
		$api_secret = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array(
				'generateRandomString',
				'oAuth2Hash',
				'__parentBeforeSave'
			)
		);
		
		$this->OAuth2Client
			->expects($this->at(0))
			->method('generateRandomString')
			->with(36)
			->will($this->returnValue($api_key));
			
		$this->OAuth2Client
			->expects($this->at(1))
			->method('generateRandomString')
			->with(64)
			->will($this->returnValue($api_key));
			
		$this->OAuth2Client
			->expects($this->once())
			->method('oAuth2Hash')
			->with($api_key)
			->will($this->returnValue($api_secret));
		
		$this->OAuth2Client
			->expects($this->once())
			->method('__parentBeforeSave')
			->with(array())
			->will($this->returnValue(true));
		
		$this->OAuth2Client->id = $id;
		
		$this->OAuth2Client->whitelist = array('test');
			
		$this->assertTrue($this->OAuth2Client->beforeSave());
		
		$this->assertEquals(
			$api_key,
			$this->OAuth2Client->data[$this->OAuth2Client->alias]['api_key']
		);
		
		$this->assertEquals(
			$api_key,
			$this->OAuth2Client->__api_secret
		);
		
		$this->assertEquals(
			$api_secret,
			$this->OAuth2Client->data[$this->OAuth2Client->alias]['api_secret']
		);
		
		$this->assertEquals(
			array('test', 'api_key', 'api_secret'),
			$this->OAuth2Client->whitelist
		);
		
	}
	
	/**
	 * Test After Save - With Created And Api Secret Property
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testAfterSaveWithCreatedAndApiSecretProperty() {
		
		$created = true;
		
		$api_secret = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('__parentAfterSave')
		);
		
		$this->OAuth2Client->__api_secret = $api_secret;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('__parentAfterSave')
			->with($created)
			->will($this->returnValue(true));
			
		$this->assertTrue($this->OAuth2Client->afterSave($created));
		
		$this->assertEquals(
			$api_secret,
			$this->OAuth2Client->data[$this->OAuth2Client->alias]['api_secret']
		);
		
	}

	/**
	 * Test After Save - Without Created
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testAfterSaveWithoutCreated() {
		
		$created = true;
		
		$api_secret = null;
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('__parentAfterSave')
		);
		
		$this->OAuth2Client->__api_secret = $api_secret;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('__parentAfterSave')
			->with($created)
			->will($this->returnValue(true));
		
		$this->OAuth2Client->data = array(
			$this->OAuth2Client->alias => array(
				'id' => 1
			)
		);
		
		$this->assertTrue($this->OAuth2Client->afterSave($created));
		
		$this->assertArrayNotHasKey(
			'api_secret',
			$this->OAuth2Client->data[$this->OAuth2Client->alias]
		);
		
	}
	
	/**
	 * Test Get Last Api Secret - Empty
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetLastApiSecretEmpty() {
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->__api_secret = null;
		
		$this->assertNull($this->OAuth2Client->getLastApiSecret());
		
	}

	/**
	 * Test Get Last Api Secret
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetLastApiSecret() {
		
		$api_secret = uniqid();

		$this->OAuth2Client = new OAuth2Client();
		
		$this->OAuth2Client->__api_secret = $api_secret;
		
		$this->assertEquals(
			$api_secret,
			$this->OAuth2Client->getLastApiSecret()
		);
		
	}
	
	/**
	 * Test Generate Random String - Default 36 digits
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGenerateRandomStringDefault() {
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->assertRegExp(
			"/[0-9a-f]{36}/i",
			$this->OAuth2Client->generateRandomString()
		);
		
	}

	/**
	 * Test Generate Random String - With 64 digits
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGenerateRandomStringWith64Digits() {
		
		$this->OAuth2Client = new OAuth2Client();
		
		$this->assertRegExp(
			"/[0-9a-f]{64}/i",
			$this->OAuth2Client->generateRandomString(64)
		);
		
	}
	
	/**
	 * Test Check Client Credentials - With Client Secret Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCheckClientCredentialsWithClientSecretFail() {
		
		$client_id = $client_secret = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id,
			'api_secret' => $client_secret
		);
		
		$field_results = false;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
			
		$this->assertFalse($this->OAuth2Client->checkClientCredentials(
			$client_id,
			$client_secret
		));
		
	}
	
	/**
	 * Test Check Client Credentials - With Client Secret Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCheckClientCredentialsWithClientSecretSuccess() {
		
		$client_id = $client_secret = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id,
			'api_secret' => $client_secret
		);
		
		$field_results = 1;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
			
		$this->assertTrue($this->OAuth2Client->checkClientCredentials(
			$client_id,
			$client_secret
		));
		
	}
	
	/**
	 * Test Check Client Credentials - Without Client Secret
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testCheckClientCredentialsWithoutClientSecret() {
		
		$client_id = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('field')
		);
		
		$field_options = array(
			'api_key' => $client_id
		);
		
		$field_results = 1;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('field')
			->with('id', $field_options)
			->will($this->returnValue($field_results));
			
		$this->assertTrue($this->OAuth2Client->checkClientCredentials(
			$client_id
		));
		
	}
	
	/**
	 * Test Get Client Details - Empty
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientDetailsEmpty() {
		
		$client_id = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('find')
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2Client->alias . '.api_key' => $client_id
			),
			'contain' => false
		);
		
		$find_results = false;
		
		$this->OAuth2Client
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
			
		$this->assertFalse($this->OAuth2Client->getClientDetails(
			$client_id
		));
		
	}
	
	/**
	 * Test Get Client Details
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetClientDetails() {
		
		$client_id = uniqid();
		
		$this->OAuth2Client = $this->getMock(
			'OAuth2Client',
			array('find')
		);
		
		$find_options = array(
			'conditions' => array(
				$this->OAuth2Client->alias . '.api_key' => $client_id
			),
			'contain' => false
		);
		
		$find_results = array(
			$this->OAuth2Client->alias => array(
				'id' => 1,
				'api_key' => uniqid(),
				'user_id' => 1,
				'app_name' => 'Test App',
				'redirect_uri' => 'http://www.wizehive.com/'
			)
		);
		
		$this->OAuth2Client
			->expects($this->once())
			->method('find')
			->with('first', $find_options)
			->will($this->returnValue($find_results));
		
		$expected = array(
			'client_id' => $find_results[$this->OAuth2Client->alias]['api_key'],
			'user_id' => $find_results[$this->OAuth2Client->alias]['user_id'],
			'app_name' => $find_results[$this->OAuth2Client->alias]['app_name'],
			'redirect_uri' => $find_results[$this->OAuth2Client->alias]['redirect_uri']
		);
		
		$this->assertEquals(
			$expected,
			$this->OAuth2Client->getClientDetails($client_id)
		);
		
	}
	
}
