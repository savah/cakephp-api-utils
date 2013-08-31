<?php
/**
 * Custom test suite to execute all tests
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
class AllOAuth2Test extends PHPUnit_Framework_TestSuite {
	
	public static function suite() {

		$path = APP . 'Plugin' . DS . 'OAuth2' . DS . 'Test' . DS . 'Case' . DS;
		
		$suite = new CakeTestSuite('All tests');
		
		$suite->addTestDirectoryRecursive($path);
		
		return $suite;

    }

}
