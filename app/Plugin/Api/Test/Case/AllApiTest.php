<?php
/**
 * Custom Test Suite to Execute All Tests
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
 * @package     Api
 * @subpackage  Api.Test.Case
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class AllApiTest extends PHPUnit_Framework_TestSuite {
	
	public static function suite() {

		$path = APP . 'Plugin' . DS . 'Api' . DS . 'Test' . DS . 'Case' . DS;
		
		$suite = new CakeTestSuite('All tests');
		
		$suite->addTestDirectoryRecursive($path);
		
		return $suite;

    }

}
