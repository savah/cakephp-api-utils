<?php
/**
 * Bootstrap Tests
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
class BootstrapTest extends CakeTestCase {
	
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
	 * Test OAuth2 Server PHP Autoloader - Registered
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   0.1
	 * @return  void
	 */
	public function testOAuth2ServerPHPAutoLoader() {

		$registered = false;
		
		foreach (spl_autoload_functions() as $function) {
			
			if (
				is_array($function) && 
				!empty($function[0]) &&
				is_object($function[0]) && 
				$function[0] instanceof OAuth2_Autoloader &&
				!empty($function[1]) &&
				$function[1] == 'autoload'
			) {
				$registered = true;
				break;
			}
			
		}
		
		$this->assertTrue($registered, 'OAuth2 Autoloader is not registered');
		
	}
	
}
