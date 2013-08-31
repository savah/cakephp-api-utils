<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('AppModel', 'Model');
App::uses('ApiQueryComponent', 'Api.Controller' . DS . 'Component');

/**
 * Api Query Component Test Model Double
 *
 */
if (!class_exists('ApiQueryComponentTestModel')) {
	class ApiQueryComponentTestModel extends AppModel {
		public $_attributes = null;
	}
}

/**
 * Api Query Component Double
 *
 */
if (!class_exists('ApiQueryComponentDouble')) {
	class ApiQueryComponentDouble extends ApiQueryComponent {
		public $_model = null;
	}
}

/**
 * ApiQueryComponentThing Model
 *
 */
if (!class_exists('ApiQueryComponentThing')) {
	class ApiQueryComponentThing extends AppModel {}
}

/**
 * ApiQueryComponentStuff Model
 *
 */
if (!class_exists('ApiQueryComponentStuff')) {
	class ApiQueryComponentStuff extends AppModel {}
}

/**
 * Api Query Component Test
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
 * @subpackage  Api.Test.Case.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiQueryComponentTest extends CakeTestCase {
	
	/**
	 * Schema for Testing
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @var 	array
	 */
	public $schema = array(
		'id' => array(
			'type' => 'integer',
			'null' => false,
			'default' => NULL,
			'length' => 11,
			'key' => 'primary'
		),
		'tag' => array(
			'type' => 'string',
			'null' => false,
			'default' => NULL,
			'length' => 255,
			'collate' => 'utf8_general_ci',
			'charset' => 'utf8'
		),
		'last_access' => array(
			'type' => 'timestamp',
			'null' => false,
			'default' => 'CURRENT_TIMESTAMP',
			'length' => NULL
		),
		'created' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'updated' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'blocked_sort_attribute' => array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'blocked_query_attribute' => array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'age' => array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'year' => array(
			'type' => 'integer',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'code' => array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'name' => array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'fullname' => array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'location' => array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'time_since_modified' => array(
			'type' => 'string',
			'null' => true,
			'default' => NULL,
			'length' => NULL
		),
		'is_disabled' => array(
			'type' => 'boolean',
			'null' => true,
			'default' => NULL,
			'length' => 1
		)
	);
	
	/**
	 * Attributes for Testing
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @var 	array
	 */
	public $attributes = array(
		'id' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true,
		),
		'blockedSortAttribute' => array(
			'field' => 'blocked_sort_attribute',
			'type' => 'int',
			'sort' => false,
			'query' => true,
		),
		'blockedQueryAttribute' => array(
			'field' => 'blocked_query_attribute',
			'type' => 'int',
			'sort' => true,
			'query' => false,
		),
		'age' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true,
		),
		'year' => array(
			'type' => 'int',
			'sort' => true,
			'query' => true,
		),
		'code' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'tag' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'name' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'fullname' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'location' => array(
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'timeSinceModified' => array(
			'field' => 'time_since_modified', 
			'type' => 'string',
			'sort' => true,
			'query' => true,
		),
		'isDisabled' => array(
			'field' => 'is_disabled', 
			'type' => 'boolean',
			'sort' => true,
			'query' => true,
		),
		'lastAccess' => array(
			'field' => 'last_access',
			'type' => 'datetime',
			'sort' => true,
			'query' => true,
		),
		'created' => array(
			'type' => 'datetime',
			'sort' => true,
			'query' => true,
		),
		'updated' => array(
			'type' => 'datetime',
			'sort' => true,
			'query' => true,
		)
	);
	
	/**
	 * Field Map for Testing
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $fieldMap = array(
		'id' => 'id',
		'blocked_sort_attribute' => 'blockedSortAttribute',
		'blocked_query_attribute' => 'blockedQueryAttribute',
		'age' => 'age',
		'created' => 'created',
		'tag' => 'tag',
		'name' => 'name',
		'fullname' => 'fullname',
		'year' => 'year',
		'code' => 'code',
		'location' => 'location',
		'last_access' => 'lastAccess',
		'time_since_modified' => 'timeSinceModified',
		'is_disabled' => 'isDisabled'
	);
	
	/**
	 * Setup
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->Controller = $this->getMock('TestController');
		
		$this->Controller->request = $this->getMock('CakeRequest');
		
		$this->Controller->response = $this->getMock('CakeResponse');
		
		$this->ComponentCollection = $this->getMock(
			'ComponentCollection',
			array('getController')
		);
		
		$this->ComponentCollection
			->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
			
		$this->Query = new ApiQueryComponentDouble($this->ComponentCollection);
		
		$this->Query->modelClass = 'Model';
		
		$this->Query->Model = $this->Model = $this->getMock(
			'ApiQueryComponentTestModel',
			array(
				'schema',
				'getOptions',
				'getDefaultAttributes',
				'attributes'
			)
		);
		
		$this->Query->OtherModel = $this->OtherModel = $this->getMock(
			'ApiQueryComponentTestModel',
			array(
				'schema',
				'getOptions',
				'getDefaultAttributes',
				'attributes'
			)
		);
		
		$this->Query->Model
			->expects($this->any())
			->method('schema')
			->will($this->returnValue($this->schema));
		
		$this->Query->OtherModel
			->expects($this->any())
			->method('schema')
			->will($this->returnValue($this->schema));
		
		$this->Query->Model
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue($this->attributes));
		
		$this->Query->OtherModel
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue($this->attributes));
		
	}

	/**
	 * TearDown
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function tearDown() {
	
		parent::tearDown();
		
		ClassRegistry::flush();
		
	}
	
	/**
	 * Instance Setup
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testInstanceSetup() {
		
		$components = Hash::normalize($this->Query->components);
		
		$this->assertArrayHasKey('Auth', $components);
		
		$this->assertInstanceOf('AuthComponent', $this->Query->Auth);
		
	}
	
	/**
	 * Test On Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testOnModel() {
		
		$this->Query->onModel();
		
		$this->assertNull($this->Query->_model);
		
		$results = $this->Query->onModel('Model');
		
		$this->assertEquals('Model', $this->Query->_model);
		
		$this->assertInstanceOf('ApiQueryComponent', $results);
		
	}
	
	/**
	 * Test On Field
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testOnField() {
		
		$this->Query->onField();
		
		$this->assertNull($this->Query->_field);
		
		$results = $this->Query->onField('field');
		
		$this->assertEquals('field', $this->Query->_field);
		
		$this->assertInstanceOf('ApiQueryComponent', $results);
		
	}
	
	/**
	 * Test With Value
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithValue() {
		
		$this->Query->withValue();
		
		$this->assertNull($this->Query->_value);
		
		$results = $this->Query->withValue('value');
		
		$this->assertEquals('value', $this->Query->_value);
		
		$this->assertInstanceOf('ApiQueryComponent', $results);
		
	}
	
	/**
	 * Test With Passed Params
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithPassedParams() {
		
		$this->Query->withPassedParams();
		
		$this->assertEquals(array(), $this->Query->_passed_params);
		
		$results = $this->Query->withPassedParams(array('key' => 'value'));
		
		$this->assertEquals(array('key' => 'value'), $this->Query->_passed_params);
		
		$this->assertInstanceOf('ApiQueryComponent', $results);
		
	}
	
	/**
	 * Test With Field Map
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testWithFieldMap() {
		
		$results = $this->Query->withFieldMap($this->fieldMap);
		
		$this->assertEquals($this->fieldMap, $this->Query->_field_map);
		
	}
	
	/**
	 * Test Prefixes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testPrefixes() {
		
		$result = $this->Query->prefixes();
		
		$expected = array(
			'not',
			'min',
			'max',
			'contains',
			'not-contains',
			'starts-with',
			'ends-with'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix `not-`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixNot() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('not-age' => '18'))
			->rendersConditions();
		
		$expected = array(
			array(
				'OR' => array(
					'OtherModel.age !=' => '18',
					'OtherModel.age' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('not-created' => '2013-01-01'))
			->rendersConditions();
		
		$expected = array(
			array(
				'OR' => array(
					'OtherModel.created !=' => '2013-01-01 00:00:00',
					'OtherModel.created' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
	
	}
	
	/**
	 * Test Renders Conditions - With Prefix `min-`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixMin() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-age' => '18'))
			->rendersConditions();
			
		$expected = array('OtherModel.age >=' => '18');
		
		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-age' => '18'))
			->rendersConditions();
		
		$expected = array('OtherModel.age >=' => '18');
		
		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-created' => '2013-01-01'))
			->rendersConditions();
		
		$expected = array('OtherModel.created >=' => '2013-01-01 00:00:00');
		
		$this->assertEquals($expected, $results);
	
	}
	
	/**
	 * Test Renders Conditions - With Prefix `min-` and an Iso8601 Datetime String
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixMinAndAnISO8601DatetimeString() {
		
		$this->Query->OtherModel->_attributes = array(
			'created' => array(
				'type' => 'datetime'
			)
		);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-created' => '2013-03-01T13:00:00+0000'))
			->rendersConditions();
		
		$expected = array('OtherModel.created >=' => '2013-03-01 13:00:00');
		
		$this->assertEquals($expected, $results);
	
	}
	
	/**
	 * Test Renders Conditions - With Prefix `min-` and an Iso8601 Jst Datetime String
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixMinAndAnISO8601JSTDatetimeString() {
		
		$this->Query->OtherModel->_attributes = array(
			'created' => array(
				'type' => 'datetime'
			)
		);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-created' => '2013-03-05T13:56:02+0900'))
			->rendersConditions();
		
		$expected = array('OtherModel.created >=' => '2013-03-05 04:56:02');
		
		$this->assertEquals($expected, $results);
	
	}
	
	/**
	 * Test Renders Conditions - With Prefix `min-` and an Invalid Iso8601 Datetime String
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixMinAndAnInvalidISO8601DatetimeString() {
		
		$this->Query->OtherModel->_attributes = array(
			'created' => array(
				'type' => 'datetime'
			)
		);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('min-created' => '2013-03-05T13:56'))
			->rendersConditions();
		
		$expected = array('OtherModel.created >=' => '2013-03-05 13:56:00');
		
		$this->assertEquals($expected, $results);
	
	}
	
	/**
	 * Test Renders Conditions - With Prefix `max-`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixMax() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('max-age' => '30'))
			->rendersConditions();
		
		$expected = array('OtherModel.age <=' => '30');
		
		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('max-created' => '2013-01-01'))
			->rendersConditions();
		
		$expected = array('OtherModel.created <=' => '2013-01-01 00:00:00');
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Value Piped (Lists)
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithValuePiped() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('year' => '2012|2013'))
			->rendersConditions();

		$expected = array('OtherModel.year' => array('2012', '2013'));

		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('code' => '"001|ABC"'))
			->rendersConditions();

		$expected = array('OtherModel.code' => '001|ABC');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Simple Key/Value
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithSimpleKeyValue() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('tag' => 'cakephp'))
			->rendersConditions();

		$expected = array('OtherModel.tag' => 'cakephp');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Field Options
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithFieldOptions() {
		
		$this->Query->OtherModel
			->expects($this->at(1))
			->method('getOptions')
			->with($this->equalTo('tag'))
			->will($this->returnValue(array()));
			
		$this->Query->OtherModel
			->expects($this->at(3))
			->method('getOptions')
			->with($this->equalTo('code'))
			->will($this->returnValue(array(1 => 'A')));
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'tag' => 'cakephp',
				'code' => 'A'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.tag' => 'cakephp',
			'OtherModel.code' => 1
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix `contains-`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixContains() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('contains-fullname' => 'John%Doe'))
			->rendersConditions();

		$expected = array('OtherModel.fullname LIKE' => '%John\\\\%Doe%');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix `not-contains-`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixNotContains() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('not-contains-fullname' => 'John%Doe'))
			->rendersConditions();

		$expected = array('OtherModel.fullname NOT LIKE' => '%John\\\\%Doe%');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix `starts-with`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixStartsWith() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('starts-with-location' => 'New York'))
			->rendersConditions();

		$expected = array('OtherModel.location LIKE' => 'New York%');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix `ends-with`
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixEndsWith() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array('ends-with-location' => 'US'))
			->rendersConditions();

		$expected = array('OtherModel.location LIKE' => '%US');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Private Fields Set
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testParseQueyWithPrivateFieldsSet() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'name' => 'John',
				'testPrivateField' => 'test'
			))
			->rendersConditions();

		$expected = array('OtherModel.name' => 'John');

		$this->assertEquals($expected, $results);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'name' => 'John',
				'testPrivateField' => 'test'
			))
			->rendersConditions();

		$expected = array('OtherModel.name' => 'John');

		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix Not And Value Null
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithPrefixNotAndValueNull() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'id' => '',
				'min-age' => '25',
				'max-age' => '28',
				'year' => '2008|2013',
				'code' => '"001|ABC"',
				'tag' => 'cakephp',
				'not-name' => 'null',
				'not-contains-name' => 'Jane',
				'contains-fullname' => 'John%Doe',
				'min-created' => '2013-01-01',
				'starts-with-location' => 'New York',
				'ends-with-location' => 'US'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.id' => false,
			'OtherModel.age >=' => '25',
			'OtherModel.age <=' => '28',
			'OtherModel.year' => array(0 => '2008', 1 => '2013'),
			'OtherModel.code' => '001|ABC',
			'OtherModel.tag' => 'cakephp',
			'OtherModel.name !=' => null,
			'OtherModel.name NOT LIKE' => '%Jane%',
			'OtherModel.fullname LIKE' => '%John\\\\%Doe%',
			'OtherModel.created >=' => '2013-01-01 00:00:00',
			'OtherModel.location LIKE' => 'New York%',
			'OtherModel.location LIKE' => '%US'
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Prefix Not And Value Null
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithNullValue() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'id' => '',
				'code' => 'null',
				'min-age' => '25',
				'max-age' => '28',
				'year' => '2008|2013',
				'tag' => 'cakephp',
				'not-name' => 'null',
				'not-contains-name' => 'Jane',
				'contains-fullname' => 'John%Doe',
				'min-created' => '2013-01-01',
				'starts-with-location' => 'New York',
				'ends-with-location' => 'US'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.id' => false,
			'OtherModel.age >=' => '25',
			'OtherModel.age <=' => '28',
			'OtherModel.year' => array(0 => '2008', 1 => '2013'),
			'OtherModel.tag' => 'cakephp',
			'OtherModel.name !=' => null,
			'OtherModel.name NOT LIKE' => '%Jane%',
			'OtherModel.fullname LIKE' => '%John\\\\%Doe%',
			'OtherModel.created >=' => '2013-01-01 00:00:00',
			'OtherModel.location LIKE' => 'New York%',
			'OtherModel.location LIKE' => '%US',
			array(
				'OR' => array(
					'OtherModel.code' => '',
					'OtherModel.code' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Many Conditions
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithManyConditions() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'id' => '',
				'min-age' => '25',
				'max-age' => '28',
				'year' => '2008|2013',
				'code' => '"001|ABC"',
				'tag' => 'cakephp',
				'not-name' => 'cake',
				'not-contains-name' => 'Jane',
				'contains-fullname' => 'John%Doe',
				'min-created' => '2013-01-01',
				'starts-with-location' => 'New York',
				'ends-with-location' => 'US'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.id' => false,
			'OtherModel.age >=' => '25',
			'OtherModel.age <=' => '28',
			'OtherModel.year' => array(0 => '2008', 1 => '2013'),
			'OtherModel.code' => '001|ABC',
			'OtherModel.tag' => 'cakephp',
			'OtherModel.name NOT LIKE' => '%Jane%',
			'OtherModel.fullname LIKE' => '%John\\\\%Doe%',
			'OtherModel.created >=' => '2013-01-01 00:00:00',
			'OtherModel.location LIKE' => 'New York%',
			'OtherModel.location LIKE' => '%US',
			array(
				'OR' => array(
					'OtherModel.name !=' => 'cake',
					'OtherModel.name' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Blocked Sort Attribute
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithBlockedSortAttribute() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'id' => '',
				'blockedSortAttribute' => 1,
				'min-age' => '25',
				'max-age' => '28',
				'year' => '2008|2013',
				'code' => '"001|ABC"',
				'tag' => 'cakephp',
				'not-name' => 'cake',
				'not-contains-name' => 'Jane',
				'contains-fullname' => 'John%Doe',
				'min-created' => '2013-01-01',
				'starts-with-location' => 'New York',
				'ends-with-location' => 'US'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.id' => false,
			'OtherModel.blocked_sort_attribute' => 1,
			'OtherModel.age >=' => '25',
			'OtherModel.age <=' => '28',
			'OtherModel.year' => array(0 => '2008', 1 => '2013'),
			'OtherModel.code' => '001|ABC',
			'OtherModel.tag' => 'cakephp',
			'OtherModel.name NOT LIKE' => '%Jane%',
			'OtherModel.fullname LIKE' => '%John\\\\%Doe%',
			'OtherModel.created >=' => '2013-01-01 00:00:00',
			'OtherModel.location LIKE' => 'New York%',
			'OtherModel.location LIKE' => '%US',
			array(
				'OR' => array(
					'OtherModel.name !=' => 'cake',
					'OtherModel.name' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Blocked Query Attribute
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWitBlockedQueryAttribute() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'id' => '',
				'blockedQueryAttribute' => 1,
				'min-age' => '25',
				'max-age' => '28',
				'year' => '2008|2013',
				'code' => '"001|ABC"',
				'tag' => 'cakephp',
				'not-name' => 'cake',
				'not-contains-name' => 'Jane',
				'contains-fullname' => 'John%Doe',
				'min-created' => '2013-01-01',
				'starts-with-location' => 'New York',
				'ends-with-location' => 'US'
			))
			->rendersConditions();
		
		$expected = array(
			'OtherModel.id' => false,
			'OtherModel.age >=' => '25',
			'OtherModel.age <=' => '28',
			'OtherModel.year' => array(0 => '2008', 1 => '2013'),
			'OtherModel.code' => '001|ABC',
			'OtherModel.tag' => 'cakephp',
			'OtherModel.name NOT LIKE' => '%Jane%',
			'OtherModel.fullname LIKE' => '%John\\\\%Doe%',
			'OtherModel.created >=' => '2013-01-01 00:00:00',
			'OtherModel.location LIKE' => 'New York%',
			'OtherModel.location LIKE' => '%US',
			array(
				'OR' => array(
					'OtherModel.name !=' => 'cake',
					'OtherModel.name' => null
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Renders Conditions - Without $field_map Param
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithoutFieldMap() {
		
		$results = $this->Query
			->onModel('OtherModel')
			->withPassedParams(array('min-age'=>18))
			->rendersConditions();
			
		$this->assertEquals(array(), $results);
		
	}
	
	/**
	 * Test Renders Conditions - With Attribute and Field Name Variation
	 *
	 * @author 	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRendersConditionsWithAttributeAndFieldNameVariation() {
		
		$expected = array('OtherModel.last_access' => 1356998400);
		
		$results = $this->Query
			->onModel('OtherModel')
			->withFieldMap($this->fieldMap)
			->withPassedParams(array(
				'lastAccess' => '2013-01-01'
			))
			->rendersConditions();
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Requested Relations
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRequestedRelations() {
		
		$this->Controller->request
			->expects($this->any())
			->method('query')
			->with('related')
			->will($this->returnValue('Categories,Details'));
			
		$results = $this->Query->requestedRelations();
		
		$this->assertEquals(array('Categories', 'Details'), $results);
		
	}
	
	/**
	 * Test Requested Relations - Without Related Models
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRequestedRelationsWithoutRelatedModels() {
		
		$this->Query->Controller->request
			->expects($this->any())
			->method('query')
			->with('related')
			->will($this->returnValue(false));
			
		$results = $this->Query->requestedRelations();
		
		$this->assertEquals(array(), $results);
		
	}
	
	/**
	 * Test Requested Attributes - Without Id
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRequestedAttributesWithoutId() {
		
		$this->Query->Controller->request
			->expects($this->any())
			->method('query')
			->with('attributes')
			->will($this->returnValue('att1,att2'));
			
		$results = $this->Query->requestedAttributes();
		
		$this->assertEquals(array('id', 'att1', 'att2'), $results);
		
	}
	
	/**
	 * Test Requested Attributes - With Id
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRequestedAttributesWithId() {
		
		$this->Query->Controller->request
			->expects($this->any())
			->method('query')
			->with('attributes')
			->will($this->returnValue('att1,att2,id'));
			
		$results = $this->Query->requestedAttributes();
		
		$this->assertEquals(array('att1', 'att2', 'id'), $results);
		
	}
	
	/**
	 * Test Requested Attributes - Without Attributes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testRequestedAttributesWithoutAttributes() {
		
		$this->Query->Controller->request
			->expects($this->any())
			->method('query')
			->with('attributes')
			->will($this->returnValue(false));
			
		$results = $this->Query->requestedAttributes();
		
		$this->assertEquals(array(), $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - For Datetime Columns 
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormatedValueForDatetime() {
		
		$field = 'created';
		
		$value = '2013-01-01';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$expected = $value .' 00:00:00';
		
		$this->assertEquals($expected, $results);
		
		$field = 'created';
		
		$value = '2013-01-01 00:00';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - For Timestamp Columns
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueForTimestamp() {
		
		$field = 'last_access';
		
		$value = '2013-01-01';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$expected = strtotime($value);
		
		$this->assertEquals($expected, $results);
		
		$field = 'last_access';
		
		$value = '2013-01-01 00:00:00';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$expected = strtotime($value);
		
		$this->assertEquals($expected, $results);
		
		$field = 'last_access';
		
		$value = '2013-01-01 00:00';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - For String False
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueForStringFalse() {
		
		$field = 'is_disabled';
		
		$value = 'false';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - For String True
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueForStringTrue() {
		
		$field = 'is_disabled';
		
		$value = 'true';
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - With a Nonexistent Field
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormatedValueWithANonexistentField() {
		
		$field = 'this_do_not_exists_in_schema';
		
		$value = uniqid();
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - With a Null Field
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueWithNullField() {
		
		$field = null;
		
		$value = uniqid();
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
			
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - Without a Model Attached to Controller
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueWithoutModelAttachedToController() {
		
		$field = null;
		
		$value = uniqid();
		
		$this->TestController->AppControllerTest = null;
		
		$results = $this->Query
			->onModel('OtherModel')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Returns Formatted Value - Without a Model Schema
	 *
	 * @since 	1.0
	 * @return 	void
	 */
	public function testReturnsFormattedValueWithoutModelSchema() {
		
		$this->Query->Model2 = $this->getMock(
			'ApiQueryComponentTestModel',
			array('schema')
		);
		
		$this->Query->Model2->expects($this->any())
			->method('schema')
			->will($this->returnValue(false));
			
		$field = 'name';
		
		$value = uniqid();
		
		$results = $this->Query
			->onModel('Model2')
			->onField($field)
			->withValue($value)
			->returnsFormattedValue();
		
		$this->assertEquals($value, $results);
		
	}
	
	/**
	 * Test Get Parent
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetParent() {
		
		$this->Query->Controller->request->params = array(
			'some' => 'stuff',
			'other' => 'stuff',
			'parent__ApiQueryComponentStuff' => 1,
			'more' => 'things',
			'parent__ApiQueryComponentThing' => 2,
			'parent__LastApiQueryComponentThing' => 3,
			'final' => 'item'
		);
		
		$result = $this->Query->getParent();
		
		$expected = array(
			'parentModelAlias' => 'LastApiQueryComponentThing',
			'parent_model_id' => 3
		);
		
		$this->assertEquals($expected, $result);
		
		// Now, if we empty out params, the result should still be the same
		// because it's cached in the object
		
		$this->Query->Controller->request->params = array();
		
		$result = $this->Query->getParent();
		
		$expected = array(
			'parentModelAlias' => 'LastApiQueryComponentThing',
			'parent_model_id' => 3
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get From Route - With Primary Has Many
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetFromRouteWithPrimaryHasMany() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'getParent'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query->Controller->request->params = array(
			'modelAlias' => 'ChildApiQueryComponentStuff'
		);
		
		$this->Query->ApiQueryComponentStuff = $this->getMock('ApiQueryComponentStuff', array(
			'getAssociated'
		));
		
		$this->Query->ApiQueryComponentStuff->ParentApiQueryComponentStuffAlias = $this->getMock('ApiQueryComponentStuff', array(
			'find'
		));
		
		$this->Query->ApiQueryComponentStuff->ParentApiQueryComponentStuffAlias->belongsTo['ChildApiQueryComponentStuff'] = array(
			'foreignKey' => 'stuff_id',
			'conditions' => array(
				'ChildApiQueryComponentStuff.name' => 'ABC'
			)
		);
		
		$this->Query
			->expects($this->once())
			->method('getParent')
			->with()
			->will($this->returnValue(array(
				'parentModelAlias' => 'ParentApiQueryComponentStuffAlias',
				'parent_model_id' => 999
			)));
		
		$this->Query->ApiQueryComponentStuff
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ParentApiQueryComponentStuffAlias' => 'hasMany'
			)));
		
		$this->Query->ApiQueryComponentStuff->ParentApiQueryComponentStuffAlias
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('list'),
				$this->equalTo(array(
					'fields' => array('ParentApiQueryComponentStuffAlias.stuff_id'),
					'conditions' => array(
						'ParentApiQueryComponentStuffAlias.id' => 999,
						'ChildApiQueryComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array(1, 2, 3)));
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => '1|2|3');
		
		$this->assertEquals($expected, $result);
		
		// Now, the answer should be cached, and none of those mocks should need to be called again
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => '1|2|3');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get From Route - With Primary Belongs To
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetFromRouteWithPrimaryBelongsTo() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'getParent'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query->Controller->request->params = array(
			'modelAlias' => 'ChildApiQueryComponentStuff'
		);
		
		$this->Query->ApiQueryComponentStuff = $this->getMock('ApiQueryComponentStuff', array(
			'getAssociated',
			'find'
		));
		
		$this->Query->ApiQueryComponentStuff->belongsTo['ParentApiQueryComponentStuffAlias'] = array(
			'foreignKey' => 'stuff_id',
			'conditions' => array(
				'ParentApiQueryComponentStuffAlias.name' => 'ABC'
			)
		);
		
		$this->Query
			->expects($this->once())
			->method('getParent')
			->with()
			->will($this->returnValue(array(
				'parentModelAlias' => 'ParentApiQueryComponentStuffAlias',
				'parent_model_id' => 999
			)));
		
		$this->Query->ApiQueryComponentStuff
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ParentApiQueryComponentStuffAlias' => 'belongsTo'
			)));
		
		$this->Query->ApiQueryComponentStuff
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('list'),
				$this->equalTo(array(
					'fields' => array(
						$this->Query->ApiQueryComponentStuff->alias .'.id'
					),
					'conditions' => array(
						'ApiQueryComponentStuff.stuff_id' => 999,
						'ParentApiQueryComponentStuffAlias.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array(1, 2, 3)));
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => '1|2|3');
		
		$this->assertEquals($expected, $result);
		
		// Now, the answer should be cached, and none of those mocks should need to be called again
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => '1|2|3');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get From Route - With Empty Filter
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetFromRouteWithEmptyFilter() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'getParent'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query->Controller->request->params = array(
			'modelAlias' => 'ChildApiQueryComponentStuff'
		);
		
		$this->Query->ApiQueryComponentStuff = $this->getMock('ApiQueryComponentStuff', array(
			'getAssociated',
			'find'
		));
		
		$this->Query->ApiQueryComponentStuff->belongsTo['ParentApiQueryComponentStuffAlias'] = array(
			'foreignKey' => 'stuff_id',
			'conditions' => array(
				'ParentApiQueryComponentStuffAlias.name' => 'ABC'
			)
		);
		
		$this->Query
			->expects($this->once())
			->method('getParent')
			->with()
			->will($this->returnValue(array(
				'parentModelAlias' => 'ParentApiQueryComponentStuffAlias',
				'parent_model_id' => 999
			)));
		
		$this->Query->ApiQueryComponentStuff
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ParentApiQueryComponentStuffAlias' => 'belongsTo'
			)));
		
		$this->Query->ApiQueryComponentStuff
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('list'),
				$this->equalTo(array(
					'fields' => array(
						$this->Query->ApiQueryComponentStuff->alias .'.id'
					),
					'conditions' => array(
						'ApiQueryComponentStuff.stuff_id' => 999,
						'ParentApiQueryComponentStuffAlias.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array()));
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => null);
		
		$this->assertEquals($expected, $result);
		
		// Now, the answer should be cached, and none of those mocks should need to be called again
		
		$result = $this->Query->onModel('ApiQueryComponentStuff')->getFromRoute();
		
		$expected = array('id' => null);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get Without Routes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetWithoutRoutes() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'name' => 'something'
		);
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->getWithoutRoutes();
		
		$expected = array('name' => 'something');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Params - With Id Query
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParamsWithIdQuery() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'id' => '2|3|4',
			'name' => 'something'
		);
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->integrateRouteParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array(
			'id' => '2|3',
			'name' => 'something'
		);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Integrate Route Params - With Id Query and Passed Id
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParamsWithIdQueryAndPassedId() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'id' => '2|3|4',
			'name' => 'something'
		);
		
		$this->Query->Controller->request->params['id'] = '2';
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->integrateRouteParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array(
			'id' => '2',
			'name' => 'something'
		);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Integrate Route Params - With Id Query and Passed Id and No Matches
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParamsWithIdQueryAndPassedIdAndNoMatches() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'id' => '2|3|4',
			'name' => 'something'
		);
		
		$this->Query->Controller->request->params['id'] = '7';
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->integrateRouteParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array(
			'id' => '',
			'name' => 'something'
		);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Integrate Route Params - With Id Query and No Matches
	 * 
	 * This one is very important. If the foreign constraints filters down the IDs,
	 * and the API consumer also filters down the IDs, then the assumption is that
	 * the user wants the intersection. However, if there is no intersection, then
	 * that should mean *no* matches, not *any* match. This means the `id` query 
	 * must be empty vs. unset.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParamsWithIdQueryAndNoMatches() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'id' => '4|5|6',
			'name' => 'something'
		);
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->integrateRouteParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array(
			'id' => '',
			'name' => 'something'
		);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Integrate Route Params - With No Id Query
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParamsWithNoIdQuery() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'getFromRoute'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('ApiQueryComponentStuff'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->once())
			->method('getFromRoute')
			->with()
			->will($this->returnValue(array('id' => '1|2|3')));
		
		$this->Query->Controller->request->query = array(
			'name' => 'something'
		);
		
		$this->Query->_model = 'ApiQueryComponentStuff';
		
		$result = $this->Query->integrateRouteParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array(
			'id' => '1|2|3',
			'name' => 'something'
		);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Filter Sort Params - With No Sort Param
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFilterSortParamsWithNoSortParam() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->never())
			->method('onModel');
		
		$result = $this->Query->filterSortParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
	}
	
	/**
	 * Test Filter Sort Params - With Single Sort Option
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFilterSortParamsWithSingleSortParam() {
		
		$sort_options = array(
			'sort' => 'timeSinceModified'
		);
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->once())
			->method('onModel')
			->with();
			
		$this->Query->Model = $this->Model;
		
		$this->Query->Model
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($this->attributes));
			
		$this->Query->_model = 'Model';
		
		$this->Query->Controller->request->query = $sort_options;
		
		$result = $this->Query->filterSortParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array($this->attributes[$sort_options['sort']]['field']);
		
		$this->assertEquals($expected, $this->Query->Controller->request->query['sort']);
		
	}
	
	/**
	 * Test Filter Sort Params - With Single Sort Option And Blocked Attribute
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFilterSortParamsWithSingleSortParamAndBlockedAttribute() {
		
		$sort_options = array(
			'sort' => 'blockedSortAttribute'
		);
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->once())
			->method('onModel')
			->with();
			
		$this->Query->Model = $this->Model;
		
		$this->Query->Model
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($this->attributes));
			
		$this->Query->_model = 'Model';
		
		$this->Query->Controller->request->query = $sort_options;
		
		$result = $this->Query->filterSortParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$this->assertArrayNotHasKey('sort', $this->Query->Controller->request->query);
		
	}
	
	/**
	 * Test Filter Sort Params - With Single Sort Option And Blocked Attribute
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFilterSortParamsWithMultipleSortParams() {
		
		$sort_options = array(
			'sort' => array(
				'blockedSortAttribute',
				'timeSinceModified',
				'id'
			)
		);
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->once())
			->method('onModel')
			->with();
			
		$this->Query->Model = $this->Model;
		
		$this->Query->Model
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($this->attributes));
			
		$this->Query->_model = 'Model';
		
		$this->Query->Controller->request->query = $sort_options;
		
		$result = $this->Query->filterSortParams();
		
		$this->assertInstanceOf('ApiQueryComponent', $result);
		
		$expected = array('time_since_modified', 'id');
		
		$this->assertEquals($expected, $this->Query->Controller->request->query['sort']);
		
	}
	
	/**
	 * Test Prepare
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testPrepare() {
		
		$this->Query = $this->getMock(
			'ApiQueryComponentDouble', 
			array(
				'onModel',
				'fixUnderscores',
				'parseAttributesAndRelated',
				'filterSortParams',
				'integrateRouteParams'
			),
			array($this->ComponentCollection)
		);
		
		$this->Query
			->expects($this->at(0))
			->method('onModel')
			->with()
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(1))
			->method('onModel')
			->with($this->equalTo('Model'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(2))
			->method('fixUnderscores')
			->will($this->returnValue($this->Query));
			
		$this->Query
			->expects($this->at(3))
			->method('onModel')
			->with($this->equalTo('Model'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(4))
			->method('parseAttributesAndRelated')
			->will($this->returnValue($this->Query));

		$this->Query
			->expects($this->at(5))
			->method('onModel')
			->with($this->equalTo('Model'))
			->will($this->returnValue($this->Query));
			
		$this->Query
			->expects($this->at(6))
			->method('filterSortParams')
			->will($this->returnValue($this->Query));
			
		$this->Query
			->expects($this->at(7))
			->method('onModel')
			->with($this->equalTo('Model'))
			->will($this->returnValue($this->Query));
		
		$this->Query
			->expects($this->at(8))
			->method('integrateRouteParams')
			->will($this->returnValue($this->Query));
		
		$this->Query->_model = 'Model';
		
		$this->Query->Controller->request->query = array('key' => 'value');
		
		$result = $this->Query->prepare();
		
		$expected = array('key' => 'value');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get Timezone - Not Passed In Query String
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneNotPassedInQueryString() {
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}
	
	/**
	 * Test Get Timezone - Value Neither True Or String
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValueIsNotString() {
		
		$timezone_test = array(1);
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());

		$timezone_test = true;
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}
	
	/**
	 * Test Get Timezone - Value Is User
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValueIsUser() {
		
		$timezone_test = 'user';
		
		$user_timezone_test = 'America/New_York';
		
		$this->Query->Auth = $this->getMock('Auth', array('user'));
		
		$this->Query->Auth
			->expects($this->once())
			->method('user')
			->with('timezone')
			->will($this->returnValue($user_timezone_test));
			
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = $user_timezone_test;
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}

	/**
	 * Test Get Timezone - Value Is User But User Preference Empty
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValueIsUserButUserPreferenceEmpty() {
		
		$timezone_test = 'user';
		
		$this->Query->Auth = $this->getMock('Auth', array('user'));
		
		$this->Query->Auth
			->expects($this->once())
			->method('user')
			->with('timezone')
			->will($this->returnValue(null));
			
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}	
		
	/**
	 * Test Get Timezone - Valid Abbreviated Identifier
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValidAbbreviatedIdentifier() {
		
		$timezone_test = 'JST';
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = $timezone_test;
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}

	/**
	 * Test Get Timezone - Invalid Abbreviated Identifier
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneInvalidAbbreviatedIdentifier() {
		
		$timezone_test = 'JSTNOT';
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}
	
	/**
	 * Test Get Timezone - Valid Identifier
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValidIdentifier() {
		
		$timezone_test = 'America/New_York';
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = $timezone_test;
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}
	
	/**
	 * Test Get Timezone - Invalid Identifier
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneInvalidIdentifier() {
		
		$timezone_test = 'America/Invalid_Indentifier';
		
		$this->Query->Controller->request->query['timezone'] = $timezone_test;
		
		$expected = date_default_timezone_get();
		
		$this->assertEquals($expected, $this->Query->getTimezone());
		
	}
	
	/**
	 * Test Get Timezone - Valid GMT Offset
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneValidGmtOffset() {
		
		$timezones = array(
			'+09:00' => 'Asia/Tokyo',
			'+9:00' => 'Asia/Tokyo',
			'+0900' => 'Asia/Tokyo',
			'0900' => 'Asia/Tokyo',
			'900' => 'Asia/Tokyo',
			'+9' => 'Asia/Tokyo',
			'9' => 'Asia/Tokyo',
			'-5' => 'America/New_York',
			'-500' => 'America/New_York',
			'-0500' => 'America/New_York',
			'-5:00' => 'America/New_York',
			'-05:00' => 'America/New_York'
		);
		
		foreach ($timezones as $offset => $timezone_name) {

			$this->Query->Controller->request->query['timezone'] = $offset;
		
			$this->assertEquals(
				$timezone_name,
				$this->Query->getTimezone(),
				'Offset:'. $offset
			);
		
		}
		
	}
	
	/**
	 * Test Get Timezone - Invalid GMT Offset
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testGetTimezoneInvalidGmtOffset() {
		
		$timezone_test = array(
			'24',
			'14:50'
		);

		$expected = date_default_timezone_get();

		foreach ($timezone_test as $offset) {
			
			$this->Query->Controller->request->query['timezone'] = $offset;

			$this->assertEquals(
				$expected,
				$this->Query->getTimezone(),
				'Offset:'. $offset
			);
			
		}
		
	}
	
}
