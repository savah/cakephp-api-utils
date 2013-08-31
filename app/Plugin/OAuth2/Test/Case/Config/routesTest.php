<?php
App::uses('CakeRequest', 'Network');
App::uses('CakeRoute', 'Routing/Route');
App::uses('Router', 'Routing');

/**
 * Routes Tests
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
 * @subpackage  OAuth2.Test.Case.Config
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class RoutesTest extends CakeTestCase {

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

	}

	/**
	 * Test OAuth2 Routes - Registered
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testOAuth2RoutesRegistered() {
		
		include APP . 'Plugin' . DS . 'OAuth2' . DS . 'Config' . DS . 'routes.php';
		
		$registered = false;
		
		$defaults = array(
			'controller' => 'OAuth2',
			'plugin' => 'OAuth2',
			'action' => 'index'
		);
		
		$template = '/oauth2/:action/*';
		
		foreach (Router::$routes as $route) {
			
			if (
				!empty($route->defaults) &&
				!empty($route->template) &&
				($route->defaults === $defaults) &&
				($route->template === $template)
			) {
				$registered = true;
				break;
			}
			
		}
		
		$this->assertTrue($registered, 'OAuth2 Routes is not registered');
		
	}
	
}
