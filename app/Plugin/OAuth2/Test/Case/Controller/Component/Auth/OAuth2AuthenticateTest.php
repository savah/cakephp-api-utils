<?php
App::uses('BaseAuthenticate', 'Controller' . DS . 'Component' . DS . 'Auth');
App::uses('Component', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('OAuth2Component', 'OAuth2.Controller' . DS . 'Component');
App::uses('OAuth2AccesToken', 'OAuth2.Model');
App::uses('OAuth2Authenticate', 'OAuth2.Controller' . DS . 'Component' . DS . 'Auth');

/**
 * OAuth2 Authenticate Double
 *
 */
class OAuth2AuthenticateDouble extends OAuth2Authenticate {
	
	public function _abort($message = '') {}
	
}

/**
 * OAuth2 Authenticate Tests
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
 * @subpackage  OAuth2.Test.Case.Component.Auth
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2AuthenticateTest extends CakeTestCase {

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

		$this->OAuth2Authenticate = new OAuth2Authenticate($this->ComponentCollection);
		
		$this->assertEquals($this->Controller, $this->OAuth2Authenticate->Controller);
		$this->assertInstanceOf('OAuth2Component', $this->OAuth2Authenticate->OAuth2Component);
		$this->assertClassHasAttribute('settings', 'OAuth2Authenticate');
	
	}
	
	/**
	 * Test Settings Property
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testSettingsProperty() {
		
		$this->OAuth2Authenticate = new OAuth2Authenticate($this->ComponentCollection);
		
		$settings = array(
			'token_type',
			'access_lifetime',
			'refresh_token_lifetime',
			'www_realm',
			'token_param_name',
			'token_bearer_header_name',
			'enforce_state',
			'allow_implicit',
			'userModel',
			'fields' => array(
				'username',
				'password'
			),
			'scope',
			'recursive',
			'contain'
		);
		
		foreach ($settings as $key => $setting) {
			
			if (is_array($setting)) {
				foreach ($setting as $level2) {
					$this->assertArrayHasKey(
						$level2,
						$this->OAuth2Authenticate->settings[$key]
					);
				}
			} else {
				$this->assertArrayHasKey(
					$setting,
					$this->OAuth2Authenticate->settings
				);
			}
			
		}
		
	}
	
	/**
	 * Test Authenticate
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testAuthenticate() {
		
		$this->OAuth2Authenticate = new OAuth2Authenticate($this->ComponentCollection);
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2Authenticate',
			array('getUser'),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('getUser')
			->with($this->equalTo($this->Controller->request))
			->will($this->returnValue(true));
			
		$this->assertTrue(
			$this->OAuth2Authenticate->authenticate(
				$this->Controller->request,
				$this->Controller->response
			)
		);
		
	}
	
	/**
	 * Test Get User - Verify Access Request Fail
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetUserVerifyAccessRequestFail() {
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2AuthenticateDouble',
			array('_abort'),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array(
				'verifyAccessRequest',
				'getResponse',
				'send'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('verifyAccessRequest')
			->with()
			->will($this->returnValue(false));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('getResponse')
			->with()
			->will($this->returnValue($this->OAuth2Authenticate->OAuth2Component));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('send')
			->with()
			->will($this->returnValue(true));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_abort')
			->with()
			->will($this->returnValue(true));
			
		$this->assertFalse($this->OAuth2Authenticate->getUser($this->Controller->request));
		
	}
	
	/**
	 * Test Get User - Empty Access Token
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetUserEmptyAccessToken() {
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2AuthenticateDouble',
			array('_abort'),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array(
				'verifyAccessRequest',
				'errorResponse',
				'send'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component->Request = $this->getMock('OAuth2_Request');
		
		$this->OAuth2Authenticate->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array('field')
		);

		$this->OAuth2Authenticate->Bearer = $this->getMock(
			'OAuth2_TokenType_Bearer',
			array('getAccessTokenParameter'),
			array(array($this->OAuth2Authenticate->OAuth2Component->Request))
		);
		
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('verifyAccessRequest')
			->with()
			->will($this->returnValue(true));
			
		$this->OAuth2Authenticate->Bearer
			->expects($this->once())
			->method('getAccessTokenParameter')
			->with($this->OAuth2Authenticate->OAuth2Component->Request)
			->will($this->returnValue(null));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('errorResponse')
			->with(400, 'invalid_request', 'Token not found')
			->will($this->returnValue($this->OAuth2Authenticate->OAuth2Component));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_abort')
			->with()
			->will($this->returnValue(true));
			
		$this->assertFalse($this->OAuth2Authenticate->getUser($this->Controller->request));
			
	}
	
	/**
	 * Test Get User - Empty User Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetUserEmptyUserId() {
		
		$access_token = String::uuid();
		
		$user_id = null;
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2AuthenticateDouble',
			array('_abort'),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array(
				'verifyAccessRequest',
				'errorResponse',
				'send'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component->Request = $this->getMock('OAuth2_Request');
		
		$this->OAuth2Authenticate->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array('field')
		);

		$this->OAuth2Authenticate->Bearer = $this->getMock(
			'OAuth2_TokenType_Bearer',
			array('getAccessTokenParameter'),
			array(array($this->OAuth2Authenticate->OAuth2Component->Request))
		);
		
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('verifyAccessRequest')
			->with()
			->will($this->returnValue(true));
			
		$this->OAuth2Authenticate->Bearer
			->expects($this->once())
			->method('getAccessTokenParameter')
			->with($this->OAuth2Authenticate->OAuth2Component->Request)
			->will($this->returnValue($access_token));
			
		$this->OAuth2Authenticate->OAuth2AccessToken
			->expects($this->once())
			->method('field')
			->with('user_id', compact('access_token'))
			->will($this->returnValue($user_id));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('errorResponse')
			->with(400, 'invalid_request', 'User not found')
			->will($this->returnValue($this->OAuth2Authenticate->OAuth2Component));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_abort')
			->with()
			->will($this->returnValue(true));
			
		$this->assertFalse($this->OAuth2Authenticate->getUser($this->Controller->request));
		
	}
	
	/**
	 * Test Get User - Empty User
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetUserEmptyUser() {
		
		$access_token = String::uuid();
		
		$user_id = 1;
		
		$user = false;
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2AuthenticateDouble',
			array(
				'_abort',
				'_findUser'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array(
				'verifyAccessRequest',
				'errorResponse',
				'send'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component->Request = $this->getMock('OAuth2_Request');
		
		$this->OAuth2Authenticate->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array('field')
		);

		$this->OAuth2Authenticate->Bearer = $this->getMock(
			'OAuth2_TokenType_Bearer',
			array('getAccessTokenParameter'),
			array(array($this->OAuth2Authenticate->OAuth2Component->Request))
		);
		
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('verifyAccessRequest')
			->with()
			->will($this->returnValue(true));
			
		$this->OAuth2Authenticate->Bearer
			->expects($this->once())
			->method('getAccessTokenParameter')
			->with($this->OAuth2Authenticate->OAuth2Component->Request)
			->will($this->returnValue($access_token));
			
		$this->OAuth2Authenticate->OAuth2AccessToken
			->expects($this->once())
			->method('field')
			->with('user_id', compact('access_token'))
			->will($this->returnValue($user_id));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_findUser')
			->with(array($this->OAuth2Authenticate->settings['userModel'] .'.id' => $user_id))
			->will($this->returnValue($user));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('errorResponse')
			->with(400, 'invalid_request', 'User not found')
			->will($this->returnValue($this->OAuth2Authenticate->OAuth2Component));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_abort')
			->with()
			->will($this->returnValue(true));
			
		$this->assertFalse($this->OAuth2Authenticate->getUser($this->Controller->request));
		
	}
	
	/**
	 * Test Get User - Success
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testGetUserSuccess() {
		
		$access_token = String::uuid();
		
		$user_id = 1;
		
		$user = array(
			'User' => array(
				'id' => $user_id,
				'email' => 'test@wizehive.com',
				'name' => 'Test User'
			)
		);
		
		$this->OAuth2Authenticate = $this->getMock(
			'OAuth2AuthenticateDouble',
			array(
				'_abort',
				'_findUser'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component = $this->getMock(
			'OAuth2Component',
			array(
				'verifyAccessRequest',
				'errorResponse',
				'send'
			),
			array($this->ComponentCollection)
		);
		
		$this->OAuth2Authenticate->OAuth2Component->Request = $this->getMock('OAuth2_Request');
		
		$this->OAuth2Authenticate->OAuth2AccessToken = $this->getMock(
			'OAuth2AccessToken',
			array('field')
		);

		$this->OAuth2Authenticate->Bearer = $this->getMock(
			'OAuth2_TokenType_Bearer',
			array('getAccessTokenParameter'),
			array(array($this->OAuth2Authenticate->OAuth2Component->Request))
		);
		
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->once())
			->method('verifyAccessRequest')
			->with()
			->will($this->returnValue(true));
			
		$this->OAuth2Authenticate->Bearer
			->expects($this->once())
			->method('getAccessTokenParameter')
			->with($this->OAuth2Authenticate->OAuth2Component->Request)
			->will($this->returnValue($access_token));
			
		$this->OAuth2Authenticate->OAuth2AccessToken
			->expects($this->once())
			->method('field')
			->with('user_id', compact('access_token'))
			->will($this->returnValue($user_id));
			
		$this->OAuth2Authenticate
			->expects($this->once())
			->method('_findUser')
			->with(array($this->OAuth2Authenticate->settings['userModel'] .'.id' => $user_id))
			->will($this->returnValue($user));
			
		$this->OAuth2Authenticate->OAuth2Component
			->expects($this->never())
			->method('errorResponse');
			
		$this->OAuth2Authenticate
			->expects($this->never())
			->method('_abort');
			
		$this->assertEquals(
			$user,
			$this->OAuth2Authenticate->getUser($this->Controller->request)
		);
		
	}

}
