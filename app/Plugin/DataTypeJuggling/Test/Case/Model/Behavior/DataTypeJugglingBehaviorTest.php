<?php
App::uses('AppModel', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('CakeTime', 'Utility');
App::uses('DataTypeJugglingBehavior', 'DataTypeJuggling.Model' . DS . 'Behavior');

/**
 * Test Model
 *
 */
if (!class_exists('TestModel')) {
	class TestModel extends AppModel {}
}

/**
 * Data Type Juggling Behavior Test
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
 * @package     DataTypeJuggling
 * @subpackage  DataTypeJuggling.Test.Case.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class DataTypeJugglingBehaviorTest extends CakeTestCase {
	
	/**
	 * Setup
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->TestModel = $this->getMock('TestModel', array('getColumnType'));
		
		$this->DataTypeJuggling = new DataTypeJugglingBehavior();
		
		$this->DataTypeJuggling->setup($this->TestModel);
		
	}
	
	/**
	 * Tear Down
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function tearDown () {
		
		parent::tearDown();

		unset($this->DataTypeJuggling);
		
		unset($this->TestModel);
		
		ClassRegistry::flush();
		
	}
	
	/**
	 * Test Before Validate - Empty Data
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeValidateEmptyData() {
	
		$this->TestModel->data = array();
		
		$this->assertTrue($this->DataTypeJuggling->beforeValidate($this->TestModel));
		
		$this->assertEquals(array(), $this->TestModel->data);
		
	}
	
	/**
	 * Test Before Validate - Results
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeValidate() {
		
		$data = array(
			'is_active' => 'true',
			'name' => 'text',
			'is_bool' => '0',
			'is_null' => 'null'
		);
		
		$this->TestModel
			->expects($this->at(0))
			->method('getColumnType')
			->with('is_active')
			->will($this->returnValue('boolean'));
			
		$this->TestModel
			->expects($this->at(1))
			->method('getColumnType')
			->with('name')
			->will($this->returnValue('string'));
			
		$this->TestModel
			->expects($this->at(2))
			->method('getColumnType')
			->with('is_bool')
			->will($this->returnValue('boolean'));
			
		$this->TestModel->data[$this->TestModel->alias] = $data;
		
		$this->assertTrue($this->DataTypeJuggling->beforeValidate($this->TestModel));
		
		$expected = array(
			'is_active' => true,
			'name' => 'text',
			'is_bool' => false,
			'is_null' => null
		);
		
		$this->assertEquals($expected, $this->TestModel->data[$this->TestModel->alias]);
		
	}
	
	/**
	 * Test Before Save - Empty Data
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeSaveEmptyData() {
	
		$this->TestModel->data = array();
		
		$this->assertTrue($this->DataTypeJuggling->beforeSave($this->TestModel));
		
		$this->assertEquals(array(), $this->TestModel->data);
		
	}
	
	/**
	 * Test Before Save - Results
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeSave() {
		
		$data = array(
			'is_active' => 'true',
			'name' => 'text',
			'is_bool' => '0',
			'is_null' => 'null'
		);
		
		$this->TestModel
			->expects($this->at(0))
			->method('getColumnType')
			->with('is_active')
			->will($this->returnValue('boolean'));
			
		$this->TestModel
			->expects($this->at(1))
			->method('getColumnType')
			->with('name')
			->will($this->returnValue('string'));
			
		$this->TestModel
			->expects($this->at(2))
			->method('getColumnType')
			->with('is_bool')
			->will($this->returnValue('boolean'));
			
		$this->TestModel->data[$this->TestModel->alias] = $data;
		
		$this->assertTrue($this->DataTypeJuggling->beforeSave($this->TestModel));
		
		$expected = array(
			'is_active' => 1,
			'name' => 'text',
			'is_bool' => 0,
			'is_null' => null
		);
		
		$this->assertInternalType('integer', $this->TestModel->data[$this->TestModel->alias]['is_active']);
		$this->assertInternalType('string', $this->TestModel->data[$this->TestModel->alias]['name']);
		$this->assertInternalType('integer', $this->TestModel->data[$this->TestModel->alias]['is_bool']);

		$this->assertEquals($expected, $this->TestModel->data[$this->TestModel->alias]);
		
	}

	/**
	 * Test Before Find - With Parse Types Off
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeFindWithParseTypesOff() {
		
		$query = array(
			'conditions' => array(
				'User.id' => 1,
				'User.is_active' => 'true'
			)
		);
		
		$this->assertEquals(
			$query,
			$this->DataTypeJuggling->beforeFind($this->TestModel, $query)
		);
		
	}
	
	/**
	 * Test Before Find - Parse Types On
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testBeforeFindParseTypesOn() {
		
		$query = array(
			'conditions' => array(
				'User.id' => array(1, 2, 3),
				'User.is_active' => 'true'
			),
			'parseTypes' => true
		);
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with('is_active')
			->will($this->returnValue('boolean'));
			
		$result = $this->DataTypeJuggling->beforeFind($this->TestModel, $query);
		
		$expected = array(
			'conditions' => array(
				'User.id' => array(1, 2, 3),
				'User.is_active' => true
			),
			'parseTypes' => true
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test After Find - Not Primary
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindNotPrimary() {
		
		$data = array($this->TestModel->alias => array('field' => 'value'));
		
		$results = $this->DataTypeJuggling->afterFind($this->TestModel, $data, false);
		
		$this->assertEquals($data, $results);
		
	}

	/**
	 * Test After Find - Empty Data
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindEmptyData() {
		
		$data = array($this->TestModel->alias => array());
		
		$results = $this->DataTypeJuggling->afterFind($this->TestModel, $data, true);
		
		$this->assertEquals($data, $results);
		
	}
	
	/**
	 * Test After Find - Test Array Value
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindArrayValue() {
		
		$results = array(
			array(
				$this->TestModel->alias => array(
					'test_array' => array()
				)
			)
		);
		
		$this->assertEquals($results, $this->DataTypeJuggling->afterFind($this->TestModel, $results, true));
		
	}
	
	/**
	 * Test After Find - Test Null Value
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindNullValue() {
		
		$results = array(
			array(
				$this->TestModel->alias => array(
					'test_null' => null
				)
			)
		);
		
		$this->assertEquals($results, $this->DataTypeJuggling->afterFind($this->TestModel, $results, true));
		
	}
	
	/**
	 * Test After Find - Test Boolean Value
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindBooleanValue() {
		
		$results = array(
			array(
				$this->TestModel->alias => array(
					'test_boolean' => 1
				)
			)
		);
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with('test_boolean')
			->will($this->returnValue('boolean'));
		
		$expected = array(
			array(
				$this->TestModel->alias => array(
					'test_boolean' => true
				)
			)
		);
		
		$this->assertEquals(
			$expected,
			$this->DataTypeJuggling->afterFind($this->TestModel, $results, true)
		);
		
	}
	
	/**
	 * Test After Find - Test String Value
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindStringValue() {
		
		$results = array(
			array(
				$this->TestModel->alias => array(
					'test_string' => 'text'
				)
			)
		);
		
		$this->TestModel
			->expects($this->at(0))
			->method('getColumnType')
			->with('test_string')
			->will($this->returnValue('text'));
			
		$this->assertEquals(
			$results,
			$this->DataTypeJuggling->afterFind($this->TestModel, $results, true)
		);
		
	}
	
	/**
	 * Test After Find - Test Integer Value
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testAfterFindIntegerValue() {
		
		$results = array(
			array(
				$this->TestModel->alias => array(
					'test_numeric' => 100
				)
			)
		);
		
		$this->TestModel
			->expects($this->at(0))
			->method('getColumnType')
			->with('test_numeric')
			->will($this->returnValue('integer'));
			
		$this->assertEquals(
			$results,
			$this->DataTypeJuggling->afterFind($this->TestModel, $results, true)
		);
		
	}
	
	/**
	 * Test Convert Data Type - Empty Type
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertDataTypeEmptyType() {
		
		$data = 1;
		
		$type = null;
		
		$this->assertEquals(
			$data,
			$this->DataTypeJuggling->convertDataType($this->TestModel, $data, $type)
		);
		
	}
	
	/**
	 * Test Convert Data Type - Many Tests
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertDataType() {
		
		// Integers
		$this->assertEquals(1, $this->DataTypeJuggling->convertDataType($this->TestModel, '1', 'int'));
		$this->assertInternalType('integer', $this->DataTypeJuggling->convertDataType($this->TestModel, '1', 'int'));
		
		$this->assertNull($this->DataTypeJuggling->convertDataType($this->TestModel, null, 1));
		$this->assertInternalType('null', $this->DataTypeJuggling->convertDataType($this->TestModel, null, 'int'));

		$this->assertEquals(1, $this->DataTypeJuggling->convertDataType($this->TestModel, 1, 'int'));
		$this->assertInternalType('integer', $this->DataTypeJuggling->convertDataType($this->TestModel, 1, 'int'));

		$this->assertEquals(2, $this->DataTypeJuggling->convertDataType($this->TestModel, 2, 'integer'));
		$this->assertInternalType('integer', $this->DataTypeJuggling->convertDataType($this->TestModel, 2, 'integer'));


		// Booleans
		$this->assertEquals(true, $this->DataTypeJuggling->convertDataType($this->TestModel, 'true', 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, 'true', 'boolean'));

		$this->assertEquals(true, $this->DataTypeJuggling->convertDataType($this->TestModel, '1', 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, '1', 'boolean'));
		
		$this->assertEquals(true, $this->DataTypeJuggling->convertDataType($this->TestModel, 1, 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, 1, 'boolean'));

		$this->assertEquals(false, $this->DataTypeJuggling->convertDataType($this->TestModel, 9, 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, 9, 'boolean'));

		$this->assertEquals(false, $this->DataTypeJuggling->convertDataType($this->TestModel, 'false', 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, 'true', 'boolean'));

		$this->assertEquals(false, $this->DataTypeJuggling->convertDataType($this->TestModel, '0', 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, '1', 'boolean'));
		
		$this->assertEquals(false, $this->DataTypeJuggling->convertDataType($this->TestModel, 0, 'boolean'));
		$this->assertInternalType('boolean', $this->DataTypeJuggling->convertDataType($this->TestModel, 1, 'boolean'));
		
		
		// Strings
		$this->assertEquals(null, $this->DataTypeJuggling->convertDataType($this->TestModel, 0, 'string'));
		$this->assertInternalType('null', $this->DataTypeJuggling->convertDataType($this->TestModel, 0, 'string'));

		$this->assertEquals(null, $this->DataTypeJuggling->convertDataType($this->TestModel, false, 'string'));
		$this->assertInternalType('null', $this->DataTypeJuggling->convertDataType($this->TestModel, false, 'string'));
		
		$this->assertEquals(null, $this->DataTypeJuggling->convertDataType($this->TestModel, null, 'string'));
		$this->assertInternalType('null', $this->DataTypeJuggling->convertDataType($this->TestModel, null, 'string'));
		
		$this->assertEquals('test', $this->DataTypeJuggling->convertDataType($this->TestModel, 'test', 'string'));
		$this->assertInternalType('string', $this->DataTypeJuggling->convertDataType($this->TestModel, 'test', 'string'));
		
		
	}
	
	/**
	 * Test Is Boolean - Column Is Boolean
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsBooleanColumnIsBoolean() {
		
		$column = 'columnName';
		
		$type = 'boolean';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isBoolean($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is Boolean - Column Is Other Type Than Boolean
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsBooleanColumnIsOtherThanBoolean() {
		
		$column = 'columnName';
		
		$type = 'other';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertFalse($this->DataTypeJuggling->isBoolean($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is String - Column Is String
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsStringColumnString() {
		
		$column = 'columnName';
		
		$type = 'string';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isString($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is String - Column Is Text
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsStringColumnIsText() {
		
		$column = 'columnName';
		
		$type = 'text';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isString($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is String - Column Is Binary
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsStringColumnIsBinary() {
		
		$column = 'columnName';
		
		$type = 'binary';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isString($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is String - Column Is Other Than String, Text or Binary
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsStringColumnIsOther() {
		
		$column = 'columnName';
		
		$type = 'other';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertFalse($this->DataTypeJuggling->isString($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is Numeric - Column Is Integer
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsNumericColumnIsInteger() {
		
		$column = 'columnName';
		
		$type = 'integer';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isNumeric($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is Numeric - Column Is Biginteger
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsNumericColumnIsBigInteger() {
		
		$column = 'columnName';
		
		$type = 'biginteger';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertTrue($this->DataTypeJuggling->isNumeric($this->TestModel, $column));
		
	}
	
	/**
	 * Test Is Numeric - Column Is Other Than Int or Bigint
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testIsNumericColumnIsOtherThanIntOrBigint() {
		
		$column = 'columnName';
		
		$type = 'other';
		
		$this->TestModel
			->expects($this->once())
			->method('getColumnType')
			->with($column)
			->will($this->returnValue($type));
			
		$this->assertFalse($this->DataTypeJuggling->isNumeric($this->TestModel, $column));
		
	}
	
	/**
	 * Test Convert To Boolean
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertToBoolean() {
		
		$tests = array('1', 1, 'true', true);
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToBoolean($this->TestModel, $test);
			$this->assertTrue($result);
			$this->assertInternalType('boolean', $result);
		}
		
		$tests = array(0, 2, null, '', array(), new stdClass);
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToBoolean($this->TestModel, $test);
			$this->assertFalse($result);
			$this->assertInternalType('boolean', $result);
		}
		
	}
	
	/**
	 * Test Convert To Integer
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertToInteger() {
		
		$tests = array('1', 'true', 1, true, 2, new stdClass);
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToInteger($this->TestModel, $test);
			$this->assertEquals(1, $result);
			$this->assertInternalType('integer', $result);
		}
		
		$tests = array('false', false, 0, null, '', array());
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToInteger($this->TestModel, $test);
			$this->assertEquals(0, $result);
			$this->assertInternalType('integer', $result);
		}
		
	}
	
	/**
	 * Test Convert To Integer Except Null
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertToIntegerExceptNull() {
		
		$test = null;
		
		$result = $this->DataTypeJuggling->convertToIntegerExceptNull($this->TestModel, $test);
		$this->assertEquals(null, $result);
		$this->assertInternalType('null', $result);
		
		$tests = array('1', 1, '2', 2);
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToIntegerExceptNull($this->TestModel, $test);
			$this->assertEquals($test, $result);
			$this->assertInternalType('integer', $result);
		}
		
		$tests = array('1', 'true', 1, '1.0', 1.0, true, new stdClass());
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToIntegerExceptNull($this->TestModel, $test);
			$this->assertEquals(1, $result);
			$this->assertInternalType('integer', $result);
		}
		
		$tests = array('false', false, 0, '0.01', 0.01, '', array());
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToIntegerExceptNull($this->TestModel, $test);
			$this->assertEquals(0, $result);
			$this->assertInternalType('integer', $result);
		}
		
	}
	
	/**
	 * Test Convert To String
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertToString() {
		
		$tests = array(0, '', false, null, array());
		
		foreach($tests as $test) {
			$result = $this->DataTypeJuggling->convertToString($this->TestModel, $test);
			$this->assertNull($result);
			$this->assertInternalType('null', $result);
		}

		$tests = array('test', '0', 1, 2, new stdClass);

		foreach($tests as $test) {
			$this->assertEquals($test, $this->DataTypeJuggling->convertToString($this->TestModel, $test));
		}
		
	}
	
	/**
	 * Test Convert To Null
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertToNull() {
		
		$result = $this->DataTypeJuggling->convertToNull($this->TestModel, null);
		$this->assertNull($result);
		$this->assertInternalType('null', $result);
		
		$result = $this->DataTypeJuggling->convertToNull($this->TestModel, '');
		$this->assertNull($result);
		$this->assertInternalType('null', $result);
		
		$result = $this->DataTypeJuggling->convertToNull($this->TestModel, 1);
		$this->assertEquals(1, $result);
		$this->assertInternalType('integer', $result);
		
		$result = $this->DataTypeJuggling->convertToNull($this->TestModel, true);
		$this->assertEquals(true, $result);
		$this->assertInternalType('boolean', $result);
		
		$result = $this->DataTypeJuggling->convertToNull($this->TestModel, 'test');
		$this->assertEquals('test', $result);
		$this->assertInternalType('string', $result);
		
		
	}
	
	
}
