<?php
App::uses('AppModel', 'Model');
App::uses('AppController', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('ApiComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiRequestHandler', 'Api.Controller' . DS . 'Component');
App::uses('ApiResourceComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiPermissionsComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiQueryComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiPaginatorComponent', 'Api.Controller'. DS .'Component');

/**
 * ApiResourceComponentThing Model
 *
 */
if (!class_exists('ApiResourceComponentThing')) {
	class ApiResourceComponentThing extends AppModel {
		public $useTable = false;
	}
}

/**
 * ApiResourceComponentStuff Model
 *
 */
if (!class_exists('ApiResourceComponentStuff')) {
	class ApiResourceComponentStuff extends AppModel {
		public $useTable = false;
	}
}

/**
 * ApiResourceComponentThings Controller
 *
 */
if (!class_exists('ApiResourceComponentThingsController')) {
	class ApiResourceComponentThingsController extends AppController {}
}

/**
 * Api Resource Component Double
 *
 */
if (!class_exists('ApiResourceComponentDouble')) {
	class ApiResourceComponentDouble extends ApiResourceComponent {
		public $_field_exceptions = array();
		public $_parent_model = null;
		public $_model = null;
		public $_id = 0;
		public $_data = array();
		public $_fields = array();
		public $_required_attributes = array();
		public $_validation_index = array();
	}
}

/**
 * Api Resource Component Test
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
class ApiResourceComponentTest extends CakeTestCase {

	/**
	 * Test Controller Name
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @var     string
	 */
	protected $test_controller = 'ApiResourceComponentThings';
	
	/**
	 * Test Model Name
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @var     string
	 */
	protected $test_model = 'ApiResourceComponentThing';
	
	/**
	 * Set Up Mocks For `saveAssociatedHasOne` Tests
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	private function __setUpMocksForSaveAssociatedHasOneTests() {
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'forModel',
				'withParentModel',
				'withData',
				'withId',
				'withFieldExceptions',
				'saveOne',
				'setResponseCode'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource
			->Controller
			->ApiResourceComponentThing = $this->getMock('ApiResourceComponentThing');
		
		$this->ApiResource
			->Controller
			->ApiResourceComponentThing
			->hasOne['ApiResourceComponentThing']['foreignKey'] = 'thing_id';
		
		$this->ApiResource
			->Controller
			->ApiResourceComponentThing
			->hasOne['ApiResourceComponentThing']['conditions'] = array('ApiResourceComponentThing.name' => 'ABC');
		
		$this->ApiResource
			->Controller
			->ApiResourceComponentThing
			->ApiResourceComponentThing = $this->getMock('ApiResourceComponentThing', array('find'));
		
	}
	
	/**
	 * Test Renders Conditions Results
	 *
	 * `rendersConditions()` is an extremely important method in terms of security.
	 * Let's run some sanity checks here to make sure it performs as expected in
	 * extremely complex scenarios.
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @param	string	$on	`read`, `update`, or `delete`
	 * @return  void
	 */
	private function __testRendersConditionsResults($on = 'read') {
		
		$test_fields = array(
			'field1',
			'field2',
			'field3',
			'field4',
			'field5',
			'field6'
		);
		
		$test_field_map = array();
		
		foreach ($test_fields as $field) {
			$test_field_map[$field] = $field;
		}
		
		$test_passed_conditions = array(
			'ApiResourceComponentThing.field1' => 'abc',
			'ApiResourceComponentThing.field1 <>' => 'xyz',
			'ApiResourceComponentThing.field1 >=' => 'a',
			'ApiResourceComponentThing.field1 <=' => 'z',
			'ApiResourceComponentThing.field1 LIKE' => '%ab%',
			'ApiResourceComponentThing.field1 NOT LIKE' => '%xy%',
			'ApiResourceComponentThing.field1 LIKE' => 'ab%',
			'ApiResourceComponentThing.field1 LIKE' => '%bc',
			'ApiResourceComponentThing.field2' => '2',
			'ApiResourceComponentThing.field2 <>' => '999',
			'ApiResourceComponentThing.field2 >=' => '1',
			'ApiResourceComponentThing.field2 <=' => '50',
			'ApiResourceComponentThing.field3' => '2013-04-03',
			'ApiResourceComponentThing.field3 <>' => '2013-04-01',
			'ApiResourceComponentThing.field3 >=' => '2013-04-02',
			'ApiResourceComponentThing.field3 <=' => '2013-04-10',
			'ApiResourceComponentThing.field3' => '2013-04-01 16:00:00',
			'ApiResourceComponentThing.field3 <>' => '2013-04-02 16:00:00',
			'ApiResourceComponentThing.field3 >=' => '2013-04-01 16:00:00',
			'ApiResourceComponentThing.field3 <=' => '2013-04-10 16:00:00',
			'ApiResourceComponentThing.field4' => array('abc','def','ghi','jkl'),
			'ApiResourceComponentThing.field5' => array(1,2,3,4),
			'ApiResourceComponentThing.field6' => false,
			'ApiResourceComponentThing.field7' => false
		);
		
		$test_permissions_conditions = array(
			'ApiResourceComponentThing.field1' => array('def', 'ghi'),
			'ApiResourceComponentThing.field2' => array(2, 4, 5, 6),
			'ApiResourceComponentThing.field4' => array('ghi', 'jkl', 'mno'),
			'ApiResourceComponentThing.field5' => false,
			'ApiResourceComponentThing.field7' => array(1, 2)
		);
		
		$this->ApiResource->Permissions
			->expects($this->once())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->once())
			->method('forModel')
			->with($this->test_model)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->once())
			->method('withFields')
			->with($test_fields)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->once())
			->method('on')
			->with($on)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->once())
			->method('requireConditions')
			->with()
			->will($this->returnValue($test_permissions_conditions));
			
		$this->ApiResource->on($on);
		
		$this->ApiResource->withFields($test_fields);
		
		$this->assertEquals(
			$test_fields,
			$this->ApiResource->_fields
		);
		
		$this->ApiResource->withFieldMap($test_field_map);
		
		$this->assertEquals(
			$test_field_map,
			$this->ApiResource->_field_map
		);
		
		$this->ApiResource->withPassedConditions($test_passed_conditions);
		
		$results = $this->ApiResource->rendersConditions();
		
		$expected = array(
			'ApiResourceComponentThing.field1' => false, // no intersections
			'ApiResourceComponentThing.field1 <>' => 'xyz',
			'ApiResourceComponentThing.field1 >=' => 'a',
			'ApiResourceComponentThing.field1 <=' => 'z',
			'ApiResourceComponentThing.field1 LIKE' => '%ab%',
			'ApiResourceComponentThing.field1 NOT LIKE' => '%xy%',
			'ApiResourceComponentThing.field1 LIKE' => 'ab%',
			'ApiResourceComponentThing.field1 LIKE' => '%bc',
			'ApiResourceComponentThing.field2' => '2', // 1 intersection
			'ApiResourceComponentThing.field2 <>' => '999',
			'ApiResourceComponentThing.field2 >=' => '1',
			'ApiResourceComponentThing.field2 <=' => '50',
			'ApiResourceComponentThing.field3' => '2013-04-03',
			'ApiResourceComponentThing.field3 <>' => '2013-04-01',
			'ApiResourceComponentThing.field3 >=' => '2013-04-02',
			'ApiResourceComponentThing.field3 <=' => '2013-04-10',
			'ApiResourceComponentThing.field3' => '2013-04-01 16:00:00',
			'ApiResourceComponentThing.field3 <>' => '2013-04-02 16:00:00',
			'ApiResourceComponentThing.field3 >=' => '2013-04-01 16:00:00',
			'ApiResourceComponentThing.field3 <=' => '2013-04-10 16:00:00',
			'ApiResourceComponentThing.field4' => array('ghi','jkl'), // 2 intersections
			'ApiResourceComponentThing.field5' => false, // permissions return `false`. non-starter.
			'ApiResourceComponentThing.field6' => false, // not set by permissions, but passed
			'ApiResourceComponentThing.field7' => false // permissions allow for me, but empty passed param is more restrictive
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
	}
	
	/**
	 * Init Validation Errors - With Validation Index
	 *  
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	private function __initValidationErrorsWithIndex() {
		
		$test_field_map = array(
			'fieldOne' => 'field_one',
			'fieldTwo' => 'field_two',
			'fieldThree' => 'field_three',
			'field_five' => 'FieldFive',
			'field_six' => 'field_six',
			'Metadatum.some_setting' => 'settings.some.setting'
		);
		
		$this->ApiResource->forModel('ApiResourceComponentThing');
		
		$this->ApiResource->withFieldMap($test_field_map);
		
		$this->ApiResource->_validation_index = array(
			0,
			'ApiResourceComponentThing'
		);
		
		$this->ApiResource->setValidationErrors(array(
			'fieldOne' => 'field1 error',
			'fieldTwo' => 'field2 error',
		));
		
		$this->ApiResource->_validation_index = array(
			1,
			'ApiResourceComponentThing'
		);
		
		$this->ApiResource->setValidationErrors(array(
			'fieldThree' => 'field3 error'
		));
		
		$this->ApiResource->forModel('ApiResourceComponentStuff');
		
		$this->ApiResource->_validation_index = array(
			0,
			'ApiResourceComponentStuff',
			0
		);
		
		$this->ApiResource->setValidationErrors(array(
			'field_five' => 'field5 error',
			'field_six' => 'field6 error',
		));
		
		$this->ApiResource->_validation_index = array(
			0,
			'ApiResourceComponentStuff',
			1
		);
		
		$this->ApiResource->setValidationErrors(array(
			'field_five' => 'field5 error'
		));
		
	}
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function setUp() {

		parent::setUp();
		
		$this->Model = $this->getMockForModel($this->test_model, array(
			'attributes',
			'getAllFieldNames',
			'getFieldNames',
			'getOptions',
			'getFieldMap',
			'validates',
			'create',
			'set',
			'save',
			'field',
			'exists',
			'getAssociated',
			'read',
			'delete',
			'find',
			'getMeta',
			'getDefaultAttributes',
			'isRelationSaveable',
			'isRelationFindable',
			'isDefaultObjectEnabled',
			'getDefaultObject',
			'begin',
			'commit',
			'rollback',
			'hasField'
		));
		
		$this->Controller = $this->getMock($this->test_controller);
		
		$this->Controller->modelClass = $this->test_model;
		
		$this->Controller->{$this->test_model} = $this->Model;
		
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
			
		$this->ApiResource = new ApiResourceComponentDouble($this->ComponentCollection);
		
		$this->ApiResource->modelClass = $this->test_model;
		
		$this->ApiResource->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);

		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);

	}

	/**
	 * Teardown
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function tearDown() {

		parent::tearDown();
		
		ClassRegistry::flush();

	}
	
	/**
	 * Test __construct
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testContructor() {
		
		$this->assertEquals(
			$this->Controller,
			$this->ApiResource->Controller
		);
		
		$this->assertEquals(
			$this->test_model,
			$this->ApiResource->modelClass
		);
		
	}
	
	/**
	 * Test For Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testForModel() {
		
		$this->ApiResource->forModel();
		
		$this->assertNull($this->ApiResource->_model);
		
		$results = $this->ApiResource->forModel($this->test_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test Validate Only - Set
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testValidateOnlySet() {

		// String
		$this->assertFalse($this->ApiResource->validateOnly('test'));
		
		// Integer
		$this->assertFalse($this->ApiResource->validateOnly(1));
		
		// Array
		$this->assertFalse($this->ApiResource->validateOnly(array('test')));
		
		// Object
		$this->assertFalse($this->ApiResource->validateOnly(new stdClass));
		
		// Validate Boolean last
		
		// Boolean False
		$this->assertFalse($this->ApiResource->validateOnly(false));
		
		// Boolean True
		$this->assertTrue($this->ApiResource->validateOnly(true));
		
	}
	
	/**
	 * Test Validate Only - Get
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testValidateOnlyGet() {
		
		// Default is FALSE
		$this->assertFalse($this->ApiResource->validateOnly());
		
		// Update value for testing
		$this->assertTrue($this->ApiResource->validateOnly(true));
		
		// Test getting only
		$this->assertTrue($this->ApiResource->validateOnly());
		
	}
	
	/**
	 * Test Skip Errors And Save - Set
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSkipErrorsAndSaveSet() {

		// String
		$this->assertFalse($this->ApiResource->skipErrorsAndSave('test'));
		
		// Integer
		$this->assertFalse($this->ApiResource->skipErrorsAndSave(1));
		
		// Array
		$this->assertFalse($this->ApiResource->skipErrorsAndSave(array('test')));
		
		// Object
		$this->assertFalse($this->ApiResource->skipErrorsAndSave(new stdClass));
		
		// Validate Boolean last
		
		// Boolean False
		$this->assertFalse($this->ApiResource->skipErrorsAndSave(false));
		
		// Boolean True
		$this->assertTrue($this->ApiResource->skipErrorsAndSave(true));
		
	}
	
	/**
	 * Test Skip Errors And Save - Get
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSkipErrorsAndSaveGet() {
		
		// Default is FALSE
		$this->assertFalse($this->ApiResource->skipErrorsAndSave());
		
		// Update value for testing
		$this->assertTrue($this->ApiResource->skipErrorsAndSave(true));
		
		// Test getting only
		$this->assertTrue($this->ApiResource->skipErrorsAndSave());
		
	}
	
	/**
	 * Test With Required Attributes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithRequiredAttributes() {
		
		$this->ApiResource->withRequiredAttributes();
		
		$this->assertEquals(array(), $this->ApiResource->_required_attributes);
		
		$results = $this->ApiResource->withRequiredAttributes(array('att1', 'att2'));
		
		$this->assertEquals(array('att1', 'att2'), $this->ApiResource->_required_attributes);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Parent Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithParentModel() {
		
		$this->ApiResource->withParentModel();
		
		$this->assertNull($this->ApiResource->_parent_model);
		
		$results = $this->ApiResource->withParentModel($this->test_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_parent_model);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Field Map
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithFieldMap() {
		
		$test_field_map = array(
			'fieldOne' => 'field_one',
			'fieldTwo' => 'field_two'
		);
		
		$this->ApiResource->withFieldMap();
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
		$results = $this->ApiResource->withFieldMap($test_field_map);
		
		$this->assertEquals($test_field_map, $this->ApiResource->_field_map);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Fields
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithFields() {
		
		$test_fields = array('field1', 'field2', 'field3');
		
		$this->ApiResource->withFields();
		
		$this->assertEmpty($this->ApiResource->_fields);
		
		$results = $this->ApiResource->withFields($test_fields);
		
		$this->assertEquals($test_fields, $this->ApiResource->_fields);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Metadata Fields
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithMetadataFields() {
		
		$test_fields = array('field1', 'field2');
		
		$this->ApiResource->withMetadataFields();
		
		$this->assertEmpty($this->ApiResource->_metadata_fields);
		
		$results = $this->ApiResource->withMetadataFields($test_fields);
		
		$this->assertEquals($test_fields, $this->ApiResource->_metadata_fields);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Special Fields
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithSpecialFields() {
		
		$test_fields = array('field1', 'field2');
		
		$this->ApiResource->withSpecialFields();
		
		$this->assertEmpty($this->ApiResource->_special_fields);
		
		$results = $this->ApiResource->withSpecialFields($test_fields);
		
		$this->assertEquals($test_fields, $this->ApiResource->_special_fields);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Data Fields
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithDataFields() {
		
		$test_data_fields = array('field1', 'field2', 'field3');
		
		$this->ApiResource->withDataFields();
		
		$this->assertEmpty($this->ApiResource->_data_fields);
		
		$results = $this->ApiResource->withDataFields($test_data_fields);
		
		$this->assertEquals($test_data_fields, $this->ApiResource->_data_fields);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Result
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithResult() {
		
		$test_result = array(
			'Model' => array(
				'field1' => 'value1',
				'field2' => 'value2',
				'field3' => 'value3'
			)
		);
		
		$this->ApiResource->withResult();
		
		$this->assertEmpty($this->ApiResource->_result);
		
		$results = $this->ApiResource->withResult($test_result);
		
		$this->assertEquals($test_result, $this->ApiResource->_result);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Passed Conditions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithPassedConditions() {
		
		$test_passed_conditions = array(
			'Model.field1' => 'value1',
			'Model.field2' => 'value2',
			'Model.field3' => 'value3'
		);
		
		$this->ApiResource->withPassedConditions();
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$results = $this->ApiResource->withPassedConditions($test_passed_conditions);
		
		$this->assertEquals($test_passed_conditions, $this->ApiResource->_passed_conditions);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Related Models
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithRelatedModels() {
		
		$test_related_models = array('Model1' => array('type' => 'hasOne'));
		
		$this->ApiResource->withRelatedModels();
		
		$this->assertEmpty($this->ApiResource->_related_models);
		
		$results = $this->ApiResource->withRelatedModels($test_related_models);
		
		$this->assertEquals($test_related_models, $this->ApiResource->_related_models);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Related Field Dependencies
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithRelatedFieldDependencies() {
		
		$test_related_field_dependencies = array('field1', 'field2', 'field3');
		
		$this->ApiResource->withRelatedFieldDependencies();
		
		$this->assertEquals(
			array(),
			$this->ApiResource->_related_field_dependencies
		);
		
		$results = $this->ApiResource
			->withRelatedFieldDependencies($test_related_field_dependencies);
		
		$this->assertEquals(
			$test_related_field_dependencies,
			$this->ApiResource->_related_field_dependencies
		);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test With Transactions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithTransactions() {
		
		// Default value
		$this->assertTrue($this->ApiResource->_transactions);
		
		// Change to False and test
		$results = $this->ApiResource->withTransactions(false);
		
		$this->assertFalse($this->ApiResource->_transactions);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test Has Transactions
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasTransactions() {
		
		// Default value
		$this->assertTrue($this->ApiResource->hasTransactions());
		
		// Change to False and test
		$this->ApiResource->withTransactions(false);
		
		$this->assertFalse($this->ApiResource->hasTransactions());
		
	}
	
	/**
	 * Test Requiring Single Result
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRequiringSingleResult() {
		
		$results = $this->ApiResource->requiringASingleResult();
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
		$this->assertTrue($this->ApiResource->_single_result);
		
	}
	
	/**
	 * Test With Id
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithId() {
		
		$test_id = 1;
		
		$this->ApiResource->withId();
		
		$this->assertEquals(0, $this->ApiResource->_id);
		
		$results = $this->ApiResource->withId($test_id);
		
		$this->assertEquals($test_id, $this->ApiResource->_id);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test Has Id
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testHasId() {
		
		$test = $this->ApiResource->hasId();
		
		$this->assertEqual($test, 0);
		
		$this->ApiResource->withId(2);
		
		$test = $this->ApiResource->hasId(2);
		
	}
	
	/**
	 * Test Expand Flattened Data - Non Array Data Paraam
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testExpandFlattenedDataNonArrayDataParam() {
		
		$data = 1;
		
		$this->assertEquals($data, $this->ApiResource->expandFlattenedData($data));
		
	}
	
	/**
	 * Test Expand Flattened Data -  With Dot-Flattened Data
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testExpandFlattenedDataWithFlattened() {
		
		// expandFlattenedData is private function called by withData, so test that
		$test_data = array(
			$this->test_model . '.field1' => 'value1',
			$this->test_model . '.field2' => 'value2',
			$this->test_model . '.field3' => 'value3'
		);
		
		$expected = array(
			$this->test_model => array(
				'field1' => 'value1',
				'field2' => 'value2',
				'field3' => 'value3'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$results = $this->ApiResource->withData($test_data);
		
		$this->assertEquals($expected, $this->ApiResource->_data);
		
	}
	
	/**
	 * Test Expand Flattened Data - With No Dot-Flattened Data - Should Not Change
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testExpandFlattenedDataWithNoFlattened() {
		
		// expandFlattenedData is private function called by withData, so test that
		
		$test_data = array(
			$this->test_model => array(
				'field1' => 'value1',
				'field2' => 'value2',
				'field3' => 'value3'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$results = $this->ApiResource->withData($test_data);
		
		$this->assertEquals($test_data, $this->ApiResource->_data);
	
	}
	
	/**
	 * Test Expand Flattened Data - With Some Dot-Flattened Data, Some Not
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testExpandFlattenedDataMixed() {
		
		// expandFlattenedData is private function called by withData, so test that
		
		$test_data = array(
			$this->test_model . '.field1' => 'value1',
			$this->test_model . '.field2' => 'value2',
			$this->test_model . '.field3' => 'value3',
			$this->test_model => array(
				'field4' => 'value4',
				'field5' => 'value5',
				'field6' => 'value6'
			)
		);
		
		$expected = array(
			$this->test_model => array(
				'field1' => 'value1',
				'field2' => 'value2',
				'field3' => 'value3',
				'field4' => 'value4',
				'field5' => 'value5',
				'field6' => 'value6'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$results = $this->ApiResource->withData($test_data);
		
		$this->assertEquals($expected, $this->ApiResource->_data);
	
	}
	
	/**
	 * Test Expand Flattened Data - With Multiple Dot-Flattened Data
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testExpandFlattenedDataWithMulti() {
		
		// expandFlattenedData is private function called by withData, so test that
		
		$test_data = array(
			$this->test_model . '.0.field1' => 'value1',
			$this->test_model . '.0.field2' => 'value2',
			$this->test_model . '.0.field3' => 'value3',
			$this->test_model . '.1.field1' => 'value4',
			$this->test_model . '.1.field2' => 'value5',
			$this->test_model . '.1.field3' => 'value6'
		);
		
		$expected = array(
			$this->test_model => array(
				'0' => array(
					'field1' => 'value1',
					'field2' => 'value2',
					'field3' => 'value3'
				),
				'1' => array(
					'field1' => 'value4',
					'field2' => 'value5',
					'field3' => 'value6'
				)
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$results = $this->ApiResource->withData($test_data);
		
		$this->assertEquals($expected, $this->ApiResource->_data);
	
	}
	
	/**
	 * Test With Data - Fails
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testWithData() {
		
		$test_data = array(
			$this->test_model => array(
				'field1' => 'value1',
				'field2' => 'value2',
				'field3' => 'value3'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$results = $this->ApiResource->withData($test_data);
		
		$this->assertEquals($test_data, $this->ApiResource->_data);
		
	}
	
	/**
	 * Test With Data - With Model Present
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testWithDataModelPresent() {
		
		$data = array(
			$this->test_model => array(
				'id' => 1,
				'name' => 'Name'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$test = $this->ApiResource->withData($data);
		
		$this->assertEqual($test, $this->ApiResource);
		
		$this->assertEqual($this->ApiResource->_data, $data);
		
	}
	
	/**
	 * Test With Data - With Model Empty
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testWithDataModelEmpty() {
		
		$data = array(
			'id' => 1,
			'name' => 'Name'
		);
		
		$this->ApiResource->forModel();
		
		$test = $this->ApiResource->withData($data);
		
		$this->assertFalse($test);
		
		$this->assertEqual($this->ApiResource->_data, array());
		
	}
	
	/**
	 * Test On
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testOn() {
		
		$this->assertEquals(
			$this->ApiResource,
			$this->ApiResource->on()
		);
		
		$this->assertInstanceOf(
			'Component',
			$this->ApiResource->on()
		);
		
		$this->assertEquals(
			'read',
			$this->ApiResource->_on
		);
		
		$this->assertInstanceOf(
			'Component',
			$this->ApiResource->on('update')
		);
		
		$this->assertEquals(
			'update',
			$this->ApiResource->_on
		);
		
	}
	
	/**
	 * Test Set Response Code
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testResponseCode() {
		
		$this->ApiResource->Api
			->expects($this->any())
			->method('setResponseCode')
			->with($this->equalTo(2000));
		
		$this->ApiResource->setResponseCode(2000);
		
	}
	
	/**
	 * Test Set Validation Errors
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetValidationErrors() {
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'username' => array('type' => 'string'),
				'options.subKey' => array('field' => 'options.sub_key', 'type' => 'string')
			)));
			
		$this->ApiResource->Controller->{$this->test_model}->Behaviors->attach(
			'SpecialFields.SpecialFields',
			array(
				'json' => array('options')
			)
		);
		
		$test_field_map = array(
			'fieldOne' => 'field_one',
			'fieldTwo' => 'field_two',
			'fieldThree' => 'field_three',
			'field_five' => 'FieldFive',
			'field_six' => 'field_six',
			'Metadatum.some_setting' => 'settings.some.setting',
			'options.subfield_one' => 'options.subfield_one',
			'options.subfield_two' => 'options.subfield_two'
		);
		
		$test_errors = array(
			'field_one' => array('Error for field one'),
			'fieldTwo' => array('Error for field two'),
			'field_four' => array('Error for field four'),
			'field_five' => array('Error for field five'),
			'field_six' => array('Error for field six'),
			'Metadatum' => array(
				'some_setting' => 'Error on a setting'
			),
			'options' => 'Please enter valid options'
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->ApiResource->withFieldMap($test_field_map);
		
		$this->assertEquals($test_field_map, $this->ApiResource->_field_map);
		
		$results = $this->ApiResource->setValidationErrors($test_errors);
		
		$expected = array(
			0 => array(
				'field_two' => array('Error for field two'),
				'FieldFive' => array('Error for field five'),
				'field_six' => array('Error for field six'),
				'settings' => array(
					'some' => array(
						'setting' => array('Error on a setting')
					)
				),
				'options' => array('Please enter valid options')
			)
		);
		
		$this->assertEquals($expected, $this->ApiResource->_validation_errors);
		
		$this->assertInstanceOf('ApiResourceComponent', $results);
		
	}
	
	/**
	 * Test Set Validation Errors - With Validation Index
	 *  
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testSetValidationErrorsWithIndex() {
		
		$expects = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => array('field1 error'),
					'field_two' => array('field2 error'),
				),
				'ApiResourceComponentStuff' => array(
					array(
						'FieldFive' => array('field5 error'),
						'field_six' => array('field6 error'),
					),
					array(
						'FieldFive' => array('field5 error')
					)
				)
			),
			array(
				'ApiResourceComponentThing' => array(
					'field_three' => array('field3 error')
				)
			)
		);
		
		$this->__initValidationErrorsWithIndex();
		
		$validationErrors = Hash::expand($this->ApiResource->_validation_errors);
		
		$this->assertEquals($expects, $validationErrors);
		
	}
	
	/**
	 * Test Has Validation Errors - With Validation Index
	 *  
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testHasValidationErrorsWithIndex() {
		
		$expected = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => array('field1 error'),
					'field_two' => array('field2 error'),
				),
				'ApiResourceComponentStuff' => array(
					array(
						'FieldFive' => array('field5 error'),
						'field_six' => array('field6 error'),
					),
					array(
						'FieldFive' => array('field5 error')
					)
				)
			),
			array(
				'ApiResourceComponentThing' => array(
					'field_three' => array('field3 error')
				)
			)
		);
		
		$this->__initValidationErrorsWithIndex();
		
		$test = $this->ApiResource->hasValidationErrors();
		
		$this->assertEquals($expected, $test);
		
	}
	
	/**
	 * Test Set Validation Errors - Without Errors Array
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testSetValidationErrorsWithoutErrorsArray() {
		
		$this->assertTrue($this->ApiResource->setValidationErrors());
		
	}
	
	/**
	 * Test Set Validation Errors - Without Model
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSetValidationErrorsWithoutModel() {
		
		$this->ApiResource->forModel();
		
		$this->ApiResource->withFieldMap(array(
			'username' => 'username'
		));
		
		$this->ApiResource->setValidationErrors(array('username' => 'Please enter a username'));
		
		$this->assertEqual(
			$this->ApiResource->_validation_errors,
			array(0 => array('username' => array('Please enter a username')))
		);
		
	}
	
	/**
	 * Test Set Validation Errors - Append
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSetValidationErrorsAppend() {
		
		$this->ApiResource->withFieldMap(array(
			'username' => 'username'
		));
		
		$this->ApiResource->forModel('ApiResourceComponentThing')->setValidationErrors(array(
			'username' => 'Please enter a username'
		));
		
		$this->ApiResource->setValidationIndex(array(0, 'FormResponse'));
		
		$this->ApiResource->forModel('FormResponse')->setValidationErrors(array(
			'FormResponse' => 'Not related'
		));
		
		$this->ApiResource->withFieldMap(array(
			'id' => 'id',
			'fname' => 'firstName',
			'lname' => 'lastName'
		));
		
		$this->ApiResource->setValidationIndex(array(0, 'ApiResourceComponentStuff'));
		
		$this->ApiResource->forModel('ApiResourceComponentStuff')->setValidationErrors(array(
			'fname' => 'Please enter a first name'
		));
		
		$this->assertEqual(
			array(
				array(
					'username' => array('Please enter a username'),
					'formResponse' => array(
						'FormResponse' => array('Not related')
					),
					'apiResourceComponentStuff' => array(
						'firstName' => array('Please enter a first name')
					)
				)
			),
			Hash::expand($this->ApiResource->_validation_errors)
		);
		
	}
	
	/**
	 * Test Set Validation Errors - Reset
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSetValidationErrorsReset() {
		
		$this->ApiResource->withFieldMap(array(
			'username' => 'username'
		));
		
		$this->ApiResource->setValidationIndex(array(0, 'ApiResourceComponentThing'));
		
		$this->ApiResource->forModel('ApiResourceComponentThing')->setValidationErrors(array(
			'username' => 'Please enter a username'
		));
		
		$this->ApiResource->withFieldMap(array(
			'id' => 'id',
			'fname' => 'firstName',
			'lname' => 'lastName'
		));
		
		$this->ApiResource->setValidationIndex(array(0, 'ApiResourceComponentStuff'));
		
		$this->ApiResource->forModel('ApiResourceComponentStuff')->setValidationErrors(array(
			'fname' => 'Please enter a first name'
		));
		
		$this->ApiResource->setValidationIndex(array(0, 'ApiResourceComponentThing'));
		
		// Reset
		$this->ApiResource->forModel('ApiResourceComponentThing')->setValidationErrors();
		
		$this->ApiResource->withFieldMap(array(
			'email' => 'email'
		));
		
		$this->ApiResource->forModel('ApiResourceComponentThing')->setValidationErrors(array(
			'email' => 'Please enter a valid email address'
		));
		
		$this->assertEqual(
			array(
				array(
					'apiResourceComponentThing' => array(
						'email' => array('Please enter a valid email address')
					),
					'apiResourceComponentStuff' => array(
						'firstName' => array('Please enter a first name')
					)
				)
			),
			Hash::expand($this->ApiResource->_validation_errors)
		);
		
	}
	
	/**
	 * Test Set Validation Errors - With Field Map
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSetValidationErrorsWithFieldMap() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withFieldMap(array(
			'id' => 'id',
			'fname' => 'firstName',
			'lname' => 'lastName'
		));
		
		$this->ApiResource->setValidationErrors(array(
			'id' => 'Invalid ID',
			'fname' => 'Please enter a first name',
			'lname' => 'Please enter a last name',
		));
		
		$this->assertEqual($this->ApiResource->_validation_errors, array(
			array(
				'id' => array('Invalid ID'),
				'firstName' => array('Please enter a first name'),
				'lastName' => array('Please enter a last name')
			)
		));
		
	}
	
	/**
	 * Test Has Validation Errors - Without Errors
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testHasValidationErrorsWithoutErrors() {
		
		$this->assertNull($this->ApiResource->hasValidationErrors());
		
	}
	
	/**
	 * Test Has Validation Errors - With Errors
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testHasValidationErrors() {
		
		$this->ApiResource->withFieldMap(array(
			'username' => 'username'
		));
		
		$this->ApiResource->forModel($this->test_model)->setValidationErrors(array(
			'username' => array('Please enter a username')
		));
		
		$test = $this->ApiResource->forModel()->hasValidationErrors();
		
		$this->assertEqual(
			$test,
			array(array('username' => array('Please enter a username')))
		);
		
	}
	
	/**
	 * Test Has Required Attributes - With Requested Attributes and Primary Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testHasRequiredAttributesWithRequestedAttributesAndPrimaryModel() {
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedAttributes'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedAttributes')
			->with()
			->will($this->returnValue(array('att1', 'att2')));
		
		$this->ApiResource->{$this->test_model}
			->expects($this->never())
			->method('getDefaultAttributes');
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withParentModel()
			->hasRequiredAttributes();
		
		$expected = array('att1', 'att2');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Has Required Attributes - With Requested Attributes and Child Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testHasRequiredAttributesWithRequestedAttributesAndChildModel() {
		
		// @todo fix test
		return;
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedAttributes'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedAttributes')
			->will($this->returnValue(array(
				'id',
				'name',
				$this->test_model . '.test_model_name'
			)));
		
		$this->ApiResource->{$this->test_model}
			->expects($this->never())
			->method('getDefaultAttributes');
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withParentModel('SomeParent')
			->hasRequiredAttributes();
		
		$expected = array('test_model_name');
		
		debug($result);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Has Required Attributes - With No Requested Attributes and Primary Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testHasRequiredAttributesWithNoRequestedAttributesAndPrimaryModel() {
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedAttributes'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedAttributes')
			->with()
			->will($this->returnValue(array()));
		
		$this->Controller->{$this->test_model}
			->expects($this->once())
			->method('getDefaultAttributes')
			->with()
			->will($this->returnValue(array('att1', 'att2', 'att3')));
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withParentModel()
			->hasRequiredAttributes();
		
		$expected = array('att1', 'att2', 'att3');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Has Required Attributes - With No Requested Attributes and Child Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testHasRequiredAttributesWithNoRequestedAttributesAndChildModel() {
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedAttributes'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedAttributes');
		
		$this->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array('getDefaultAttributes')
		);
		
		$this->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('getDefaultAttributes')
			->with()
			->will($this->returnValue(array('att1', 'att2', 'att3')));
		
		$result = $this->ApiResource
			->forModel('ApiResourceComponentStuff')
			->withParentModel($this->test_model)
			->hasRequiredAttributes();
		
		$expected = array('att1', 'att2', 'att3');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Renders Conditions - Fails With No Fields
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRendersConditionsFailsWithNoFields() {
		
		$this->ApiResource->Permissions
			->expects($this->never())
			->method('forModel');
		
		$this->assertEmpty($this->ApiResource->rendersConditions());
		
	}
	
	/**
	 * Test Renders Conditions Results - on Read
	 *
	 * @todo    Do more checks
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRendersConditionsResultsOnRead() {
		
		$this->__testRendersConditionsResults('read');
		
	}
	
	/**
	 * Test Renders Conditions Results - on Update
	 *
	 * @todo    Do more checks
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRendersConditionsResultsOnUpdate() {
		
		$this->__testRendersConditionsResults('update');
		
	}
	
	/**
	 * Test Renders Conditions Results - on Delete
	 *
	 * @todo    Do more checks
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testRendersConditionsResultsOnDelete() {
		
		$this->__testRendersConditionsResults('delete');
		
	}
	
	/**
	 * Test Has Validation Errors - Without Validation Errors
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasValidationErrorsFailsWithoutValidationErrors() {
		
		$this->assertNull($this->ApiResource->hasValidationErrors());
		
	}
	
	/**
	 * Test Has Validation Errors - Results
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasValidationErrorsResults() {
		
		$test_errors = array(
			'error1' => array('Error one'),
			'error2' => array('Error two')
		);
		
		$this->ApiResource->withFieldMap(array(
			'error1' => 'error1',
			'error2' => 'error2'
		));
		
		$this->ApiResource->setValidationErrors($test_errors);
		
		$results = $this->ApiResource->hasValidationErrors();
		
		$this->assertEquals(array($test_errors), $results);
		
		$this->assertEmpty($this->ApiResource->_validation_errors);
		
		$this->assertNull($this->ApiResource->_model);
		
	}
	
	/**
	 * Test Has Required Relations - With No Requested Relations
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasRequiredRelationsWithNoRequestedRelations() {
		
		$this->ApiResource->{$this->test_model}
			->expects($this->any())
			->method('isRelationFindable')
			->will($this->returnCallback(function($relation){
				if ($relation === 'BannedApiResourceComponentStuff') {
					return false;
				}
				return true;
			}));
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedRelations'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedRelations')
			->with()
			->will($this->returnValue(array()));
		
		$hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'request' => true
			),
			'OtherApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'require' => true
			),
			'MoreApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => ''
			),
			'BannedApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'findable' => false
			),
		);
		
		$this->ApiResource->{$this->test_model}->hasOne = $hasOne;
		
		$results = $this->ApiResource->hasRequiredRelations();
		
		$expected = array(
			'OtherApiResourceComponentStuff' => array(
				'type' => 'hasOne',
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'require' => true
			),
			'MoreApiResourceComponentStuff' => array(
				'type' => 'hasOne',
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => ''
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->_related_models
		);
		
		$this->assertNull($this->ApiResource->_model);
		
	}
	
	/**
	 * Test Has Required Relations - With Requested Relations
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasRequiredRelationsWithRequestedRelations() {
		
		$this->ApiResource->{$this->test_model}
			->expects($this->any())
			->method('isRelationFindable')
			->will($this->returnCallback(function($relation){
				if ($relation === 'BannedApiResourceComponentStuff') {
					return false;
				}
				return true;
			}));
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent', 
			array('requestedRelations'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('requestedRelations')
			->with()
			->will($this->returnValue(array('ApiResourceComponentStuff', 'BannedApiResourceComponentStuff')));
		
		$hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'request' => true
			),
			'OtherApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'require' => true
			),
			'MoreApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => ''
			),
			'BannedApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'findable' => false
			)
		);
		
		$this->ApiResource->{$this->test_model}->hasOne = $hasOne;
		
		$results = $this->ApiResource->hasRequiredRelations();
		
		$expected = array(
			'ApiResourceComponentStuff' => array(
				'type' => 'hasOne',
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'request' => true
			),
			'OtherApiResourceComponentStuff' => array(
				'type' => 'hasOne',
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => array('id', 'name'),
				'offset' => '',
				'counterQuery' => '',
				'require' => true
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->_related_models
		);
		
		$this->assertNull($this->ApiResource->_model);
		
	}
	
	/**
	 * Test Has Related Field Names
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasRelatedFieldNames() {
		
		$fields = array(
			$this->test_model .'.field_one',
			$this->test_model .'.field_two'
		);
		
		$related_models = array('ApiResourceComponentStuff' => array('type' => 'belongsTo'));
		
		$field_dependencies_with_value = array(
			$this->test_model => array('belongsTo' => array('ApiResourceComponentStuff'))
		);
		
		$field_dependencies_will_value = array(
			'field_two',
			'field_four',
			'field_five'
		);
		
		$this->ApiResource->{$this->test_model} = $this->getMock(
			$this->test_model,
			array('fieldDependencies')
		);
		
		$this->ApiResource->{$this->test_model}
			->expects($this->atLeastOnce())
			->method('fieldDependencies')
			->with($this->equalTo($field_dependencies_with_value))
			->will($this->returnValue($field_dependencies_will_value));
			
		$this->ApiResource->withFields($fields);
		
		$this->ApiResource->withRelatedModels($related_models);
		
		$results = $this->ApiResource->hasRelatedFieldNames();
		
		$expected = array(
			1 => 'ApiResourceComponentThing.field_four',
			2 => 'ApiResourceComponentThing.field_five'
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_fields);
		
		$this->assertEmpty($this->ApiResource->_related_models);
		
	}
	
	/**
	 * Test Has Related Field Names - Without Related Fieldnames
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testHasRelatedFieldNamesWithoutRelatedFieldnames() {
		
		$fields = array(
			'field_one',
			'field_two'
		);
		
		$this->ApiResource->withFields($fields);
		
		$results = $this->ApiResource->hasRelatedFieldNames();
		
		$this->assertEmpty($results);
		
		$this->assertEmpty($this->ApiResource->_fields);
		
		$this->assertEmpty($this->ApiResource->_related_models);
		
	}
	
	/**
	 * Test Returns Fields As Attributes - Results
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFieldsAsAttributes() {
		
		$field_map = array(
			'field_one' => 'attributeOne',
			'field_four_with_suffix' => 'attributeFourWithSuffix',
			'field_two' => 'attributeTwo',
			'field_two_with_suffix' => 'attributeTwoWithSuffix',
			'Metadatum.nested' => 'attributeThree',
			'field_four' => 'attributeFour',
			'dont_include' => 'dontInclude'
		);
		
		$result = array(
			'field_one' => 'value1',
			'field_two' => 'value2',
			'field_two_with_suffix' => array(
				'multi' => 'dimensional',
				'another' => 'one'
			),
			'field_four' => 'value4',
			'field_four_with_suffix' => 'value4 with suffix',
			'Metadatum' => array(
				'nested' => 'value3'
			)
		);
		
		$this->ApiResource->withResult($result);
		
		$this->ApiResource->withFieldMap($field_map);
		
		$results = $this->ApiResource->returnsFieldsAsAttributes();
		
		$expected = array(
			'attributeOne' => 'value1',
			'attributeTwo' => 'value2',
			'attributeTwoWithSuffix' => array(
				'multi' => 'dimensional',
				'another' => 'one'
			),
			'attributeThree' => 'value3',
			'attributeFour' => 'value4',
			'attributeFourWithSuffix' => 'value4 with suffix'
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertNull($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}
	
	/**
	 * Test Returns Fields Asattributes - Fails Empty `$_result`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFieldsAsAttributesFailsEmptyResult() {
		
		$this->assertEquals(
			$this->ApiResource->_result,
			$this->ApiResource->returnsFieldsAsAttributes()
		);
		
	}
	
	/**
	 * Test Returns Fields As Attributes - Fails Empty `$_field_map`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFieldsAsAttributesFailsEmptyFieldMap() {
		
		$result = array(
			'field' => 'value'
		);
		
		$this->ApiResource->withResult($result);
		
		$this->assertEquals(
			$this->ApiResource->_result,
			$this->ApiResource->returnsFieldsAsAttributes()
		);
		
	}
	
	/**
	 * Test Returns Formatted Result - With Primary Model Only
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultWithPrimaryModelOnly() {
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue('UTC'));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(4))
			->method('getOptions')
			->with($this->equalTo('type'))
			->will($this->returnValue(array(1 => 'A')));
		
		$field_map = array(
			'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo',
			'foreign_field' => 'foreignAttribute',
			'type' => 'type',
			'Metadatum.notes' => 'attributeNotes'
		);
		
		$result = array(
			array('ApiResourceComponentThing' => array(
				'field_one' => 'value1',
				'field_two' => 'value2',
				'foreign_field' => 'foreign_value',
				'type' => 1,
				'Metadatum' => array(
					'notes' => 'some notes here'
				)
			))
		);
		
		$this->ApiResource->withFieldMap($field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			array(
				'attributeOne' => 'value1',
				'attributeTwo' => 'value2',
				'foreignAttribute' => 'foreign_value',
				'type' => 'A',
				'attributeNotes' => 'some notes here'
			)
		);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
	 * Test Returns Formatted Result - With Associative Result Array
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultWithAssociativeResultArray() {
		
		$field_map = array(
			'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo'
		);
		
		$result = array(
			'ApiResourceComponentThing' => array(
				'field_one' => 'value1',
				'field_two' => 'value2'
			)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue('UTC'));
			
		$this->ApiResource->withFieldMap($field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			'attributeOne' => 'value1',
			'attributeTwo' => 'value2'
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}
	
	/**
	 * Test Returns Formatted Result - With Associated Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultWithAssociatedModel() {
		
		$field_map = array(
			'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo'
		);
		
		$result = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => 'value1',
					'field_two' => 'value2'
				),
				'ApiResourceComponentStuff' => array(
					'field_four' => 'value4',
					'field_five' => 'value5'
				)
			)
		);
		
		$attributes = array(
			'attributeFour' => 'field_four',
			'attributeFive' => 'field_five'
		);
		
		$allowed_attributes = array(
			'field_four',
			'field_five'
		);
		
		$get_field_map = array(
			'field_four' => 'attributeFour',
			'field_five' => 'attributeFive'
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue('UTC'));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff', 
			array('attributes', 'getFieldMap')
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($attributes));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('forModel')
			->with($this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff->alias)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('withAttributes')
			->with($attributes)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('on')
			->with('read')
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('allowAttributes')
			->with()
			->will($this->returnValue($allowed_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('getFieldMap')
			->with($allowed_attributes)
			->will($this->returnValue($get_field_map));
		
		$this->ApiResource->withFieldMap($field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			array(
				'attributeOne' => 'value1',
				'attributeTwo' => 'value2',
				'apiResourceComponentStuff' => array(
					'attributeFour' => 'value4',
					'attributeFive' => 'value5'
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}

	/**
	 * Test Returns Formatted Result - With Empty `$allowed_attributes`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultWithEmptyAllowedAttributes() {
		
		$field_map = array(
			'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo'
		);
		
		$result = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => 'value1',
					'field_two' => 'value2'
				)
			),
			array(
				'ApiResourceComponentStuff' => array(
					'field_four' => 'value4',
					'field_five' => 'value5'
				)
			)
		);
		
		$attributes = array(
			'attributeFour' => 'field_four',
			'attributeFive' => 'field_five'
		);
		
		$allowed_attributes = array();
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue('UTC'));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($attributes));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('forModel')
			->with($this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff->alias)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('withAttributes')
			->with($attributes)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('on')
			->with('read')
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('allowAttributes')
			->with()
			->will($this->returnValue($allowed_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('getFieldMap');
		
		$this->ApiResource->withFieldMap($field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			array(
				'attributeOne' => 'value1',
				'attributeTwo' => 'value2'
			),
			array()
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}
	
	/**
	 * Test Returns Formatted Result - Fails Empty `$_result`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultFailsEmptyResult() {
		
		$this->assertEquals(
			$this->ApiResource->_result,
			$this->ApiResource->returnsFormattedResult()
		);
		
	}
	
	/**
	 * Test Returns Formatted Result - Fails Empty `$_field_map`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultFailsEmptyFieldMap() {
		
		$result = array(
			'field' => 'value'
		);
		
		$this->ApiResource->withResult($result);
		
		$this->assertEquals(
			$this->ApiResource->_result,
			$this->ApiResource->returnsFormattedResult()
		);
		
	}
	
	/**
	 * Test Returns Formatted Result - Polymorphic Resource Path
	 *
	 * @author  Paul Smith <paul@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultPolymorphicResource() {
		
		$result = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => 'somevalue', // Attribute is not in options array - won't be changed
					'field_two' => 'FirstModel' // Missing `type` setting
				),
				'ApiResourceComponentStuff' => array(
					'field_four' => 'somevalue'
				)
			)
		);
		
		$thing_field_map = array(
			'field_two' => 'attributeTwo'
		);
		
		$thing_attributes = array(
			//'attributeOne' // Keep out for testing
			'attributeTwo' => array(
				'field' => 'field_two',
				'type' => 'string',
				'sort' => true,
				'query' => true,
				'polymorphic_model' => true
 			)
		);
		
		$thing_allowed_attributes = array(
			'field_two'
		);
		
		$thing_get_field_map = array(
			// 'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo'
		);
		
		$stuff_attributes = array(
			'attributeFour' => array(
				'field' => 'field_four',
				'type' => 'string',
				'sort' => true,
				'query' => true
 			)
		);
		
		$stuff_allowed_attributes = array(
			'field_four',
			'field_five'
		);
		
		$stuff_get_field_map = array(
			'field_four' => 'attributeFour'
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($thing_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(2))
			->method('getOptions')
			->with($this->equalTo('field_two'))
			->will($this->returnValue(
				array(
					'FirstModel' => 'first_resources',
					'SecondModel' => 'second_resources'
				)
			));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff', 
			array('attributes', 'getFieldMap')
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($stuff_attributes));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('forModel')
			->with($this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff->alias)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('withAttributes')
			->with($stuff_attributes)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('on')
			->with('read')
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('allowAttributes')
			->with()
			->will($this->returnValue($stuff_allowed_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('getFieldMap')
			->with($stuff_allowed_attributes)
			->will($this->returnValue($stuff_get_field_map));
		
		$this->ApiResource->withFieldMap($thing_field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			array(
				'attributeTwo' => 'first_resources',
				'apiResourceComponentStuff' => array(
					'attributeFour' => 'somevalue'
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}
	
	/**
	 * Test Returns Formatted Result - Timezone Conversion
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsFormattedResultTimezoneConversion() {
		
		$timezone = 'Asia/Tokyo';
		
		$result = array(
			array(
				'ApiResourceComponentThing' => array(
					'field_one' => '2013-04-29 01:00:00', // Attribute is not in attributes array - technically this should not ever happen
					'field_two' => '2013-04-29 02:00:00' // Missing `type` setting
				),
				'ApiResourceComponentStuff' => array(
					'field_four' => '2013-04-29 11:40:00', // Will convert from UTC to $timezone
					'field_five' => '2013-04-29 00:00:00', // Will convert from UTC to $timezone
					'field_six' => '2013-07-06' // Will not convert to ISO8601
				)
			)
		);
		
		$thing_field_map = array(
			'field_two' => 'attributeTwo'
		);
		
		$thing_attributes = array(
			//'attributeOne' // Keep out for testing
			'attributeTwo' => array(
				'field' => 'field_two',
				//'type' => 'string', // Keep the `type` setting out here for testing
				'sort' => true,
				'query' => true
 			)
		);
		
		$thing_allowed_attributes = array(
			'field_two'
		);
		
		$thing_get_field_map = array(
			// 'field_one' => 'attributeOne',
			'field_two' => 'attributeTwo'
		);
		
		$stuff_attributes = array(
			'attributeFour' => array(
				'field' => 'field_four',
				'type' => 'datetime',
				'sort' => true,
				'query' => true
 			),
			'attributeFive' => array(
				'field' => 'field_five',
				'type' => 'datetime',
				'sort' => true,
				'query' => true
 			),
			'attributeSix' => array(
				'field' => 'field_six',
				'type' => 'date',
				'sort' => true,
				'query' => true
 			)
		);
		
		$stuff_allowed_attributes = array(
			'field_four',
			'field_five',
			'field_six'
		);
		
		$stuff_get_field_map = array(
			'field_four' => 'attributeFour',
			'field_five' => 'attributeFive',
			'field_six' => 'attributeSix'
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue($timezone));
		
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($thing_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff', 
			array('attributes', 'getFieldMap')
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('attributes')
			->with()
			->will($this->returnValue($stuff_attributes));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('forModel')
			->with($this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff->alias)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('withAttributes')
			->with($stuff_attributes)
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('on')
			->with('read')
			->will($this->returnValue($this->ApiResource->Permissions));
			
		$this->ApiResource->Permissions
			->expects($this->atLeastOnce())
			->method('allowAttributes')
			->with()
			->will($this->returnValue($stuff_allowed_attributes));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->atLeastOnce())
			->method('getFieldMap')
			->with($stuff_allowed_attributes)
			->will($this->returnValue($stuff_get_field_map));
		
		$this->ApiResource->withFieldMap($thing_field_map);
		
		$this->ApiResource->withResult($result);
		
		$results = $this->ApiResource->returnsFormattedResult();
		
		$expected = array(
			array(
				'attributeTwo' => '2013-04-29 02:00:00',
				'apiResourceComponentStuff' => array(
					'attributeFour' => '2013-04-29T20:40:00+0900', // JST
					'attributeFive' => '2013-04-29T09:00:00+0900', // JST
					'attributeSix' => '2013-07-06' // Do not change to ISO8601 `date` fields
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_field_map);
		
	}
	
	/**
	 * Test Returns Field Map - With Empty Model
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testReturnsFieldMapEmptyModel() {
		
		$test = $this->ApiResource->returnsFieldMap('read');
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Returns Field Map - With Empty Filtered Attributes
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testReturnsFieldMapEmptyFilteredAttributes() {
		
		$this->ApiResource->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username',
				'password',
				'name',
				'email'
			)));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->withParentModel();
		
		$this->ApiResource->forModel($this->test_model);
		
		$test = $this->ApiResource->returnsFieldMap('read');
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Returns Field Map
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testReturnsFieldMapWithRequiredAttributes() {
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'att1' => array('field' => 'field1'),
				'att2' => array('field' => 'field2'),
				'att3' => array('field' => 'field3')
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->with()
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->with($this->equalTo($this->test_model))
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->with($this->equalTo(array(
				'att1' => array('field' => 'field1'),
				'att2' => array('field' => 'field2')
			)))
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->with($this->equalTo('read'))
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->with()
			->will($this->returnValue(array(
				'att1' => array('field' => 'field1'),
				'att2' => array('field' => 'field2')
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->with($this->equalTo(array(
				'att1' => array('field' => 'field1'),
				'att2' => array('field' => 'field2')
			)))
			->will($this->returnValue(array(
				'field1' => 'att1',
				'field2' => 'att2'
			)));
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withRequiredAttributes(array('att1', 'att2'))
			->returnsFieldMap('read');
		
		$expected = array(
			'field1' => 'att1',
			'field2' => 'att2'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Returns Field Map
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testReturnsFieldMap() {
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username',
				'password',
				'name',
				'emailAddress'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id' => array(),
				'username' => array(),
				'name' => array(),
				'emailAddress' => array('field' => 'email')
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username',
				'name' => 'name',
				'email' => 'emailAddress'
			)));
		
		$this->ApiResource->withParentModel();
		
		$this->ApiResource->forModel($this->test_model);
		
		$test = $this->ApiResource->returnsFieldMap('read');
		
		$this->assertEqual($test, array(
			'id' => 'id',
			'username' => 'username',
			'name' => 'name',
			'email' => 'emailAddress'
		));
		
	}
	
	/**
	 * Test Returns Api Response - Without Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseWithoutModel() {
		
		$this->assertFalse($this->ApiResource->returnsApiResponse());
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		 
		$this->assertEmpty($this->ApiResource->_model);
		
	}
	
	/**
	 * Test Returns Api Response - As Primary Model
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseAsPrimaryModel() {
		
		$field_map = array(
			'field_one' => 'attributeOne'
		);
		
		$passed_conditions = array();
		
		$field_names = array('ApiResourceComponentThing.field_one');
		
		$conditions = array('field_one' => 'one');
		
		$related_models = array('ApiResourceComponentStuff');
		
		$related_field_names = array('field_two', 'field_three');
		
		$results_without_metadata = array(
			array('result' => 'test_results')
		);
		
		$results_with_metadata = array(
			array(
				'result' => 'test_results',
				'Metadatum' => array('key' => 'value')
			)
		);
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'returnsFieldMap',
				'withFieldMap',
				'withFields',
				'withPassedConditions',
				'rendersConditions',
				'hasRequiredRelations',
				'withRelatedModels',
				'hasRelatedFieldNames',
				'withRelatedFieldDependencies',
				'withResult',
				'returnsResultWithMetadata',
				'returnsResultWithRelatedModelData',
				'returnsFormattedResult',
				'withRequiredAttributes',
				'hasRequiredAttributes'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array(
				'withFieldMap',
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->action = 'index';
		
		$this->ApiResource->Controller->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array('attributeOne' => array())));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array('attributeOne' => array())))
			->will($this->returnValue($field_map));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with(null)
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('hasRequiredAttributes')
			->with()
			->will($this->returnValue(array('attributeOne')));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRequiredAttributes')
			->with($this->equalTo(array('attributeOne')))
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFieldMap')
			->with('read')
			->will($this->returnValue($field_map));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getAllFieldNames')
			->with()
			->will($this->returnValue($field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldNames')
			->with($field_map)
			->will($this->returnValue($field_names));
			
		$this->ApiResource->Query
			->expects($this->once())
			->method('withFieldMap')
			->with($field_map)
			->will($this->returnValue($this->ApiResource->Query));
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($passed_conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFieldMap')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFields')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRequiredRelations')
			->with()
			->will($this->returnValue($related_models));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withRelatedModels')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRelatedFieldNames')
			->with()
			->will($this->returnValue($related_field_names));
		
		$this->ApiResource->ApiPaginator
			->expects($this->atLeastOnce())
			->method('paginate')
			->will($this->returnValue($results_without_metadata));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRelatedFieldDependencies')
			->with($related_field_names)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withResult')
			->with()
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithRelatedModelData')
			->with()
			->will($this->returnValue($results_with_metadata));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithMetadata')
			->with()
			->will($this->returnValue($results_with_metadata));
			
		
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFormattedResult')
			->with()
			->will($this->returnValue($results_with_metadata));
			
		$this->ApiResource->forModel($this->test_model);
		
		$expected = $this->ApiResource->returnsApiResponse();
		
		$this->assertEquals($expected, $results_with_metadata);
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$this->assertFalse($this->ApiResource->_single_result);
		
		$expected_paginator_component_settings = array(
			'paramType' => 'querystring',
			'fields' => array(
				'ApiResourceComponentThing.field_one',
				'field_two',
				'field_three'
			),
			'contain' => false,
			'parseTypes' => true,
			'parentModel' => null,
			'callbackFields' => array(
				'attributeOne'
			)
		);
		
		$this->assertEquals(
			$expected_paginator_component_settings,
			$this->ApiResource->ApiPaginator->settings
		);
		
	}
	
	/**
	 * Test Returns Api Response - As Primary Model With Default Object
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseAsPrimaryModelWithDefaultObject() {
		
		$field_map = array(
			'field_one' => 'attributeOne'
		);
		
		$passed_conditions = array();
		
		$field_names = array('ApiResourceComponentThing.field_one');
		
		$conditions = array('field_one' => 'one');
		
		$related_models = array('ApiResourceComponentStuff');
		
		$related_field_names = array('field_two', 'field_three');
		
		$default_object = array('result' => 'default');
		
		$results_without_metadata = array();
		
		$results_with_metadata = array();
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'returnsFieldMap',
				'withFieldMap',
				'withFields',
				'withPassedConditions',
				'rendersConditions',
				'hasRequiredRelations',
				'withRelatedModels',
				'hasRelatedFieldNames',
				'withRelatedFieldDependencies',
				'withResult',
				'returnsResultWithMetadata',
				'returnsResultWithRelatedModelData',
				'returnsFormattedResult',
				'withRequiredAttributes',
				'hasRequiredAttributes'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array(
				'withFieldMap',
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->action = 'index';
		
		$this->ApiResource->Controller->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array('attributeOne' => array())));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array('attributeOne' => array())))
			->will($this->returnValue($field_map));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('isDefaultObjectEnabled')
			->with()
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getDefaultObject')
			->with($this->equalTo(null))
			->will($this->returnValue($default_object));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with(null)
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('hasRequiredAttributes')
			->with()
			->will($this->returnValue(array('attributeOne')));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRequiredAttributes')
			->with($this->equalTo(array('attributeOne')))
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFieldMap')
			->with('read')
			->will($this->returnValue($field_map));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getAllFieldNames')
			->with()
			->will($this->returnValue($field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldNames')
			->with($field_map)
			->will($this->returnValue($field_names));
			
		$this->ApiResource->Query
			->expects($this->once())
			->method('withFieldMap')
			->with($field_map)
			->will($this->returnValue($this->ApiResource->Query));
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($passed_conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFieldMap')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFields')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRequiredRelations')
			->with()
			->will($this->returnValue($related_models));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withRelatedModels')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRelatedFieldNames')
			->with()
			->will($this->returnValue($related_field_names));
		
		$this->ApiResource->ApiPaginator
			->expects($this->atLeastOnce())
			->method('paginate')
			->will($this->returnValue($results_without_metadata));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRelatedFieldDependencies')
			->with($related_field_names)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withResult')
			->with()
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithMetadata')
			->with()
			->will($this->returnValue($results_with_metadata));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithRelatedModelData')
			->with()
			->will($this->returnValue(array(
				0 => array($this->test_model => $default_object)
			)));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFormattedResult')
			->with()
			->will($this->returnValue(array(0 => $default_object)));
			
		$this->ApiResource->forModel($this->test_model);
		
		$result = $this->ApiResource->returnsApiResponse();
		
		$expected = array(0 => $default_object);
		
		$this->assertEquals($expected, $result);
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$this->assertFalse($this->ApiResource->_single_result);
		
		$expected_paginator_component_settings = array(
			'paramType' => 'querystring',
			'fields' => array(
				'ApiResourceComponentThing.field_one',
				'field_two',
				'field_three'
			),
			'contain' => false,
			'parseTypes' => true,
			'parentModel' => null,
			'callbackFields' => array(
				'attributeOne'
			)
		);
		
		$this->assertEquals(
			$expected_paginator_component_settings,
			$this->ApiResource->ApiPaginator->settings
		);
		
	}
	
	/**
	 * Test Returns Api Response - As Primary Model and Requiring a Single Result
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseAsPrimaryModelAndRequiringASingleResult() {
		
		$field_map = array(
			'field_one' => 'attributeOne'
		);
		
		$passed_conditions = array();
		
		$field_names = array('field_one');
		
		$conditions = array('field_one' => 'one');
		
		$related_models = array('ApiResourceComponentStuff');
		
		$related_field_names = array('field_two', 'field_three');
		
		$results = array(
			// this one should not come back because we use `array_pop` to
			// take the last iteration of the array...
			array('result' => 'this_should_not_come_back_with_response'),
			// this one should come back because it is `array_pop`ped
			array('result' => 'test_results')
		);
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'returnsFieldMap',
				'withFieldMap',
				'withFields',
				'withPassedConditions',
				'rendersConditions',
				'hasRequiredRelations',
				'withRelatedModels',
				'hasRelatedFieldNames',
				'withRelatedFieldDependencies',
				'withResult',
				'returnsResultWithMetadata',
				'returnsResultWithRelatedModelData',
				'returnsFormattedResult',
				'hasRequiredAttributes',
				'withRequiredAttributes'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array(
				'withFieldMap',
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->action = 'index';
		
		$this->ApiResource->Controller->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array('attributeOne' => array())));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array('attributeOne' => array())))
			->will($this->returnValue($field_map));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with(null)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('hasRequiredAttributes')
			->with()
			->will($this->returnValue(array('attributeOne')));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRequiredAttributes')
			->with($this->equalTo(array('attributeOne')))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsFieldMap')
			->with('read')
			->will($this->returnValue($field_map));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getAllFieldNames')
			->with()
			->will($this->returnValue($field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldNames')
			->with($field_map)
			->will($this->returnValue($field_names));
			
		$this->ApiResource->Query
			->expects($this->once())
			->method('withFieldMap')
			->with($field_map)
			->will($this->returnValue($this->ApiResource->Query));
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($passed_conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFieldMap')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFields')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRequiredRelations')
			->with()
			->will($this->returnValue($related_models));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withRelatedModels')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRelatedFieldNames')
			->with()
			->will($this->returnValue($related_field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('hasField')
			->with('deleted')
			->will($this->returnValue(false));
			
		$this->ApiResource->ApiPaginator
			->expects($this->atLeastOnce())
			->method('paginate')
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRelatedFieldDependencies')
			->with($related_field_names)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withResult')
			->with($results)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithMetadata')
			->with()
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithRelatedModelData')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFormattedResult')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->requiringASingleResult();
		
		$expected = $this->ApiResource->returnsApiResponse();
		
		$this->assertEquals($expected, $results[1]);
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$this->assertTrue($this->ApiResource->_single_result);
		
		$expected_paginator_component_settings = array(
			'paramType' => 'querystring',
			'fields' => array_merge($field_names, $related_field_names),
			'contain' => false,
			'parseTypes' => true,
			'parentModel' => null,
			'callbackFields' => array(
				'attributeOne'
			)
		);
		
		$this->assertEquals(
			$expected_paginator_component_settings,
			$this->ApiResource->ApiPaginator->settings
		);
		
	}
	
	/**
	 * Test Returns Api Response - Model Limit Set To All
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseModelLimitSetToAll() {
		
		$field_map = array(
			'field_one' => 'attributeOne'
		);
		
		$passed_conditions = array();
		
		$field_names = array('field_one');
		
		$conditions = array('field_one' => 'one');
		
		$related_models = array('ApiResourceComponentStuff');
		
		$related_field_names = array('field_two', 'field_three');
		
		$results = array(
			// this one should not come back because we use `array_pop` to
			// take the last iteration of the array...
				array('result' => 'this_should_not_come_back_with_response'),
			// this one should come back because it is `array_pop`ped
			array('result' => 'test_results')
		);
		
		$limit = PHP_INT_MAX;
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'returnsFieldMap',
				'withFieldMap',
				'withFields',
				'withPassedConditions',
				'rendersConditions',
				'hasRequiredRelations',
				'withRelatedModels',
				'hasRelatedFieldNames',
				'withRelatedFieldDependencies',
				'withResult',
				'returnsResultWithMetadata',
				'returnsResultWithRelatedModelData',
				'returnsFormattedResult',
				'hasRequiredAttributes',
				'withRequiredAttributes'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array(
				'withFieldMap',
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->action = 'index';
		
		$this->ApiResource->Controller->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Controller->{$this->test_model}->limit = 'all';
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array('attributeOne' => array())));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array('attributeOne' => array())))
			->will($this->returnValue($field_map));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with(null)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('hasRequiredAttributes')
			->with()
			->will($this->returnValue(array('attributeOne')));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRequiredAttributes')
			->with($this->equalTo(array('attributeOne')))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsFieldMap')
			->with('read')
			->will($this->returnValue($field_map));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getAllFieldNames')
			->with()
			->will($this->returnValue($field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldNames')
			->with($field_map)
			->will($this->returnValue($field_names));
			
		$this->ApiResource->Query
			->expects($this->once())
			->method('withFieldMap')
			->with($field_map)
			->will($this->returnValue($this->ApiResource->Query));
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($passed_conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFieldMap')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFields')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRequiredRelations')
			->with()
			->will($this->returnValue($related_models));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withRelatedModels')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRelatedFieldNames')
			->with()
			->will($this->returnValue($related_field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('hasField')
			->with('deleted')
			->will($this->returnValue(false));
			
		$this->ApiResource->ApiPaginator
			->expects($this->atLeastOnce())
			->method('paginate')
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRelatedFieldDependencies')
			->with($related_field_names)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withResult')
			->with($results)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithMetadata')
			->with()
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithRelatedModelData')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFormattedResult')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->requiringASingleResult();
		
		$expected = $this->ApiResource->returnsApiResponse();
		
		$this->assertEquals($expected, $results[1]);
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$this->assertTrue($this->ApiResource->_single_result);
		
		$expected_paginator_component_settings = array(
			'paramType' => 'querystring',
			'fields' => array_merge($field_names, $related_field_names),
			'parentModel' => null,
			'contain' => false,
			'parseTypes' => true,
			'limit' => $limit,
			'maxLimit' => $limit,
			'callbackFields' => array(
				'attributeOne'
			)
		);
		
		$this->assertEquals(
			$expected_paginator_component_settings,
			$this->ApiResource->ApiPaginator->settings
		);
		
	}
	
	/**
	 * Test Returns Api Response - Model Limit Set To Number
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsApiResponseModelLimitEqualAll() {
		
		$field_map = array(
			'field_one' => 'attributeOne'
		);
		
		$passed_conditions = array();
		
		$field_names = array('field_one');
		
		$conditions = array('field_one' => 'one');
		
		$related_models = array('ApiResourceComponentStuff');
		
		$related_field_names = array('field_two', 'field_three');
		
		$results = array(
			// this one should not come back because we use `array_pop` to
			// take the last iteration of the array...
				array('result' => 'this_should_not_come_back_with_response'),
			// this one should come back because it is `array_pop`ped
			array('result' => 'test_results')
		);
		
		$limit = PHP_INT_MAX;
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'returnsFieldMap',
				'withFieldMap',
				'withFields',
				'withPassedConditions',
				'rendersConditions',
				'hasRequiredRelations',
				'withRelatedModels',
				'hasRelatedFieldNames',
				'withRelatedFieldDependencies',
				'withResult',
				'returnsResultWithMetadata',
				'returnsResultWithRelatedModelData',
				'returnsFormattedResult',
				'hasRequiredAttributes',
				'withRequiredAttributes'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Query = $this->getMock(
			'ApiQueryComponent',
			array(
				'withFieldMap',
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->action = 'index';
		
		$this->ApiResource->Controller->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Controller->{$this->test_model}->limit = $limit;
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array('attributeOne' => array())));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array('attributeOne' => array())))
			->will($this->returnValue($field_map));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with(null)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('hasRequiredAttributes')
			->with()
			->will($this->returnValue(array('attributeOne')));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRequiredAttributes')
			->with($this->equalTo(array('attributeOne')))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsFieldMap')
			->with('read')
			->will($this->returnValue($field_map));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getAllFieldNames')
			->with()
			->will($this->returnValue($field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getFieldNames')
			->with($field_map)
			->will($this->returnValue($field_names));
			
		$this->ApiResource->Query
			->expects($this->once())
			->method('withFieldMap')
			->with($field_map)
			->will($this->returnValue($this->ApiResource->Query));
		
		$this->ApiResource->Query
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($passed_conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFieldMap')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withFields')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRequiredRelations')
			->with()
			->will($this->returnValue($related_models));
		
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withRelatedModels')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('hasRelatedFieldNames')
			->with()
			->will($this->returnValue($related_field_names));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('hasField')
			->with('deleted')
			->will($this->returnValue(false));
			
		$this->ApiResource->ApiPaginator
			->expects($this->atLeastOnce())
			->method('paginate')
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('withRelatedFieldDependencies')
			->with($related_field_names)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withResult')
			->with($results)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithMetadata')
			->with()
			->will($this->returnValue($results));
		
		$this->ApiResource
			->expects($this->once())
			->method('returnsResultWithRelatedModelData')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource
			->expects($this->once())
			->method('returnsFormattedResult')
			->with()
			->will($this->returnValue($results));
			
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->requiringASingleResult();
		
		$expected = $this->ApiResource->returnsApiResponse();
		
		$this->assertEquals($expected, $results[1]);
		
		$this->assertEmpty($this->ApiResource->_parent_model);
		
		$this->assertEquals($this->test_model, $this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_passed_conditions);
		
		$this->assertTrue($this->ApiResource->_single_result);
		
		$expected_paginator_component_settings = array(
			'paramType' => 'querystring',
			'fields' => array_merge($field_names, $related_field_names),
			'parentModel' => null,
			'contain' => false,
			'parseTypes' => true,
			'limit' => $limit,
			'callbackFields' => array(
				'attributeOne'
			)
		);
		
		$this->assertEquals(
			$expected_paginator_component_settings,
			$this->ApiResource->ApiPaginator->settings
		);
		
	}
	
	/**
	 * Test Returns Result With Related Model Data - With empty `$_model`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsResultWithRelatedModelDataWithEmptyModel() {
		
		$this->assertFalse($this->ApiResource->returnsResultWithRelatedModelData());
		
	}
	
	/**
	 * Test Returns Result With Related Model Data - With Empty `$_result`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsResultWithRelatedModelDataWithEmptyResult() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->assertEmpty($this->ApiResource->returnsResultWithRelatedModelData());
		
		$this->assertEmpty($this->ApiResource->_model);
		
	}
	
	/**
	 * Test Returns Result With Related Model Data - With Empty `$_related_models`
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsResultWithRelatedModelDataWithEmptyRelatedModels() {
		
		$result = array('test');
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withResult($result);
		
		$this->assertEquals($result, $this->ApiResource->returnsResultWithRelatedModelData());
		
		$this->assertEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_result);
		
	}
	
	/**
	 * Test Returns Result With Related Model Data - With Empty `$find`
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testReturnsResultWithRelatedModelDataWithEmptyFindVar() {
		
		$result = array(
			array(
				'ApiResourceComponentThing' => array(
					'id' => 'thing_one',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'OtherOne' => array(
					'thing_id' => 'thing_one',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			),
			array(
				'ApiResourceComponentThing' => array(
					'id' => 'thing_two',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'OtherTwo' => array(
					'thing_id' => 'thing_two',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			),
			array(
				'ApiResourceComponentThing' => array(
					'id' => 'thing_three',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'OtherThree' => array(
					'thing_id' => 'thing_three',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			)
		);
		
		$related_models = array(
			'OtherOne' => array(
				'type' => 'belongsTo',
				'foreignKey' => 'id',
				'conditions' => ''
			),
			'OtherTwo' => array(
				'type' => 'hasOne',
				'foreignKey' => 'thing_id',
				'conditions' => ''
			),
			'OtherThree' => array(
				'type' => 'hasMany',
				'foreignKey' => 'thing_id',
				'conditions' => ''
			)
		);
		
		$related_field_dependencies = array(
			'ApiResourceComponentThing.id'
		);
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array(
				'withParentModel',
				'withPassedConditions',
				'returnsApiResponse'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->Controller
			->{$this->test_model}
			->OtherOne = $this->getMock('OtherOne');
		
		$this->ApiResource->Controller
			->{$this->test_model}
			->OtherOne
			->primaryKey = 'id';
		
		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withParentModel')
			->with($this->test_model)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('withPassedConditions')
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->atLeastOnce())
			->method('returnsApiResponse')
			->with()
			->will($this->returnValue(array()));
		
		$results = $this->ApiResource
			->forModel($this->test_model)
			->withResult($result)
			->withRelatedModels($related_models)
			->withRelatedFieldDependencies($related_field_dependencies)
			->returnsResultWithRelatedModelData();
		
		$expected = array(
			array(
				'OtherThree' => null,
				'OtherTwo' => null,
				'OtherOne' => array(
					'thing_id' => 'thing_one',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'ApiResourceComponentThing' => array(
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			),
			array(
				'OtherThree' => null,
				'OtherTwo' => array(
					'thing_id' => 'thing_two',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'OtherOne' => null,
				'ApiResourceComponentThing' => array(
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			),
			array(
				'OtherThree' => array(
					'thing_id' => 'thing_three',
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				),
				'OtherTwo' => null,
				'OtherOne' => null,
				'ApiResourceComponentThing' => array(
					'field_one' => 'value_one',
					'field_two' => 'value_two'
				)
			)
		);
		
		$this->assertEquals($expected, $results);
		
		$this->assertNotEmpty($this->ApiResource->_model);
		
		$this->assertEmpty($this->ApiResource->_result);
		
		$this->assertEmpty($this->ApiResource->_related_models);
		
		$this->assertEmpty($this->ApiResource->_related_field_dependencies);
		
	}
	
	/**
	 * Test Returns Result With Metadata - With a Single Piece of Metadata
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testReturnsResultWithMetadataWithASinglePieceOfMetadata() {
		
		$attributes = array(
			'id' => array(
				'type' => 'int',
				'query' => true,
				'sort' => false
			),
			'isKey' => array(
				'type' => 'string',
				'field' => 'Metadatum.key',
				'query' => true,
				'sort' => false
			)
		);
		
		$field_map = array(
			'id' => 'id',
			'Metadatum.key' => 'isKey'
		);
			
		$this->ApiResource->Controller->{$this->test_model} = $this->getMock(
			$this->test_model,
			array(
				'id',
				'getMeta',
				'attributes'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors = $this->getMock(
			'Behaviors',
			array(
				'loaded'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors
			->expects($this->at(0))
			->method('loaded')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->Behaviors
			->expects($this->at(1))
			->method('loaded')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->with()
			->will($this->returnValue($attributes));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('id')
			->with($this->equalTo(1))
			->will($this->returnValue($this->ApiResource->Controller->{$this->test_model}));
		
		$get_meta_results = 'value';
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getMeta')
			->with($this->equalTo('key'))
			->will($this->returnValue($get_meta_results));
			
		$with_results_return = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1
				)
			)
		);
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withMetadataFields(array('key'))
			->withFieldMap($field_map)
			->withResult($with_results_return)
			->returnsResultWithMetadata();
		
		$expected = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1,
					'Metadatum' => array(
						'key' => $get_meta_results
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Returns Result With Metadata - With a Single Piece of Metadata
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testReturnsResultWithMetadataDataTypeJuggling() {
		
		$attributes = array(
			'id' => array(
				'type' => 'int',
				'query' => true,
				'sort' => false
			),
			'isStringAttribute' => array(
				'type' => 'string',
				'field' => 'Metadatum.is_string_attribute',
				'query' => true,
				'sort' => false
			),
			'isBooleanZeroAttribute' => array(
				'type' => 'boolean',
				'field' => 'Metadatum.is_boolean_zero_attribute',
				'query' => true,
				'sort' => false
			),
			'isBooleanOneAttribute' => array(
				'type' => 'boolean',
				'field' => 'Metadatum.is_boolean_one_attribute',
				'query' => true,
				'sort' => false
			),
			'isIntegerAttribute' => array(
				'type' => 'int',
				'field' => 'Metadatum.is_integer_attribute',
				'query' => true,
				'sort' => false
			)
		);
		
		$field_map = array(
			'id' => 'id',
			'Metadatum.is_string_attribute' => 'isStringAttribute',
			'Metadatum.is_boolean_zero_attribute' => 'isBooleanZeroAttribute',
			'Metadatum.is_boolean_one_attribute' => 'isBooleanOneAttribute',
			'Metadatum.is_integer_attribute' => 'isIntegerAttribute'
		);
			
		$this->ApiResource->Controller->{$this->test_model} = $this->getMock(
			$this->test_model,
			array(
				'id',
				'getMeta',
				'attributes',
				'convertDataType'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors = $this->getMock(
			'Behaviors',
			array(
				'loaded'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors
			->expects($this->any())
			->method('loaded')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($attributes));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('id')
			->with($this->equalTo(1))
			->will($this->returnValue($this->ApiResource->Controller->{$this->test_model}));
		
		$get_meta_results = array(
			'is_string_attribute' => 'text',
			'is_boolean_zero_attribute' => '0',
			'is_boolean_one_attribute' => '1',
			'is_integer_attribute' => '1'
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getMeta')
			->with()
			->will($this->returnValue($get_meta_results));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(3))
			->method('convertDataType')
			->with('text', 'string')
			->will($this->returnValue('text'));

		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(4))
			->method('convertDataType')
			->with('0', 'boolean')
			->will($this->returnValue(false));

		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(5))
			->method('convertDataType')
			->with('1', 'boolean')
			->will($this->returnValue(true));

		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->at(6))
			->method('convertDataType')
			->with('1', 'int')
			->will($this->returnValue(1));
			
		$metadata_fields = array(
			'is_string_attribute',
			'is_boolean_zero_attribute',
			'is_boolean_one_attribute',
			'is_integer_attribute'
		);
		
		$with_results_return = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1
				)
			)
		);
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withMetadataFields($metadata_fields)
			->withFieldMap($field_map)
			->withResult($with_results_return)
			->returnsResultWithMetadata();
		
		$expected = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1,
					'Metadatum' => array(
						'is_string_attribute' => 'text',
						'is_boolean_zero_attribute' => false,
						'is_boolean_one_attribute' => true,
						'is_integer_attribute' => 1
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Returns Result With Metadata - With Multiple Pieces of Metadata
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testReturnsResultWithMetadataWithMultiplePiecesOfMetadata() {
		
		$attributes = array(
			'id' => array(
				'type' => 'int',
				'query' => true,
				'sort' => false
			),
			'isKeyOne' => array(
				'type' => 'string',
				'field' => 'Metadatum.key1',
				'query' => true,
				'sort' => false
			),
			'isKeyTwo' => array(
				'type' => 'string',
				'field' => 'Metadatum.key2',
				'query' => true,
				'sort' => false
			),
			'isKeyThree' => array(
				'type' => 'string',
				'field' => 'Metadatum.key3',
				'query' => true,
				'sort' => false
			)
		);
		
		$field_map = array(
			'id' => 'id',
			'Metadatum.key1' => 'isKeyOne',
			'Metadatum.key2' => 'isKeyTwo',
			'Metadatum.key3' => 'isKeyThree',
		);
			
		$this->ApiResource->Controller->{$this->test_model} = $this->getMock(
			$this->test_model,
			array(
				'id',
				'getMeta',
				'attributes'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors = $this->getMock(
			'Behaviors',
			array(
				'loaded'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->Behaviors
			->expects($this->at(0))
			->method('loaded')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->Behaviors
			->expects($this->at(1))
			->method('loaded')
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->with()
			->will($this->returnValue($attributes));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('id')
			->with($this->equalTo(1))
			->will($this->returnValue($this->ApiResource->Controller->{$this->test_model}));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('getMeta')
			->with()
			->will($this->returnValue(array(
				'key1' => 'value1',
				'key2' => 'value2',
				'key3' => 'value3'
			)));
			
		$with_results_return = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1
				)
			)
		);
		
		$result = $this->ApiResource
			->forModel($this->test_model)
			->withMetadataFields(array('key1', 'key3'))
			->withFieldMap($field_map)
			->withResult($with_results_return)
			->returnsResultWithMetadata();
		
		$expected = array(
			array(
				$this->ApiResource->Controller->{$this->test_model}->alias => array(
					'id' => 1,
					'Metadatum' => array(
						'key1' => 'value1',
						'key3' => 'value3'
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Save - With Empty Primary Model Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveEmptyModelData() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_parent_model = 'ApiResourceComponentThing';
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_id = 0;
		$this->ApiResource->_data = array(
			'ApiResourceComponentThing' => array()
		);

		$this->ApiResource
			->expects($this->at(0))
			->method('withData')
			->with()
			->will($this->returnValue($this->ApiResource));

		$this->ApiResource
			->expects($this->at(1))
			->method('forModel')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(2))
			->method('withFieldExceptions')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(3))
			->method('withId')
			->with()
			->will($this->returnValue($this->ApiResource));

		$this->ApiResource
			->expects($this->at(4))
			->method('withParentModel')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(5))
			->method('forModel')
			->with('ApiResourceComponentThing')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(6))
			->method('withId')
			->with(0)
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(7))
			->method('withData')
			->with($this->equalTo(array(
				'ApiResourceComponentThing' => array()
			)))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(8))
			->method('withFieldExceptions')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('saveOne')
			->will($this->returnValue(true));
		
		$test = $this->ApiResource->save();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save - Primary on Create Shouldnt Have Id
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryCreateDataShouldNotHaveID() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			'ApiResourceComponentThing' => array(
				'id' => 1,
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4001));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary With Empty Field Map
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryEmptyFieldMap() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			'ApiResourceComponentThing' => array(
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		// Assume Empty Data Will Not Validate
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4012));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary Update With Id Mismatch
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryUpdateIDMismatch() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 2,
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4002));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary Create and Permissions Can't Create
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryCreateCantCreate() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
						
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(false));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4013));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary Update and Permissions Can't Update
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryUpdateCantUpdate() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
						
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(false));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4013));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary Fails Validation
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimaryFailsValidation() {
		
		$this->ApiResource = $this->getMock('ApiResourceComponentDouble', array(
			'setValidationErrors'
		), array($this->ComponentCollection));
		
		$this->ApiResource->modelClass = $this->test_model;
		
		$this->ApiResource->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => ''
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4012));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary and Save Fails
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimarySaveFails() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('save')
			->will($this->returnValue(false));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5001));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save - Primary and Save Succeeds
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSavePrimarySaveSucceeds() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username',
				'options' => array(
					'sub_key' => array('foo' => '123', 'bar' => '123')
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'username' => array('type' => 'string'),
				'options.subKey' => array('field' => 'options.sub_key', 'type' => 'string')
			)));
			
		$this->ApiResource->Controller->{$this->test_model}->Behaviors->attach(
			'SpecialFields.SpecialFields',
			array(
				'json' => array('options')
			)
		);
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'username' => array('type' => 'string'),
				'options.subKey' => array('field' => 'options.sub_key', 'type' => 'string')
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'username' => 'username',
				'options.sub_key' => 'options.subKey'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->with()
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array()));
		
		$test = $this->ApiResource->save();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save - Related Data Passed Is Not Related
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedDataNotRelated() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			),
			'FormResponse' => array(
				'id' => 1,
				'value' => 'value'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4007));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
		$this->assertEqual(
			array('formResponse' => array('FormResponse' => array('Not related'))),
			$this->ApiResource->_validation_errors
		);
		
	}
	
	/**
	 * Test Save Related - Related Not Allowed
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedRelatedNotAllowed() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				'id' => 1,
				'value' => 'value'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(false));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4015));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
		$this->assertEqual(
			array('apiResourceComponentStuff' => array('ApiResourceComponentStuff' => array('Related data not allowed'))),
			$this->ApiResource->_validation_errors
		);
		
	}
	
	/**
	 * Test Save Related - Has One Data Should Not Be Indexed
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasOneShouldNotBeIndexed() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				0 => array(
					'id' => 1,
					'fname' => 'First'
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array('save')
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4008));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
		// Test for Validation Error
		
	}
	
	/**
	 * Test Save - Related Has One Data Id Does Not Belong to Primary
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasOneIDDoesNotBelong() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				'id' => 2,
				'fname' => 'First'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}->hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => '',
				'offset' => '',
				'counterQuery' => '',
				'conditions' => array('ApiResourceComponentStuff.name' => 'ABC')
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array('find')
		);
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.thing_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array('ApiResourceComponentStuff' => array('id' => 1))));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4002));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save Related - Has Many Data Passed Should Be Indexed
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasOne() {
		
		$data = array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				'fname' => 'First'
			)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData($data);
		
		$this->ApiResource->Controller->{$this->test_model}->hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => '',
				'offset' => '',
				'counterQuery' => '',
				'conditions' => array('ApiResourceComponentStuff.name' => 'ABC')
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'find',
				'save',
				'validates',
				'create',
				'set'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'firstName' => array(
					'field' => 'fname'
				)
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'fname' => 'firstName'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.thing_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array('ApiResourceComponentStuff' => array('id' => 1))));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('create')
			->with(false)
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('set')
			->with(array(
				'ApiResourceComponentStuff' => array(
					'fname' => 'First',
					'id' => 1,
					'thing_id' => 1
				)
			))
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('save')
			->with()
			->will($this->returnValue(true));
		
		$test = $this->ApiResource->save();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save Related - Has Many Data Passed Should Be Indexed
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasManyShouldBeIndexed() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'username' => 'username'
			),
			'Form' => array(
				'id' => 1,
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('Form'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne',
				'Form' => 'hasMany'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->Form = $this->getMock('Form');
		
		$this->ApiResource->Controller->{$this->test_model}->Form
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->Form
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4009));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
		// Test for Validation Error
		
	}
	
	/**
	 * Test Save Related - Hasmany Data Id Does Not Belong to Primary
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasManyIDDoesNotBelong() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				0 => array(
					'id' => 2,
					'name' => 'Name'
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasMany'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->hasMany = array(
			'ApiResourceComponentStuff' => array(
				'foreignKey' => 'user_id',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'find',
				'save'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.id' => 2,
						'ApiResourceComponentStuff.user_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4003));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save Related - Has Many With Unique Id and Unique Conditions Are Empty
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasManyUniqueIDEmptyConditions() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				0 => array(
					'name' => 'Name'
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasMany'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->hasMany = array(
			'ApiResourceComponentStuff' => array(
				'foreignKey' => 'user_id',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'hasUniqueID',
				'getUniqueConditions',
				'save'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('hasUniqueID')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getUniqueConditions')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4006));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save Related - Has Many With Unique Id
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasManyUniqueID() {

		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				0 => array(
					'name' => 'Name'
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasMany'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->hasMany = array(
			'ApiResourceComponentStuff' => array(
				'foreignKey' => 'user_id',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'hasUniqueID',
				'getUniqueConditions',
				'getUniqueID',
				'create',
				'set',
				'save'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('hasUniqueID')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getUniqueConditions')
			->will($this->returnValue(array('ApiResourceComponentStuff.id' => 1)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getUniqueID')
			->will($this->returnValue(1));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('getUniqueID')
			->with($this->equalTo(array('ApiResourceComponentStuff.id' => 1)));
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('save')
			->with()
			->will($this->returnValue(true));
		
		$test = $this->ApiResource->save();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save Related - Has Many
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedHasMany() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				0 => array(
					'name' => 'Name'
				),
				1 => array(
					'id' => 99,
					'name' => 'Other Name'
				)
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasMany',
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->hasMany = array(
			'ApiResourceComponentStuff' => array(
				'foreignKey' => 'user_id',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'hasUniqueID',
				'save',
				'find',
				'create',
				'set'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'attrName'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'attrName'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('hasUniqueID')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('first'), 
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.id' => 99,
						'ApiResourceComponentStuff.user_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => array(
					'id' => 99
				)
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->exactly(2))
			->method('create')
			->with(false)
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->exactly(2))
			->method('set')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->exactly(2))
			->method('save')
			->with()
			->will($this->returnValue(true));

		$test = $this->ApiResource->save();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save Related - Fails Validation
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedFailsValidation() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				'fname' => ''
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}->hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => '',
				'offset' => '',
				'counterQuery' => '',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'find',
				'validates',
				'save',
				'create',
				'set'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'firstName' => array(
					'field' => 'fname'
				)
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'fname' => 'firstName'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->id = 1;
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.thing_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array('ApiResourceComponentStuff' => array('id' => 1))));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('create')
			->will($this->returnValue(true));

		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('set')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(false));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->never())
			->method('save');
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4012));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save Related - Save Fails
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testSaveRelatedFailsSave() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'username' => 'username'
			),
			'ApiResourceComponentStuff' => array(
				'fname' => ''
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}->hasOne = array(
			'ApiResourceComponentStuff' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'thing_id',
				'dependent' => true,
				'exclusive' => false,
				'finderQuery' => '',
				'fields' => '',
				'offset' => '',
				'counterQuery' => '',
				'conditions' => array(
					'ApiResourceComponentStuff.name' => 'ABC'
				)
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
	
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('isRelationSaveable')
			->with($this->equalTo('ApiResourceComponentStuff'))
			->will($this->returnValue(true));
			
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'username'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canCreate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'username' => 'username'
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getAssociated')
			->will($this->returnValue(array(
				'ApiResourceComponentStuff' => 'hasOne'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff = $this->getMock(
			'ApiResourceComponentStuff',
			array(
				'attributes',
				'getFieldMap',
				'find',
				'validates',
				'save',
				'create',
				'set'
			)
		);
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'firstName' => array(
					'field' => 'fname'
				)
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'fname' => 'firstName'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->any())
			->method('field')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentStuff.id'),
					'conditions' => array(
						'ApiResourceComponentStuff.thing_id' => 1,
						'ApiResourceComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array('ApiResourceComponentStuff' => array('id' => 1))));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('create')
			->will($this->returnValue(true));

		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('set')
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}->ApiResourceComponentStuff
			->expects($this->once())
			->method('save')
			->will($this->returnValue(false));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5001));
		
		$test = $this->ApiResource->save();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Save All - Validate And Save Pass
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void 
	 */
	public function testSaveAllValidateAndSavePass() {
		
		$data = array(
			0 => array(
				$this->test_model => array(
					'id' => 1,
					'username' => 'user1'
				)
			),
			1 => array(
				$this->test_model => array(
					'username' => 'user2'
				)
			)
		);
		
		$parent_model = 'ApiResourceComponentThing';
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'withData',
				'forModel',
				'withId',
				'withFieldExceptions',
				'save',
				'withTransactions',
				'setValidationIndex',
				'skipErrorsAndSave',
				'validateOnly'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->ApiResourceComponentThing = $this->getMock(
			'ApiResourceComponentThing',
			array(
				'begin',
				'rollback',
				'commit'
			)
		);
		
		$this->ApiResource->_data = $data;
		
		$this->ApiResource->_model = $parent_model;
		
		$this->ApiResource->_field_exceptions = array();
		
		$this->ApiResource
			->expects($this->at(0))
			->method('withData');
			
		$this->ApiResource
			->expects($this->at(1))
			->method('forModel');
			
		$this->ApiResource
			->expects($this->at(2))
			->method('withFieldExceptions');
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('begin');
			
		$at_index = 2;

		foreach ($data as $index => $indexedData) {
			
			$id = !empty($indexedData[$parent_model]['id']) ? $indexedData[$parent_model]['id'] : 0;
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('setValidationIndex')
				->with(array($index))
				->will($this->returnValue(null));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('forModel')
				->with($parent_model)
				->will($this->returnValue($this->ApiResource));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withId')
				->with($id)
				->will($this->returnValue($this->ApiResource));

			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withData')
				->with($indexedData)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withFieldExceptions')
				->with(array())
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withTransactions')
				->with(false)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('save')
				->with()
				->will($this->returnValue(true));
		
		}
		
		$at_index++;
		
		$this->ApiResource
			->expects($this->at($at_index))
			->method('validateOnly')
			->with()
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('commit');

		$this->assertTrue($this->ApiResource->saveAll());
		
	}
	
	/**
	 * Test Save All - Validate And Save Fail
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void 
	 */
	public function testSaveAllValidateAndSaveFail() {
		
		$data = array(
			0 => array(
				$this->test_model => array(
					'id' => 1,
					'username' => 'user1'
				)
			),
			1 => array(
				$this->test_model => array(
					'username' => 'user2'
				)
			)
		);
		
		$parent_model = 'ApiResourceComponentThing';
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'withData',
				'forModel',
				'withId',
				'withFieldExceptions',
				'save',
				'withTransactions',
				'setValidationIndex',
				'skipErrorsAndSave',
				'validateOnly'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->ApiResourceComponentThing = $this->getMock(
			'ApiResourceComponentThing',
			array(
				'begin',
				'rollback',
				'commit'
			)
		);
		
		$this->ApiResource->_data = $data;
		
		$this->ApiResource->_model = $parent_model;
		
		$this->ApiResource->_field_exceptions = array();
		
		$this->ApiResource
			->expects($this->at(0))
			->method('withData');
			
		$this->ApiResource
			->expects($this->at(1))
			->method('forModel');
			
		$this->ApiResource
			->expects($this->at(2))
			->method('withFieldExceptions');
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('begin');
			
		$at_index = 2;

		foreach ($data as $index => $indexedData) {
			
			$id = !empty($indexedData[$parent_model]['id']) ? $indexedData[$parent_model]['id'] : 0;
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('setValidationIndex')
				->with(array($index))
				->will($this->returnValue(null));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('forModel')
				->with($parent_model)
				->will($this->returnValue($this->ApiResource));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withId')
				->with($id)
				->will($this->returnValue($this->ApiResource));

			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withData')
				->with($indexedData)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withFieldExceptions')
				->with(array())
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withTransactions')
				->with(false)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('save')
				->with()
				->will($this->returnValue(false));
		
		}
		
		$at_index++;
		
		$this->ApiResource
			->expects($this->at($at_index))
			->method('skipErrorsAndSave')
			->with()
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('rollback');

		$this->assertFalse($this->ApiResource->saveAll());
		
	}
	
	/**
	 * Test Save All - Validate Only
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void 
	 */
	public function testSaveAllValidateOnly() {
		
		$data = array(
			0 => array(
				$this->test_model => array(
					'id' => 1,
					'username' => 'user1'
				)
			),
			1 => array(
				$this->test_model => array(
					'username' => 'user2'
				)
			)
		);
		
		$parent_model = 'ApiResourceComponentThing';
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'withData',
				'forModel',
				'withId',
				'withFieldExceptions',
				'save',
				'withTransactions',
				'setValidationIndex',
				'skipErrorsAndSave',
				'validateOnly'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->ApiResourceComponentThing = $this->getMock(
			'ApiResourceComponentThing',
			array(
				'begin',
				'rollback',
				'commit'
			)
		);
		
		$this->ApiResource->_data = $data;
		
		$this->ApiResource->_model = $parent_model;
		
		$this->ApiResource->_field_exceptions = array();
		
		$this->ApiResource
			->expects($this->at(0))
			->method('withData');
			
		$this->ApiResource
			->expects($this->at(1))
			->method('forModel');
			
		$this->ApiResource
			->expects($this->at(2))
			->method('withFieldExceptions');
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('begin');
			
		$at_index = 2;

		foreach ($data as $index => $indexedData) {
			
			$id = !empty($indexedData[$parent_model]['id']) ? $indexedData[$parent_model]['id'] : 0;
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('setValidationIndex')
				->with(array($index))
				->will($this->returnValue(null));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('forModel')
				->with($parent_model)
				->will($this->returnValue($this->ApiResource));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withId')
				->with($id)
				->will($this->returnValue($this->ApiResource));

			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withData')
				->with($indexedData)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withFieldExceptions')
				->with(array())
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withTransactions')
				->with(false)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('save')
				->with()
				->will($this->returnValue(true));
		
		}
		
		$at_index++;
		
		$this->ApiResource
			->expects($this->at($at_index))
			->method('validateOnly')
			->with()
			->will($this->returnValue(true));
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('rollback');

		$this->assertTrue($this->ApiResource->saveAll());
		
	}
	
	/**
	 * Test Save All - Skip Errors And Save
	 * 
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void 
	 */
	public function testSaveAllSkipErrorsAndSave() {
		
		$data = array(
			0 => array(
				$this->test_model => array(
					'id' => 1,
					'username' => 'user1'
				)
			),
			1 => array(
				$this->test_model => array(
					'username' => 'user2'
				)
			)
		);
		
		$parent_model = 'ApiResourceComponentThing';
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'withData',
				'forModel',
				'withId',
				'withFieldExceptions',
				'save',
				'withTransactions',
				'setValidationIndex',
				'skipErrorsAndSave',
				'validateOnly'
			),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Controller->ApiResourceComponentThing = $this->getMock(
			'ApiResourceComponentThing',
			array(
				'begin',
				'rollback',
				'commit'
			)
		);
		
		$this->ApiResource->_data = $data;
		
		$this->ApiResource->_model = $parent_model;
		
		$this->ApiResource->_field_exceptions = array();
		
		$this->ApiResource
			->expects($this->at(0))
			->method('withData');
			
		$this->ApiResource
			->expects($this->at(1))
			->method('forModel');
			
		$this->ApiResource
			->expects($this->at(2))
			->method('withFieldExceptions');
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('begin');
			
		$at_index = 2;

		foreach ($data as $index => $indexedData) {
			
			$id = !empty($indexedData[$parent_model]['id']) ? $indexedData[$parent_model]['id'] : 0;
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('setValidationIndex')
				->with(array($index))
				->will($this->returnValue(null));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('forModel')
				->with($parent_model)
				->will($this->returnValue($this->ApiResource));
				
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withId')
				->with($id)
				->will($this->returnValue($this->ApiResource));

			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withData')
				->with($indexedData)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withFieldExceptions')
				->with(array())
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('withTransactions')
				->with(false)
				->will($this->returnValue($this->ApiResource));
			
			$at_index++;
			
			$this->ApiResource
				->expects($this->at($at_index))
				->method('save')
				->with()
				->will($this->returnValue(($index === 0) ? false : true));
		
		}
		
		$at_index++;
		
		$this->ApiResource
			->expects($this->at($at_index))
			->method('skipErrorsAndSave')
			->with()
			->will($this->returnValue(true));
			
		$at_index++;
		
		$this->ApiResource
			->expects($this->at($at_index))
			->method('validateOnly')
			->with()
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->ApiResourceComponentThing
			->expects($this->once())
			->method('commit');

		$this->assertTrue($this->ApiResource->saveAll());
		
	}
	
	/**
	 * Test Batch Update - Empty Data
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateEmptyData() {
		
		$passed_conditions = array('key' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;

		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
								
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array()
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(false));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				$this->test_model . '.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
			
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
					
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4012));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Extra Data
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateExtraData() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => 'Name'
			),
			'ApiResourceComponentStuff' => array(
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4015));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Indexed Data
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateIndexedData() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				0 => array(
					'name' => 'Name'
				),
				1 => array(
					'name' => 'Name 2'
				)
			)
		));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4008));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - With Id
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateWithID() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'id' => 1,
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4001));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Empty Field Map
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateEmptyFieldMap() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(false));

	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4017));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Validation Fails
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateValidationFails() {
		
		$passed_conditions = array('key' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;

		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
								
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => ''
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(false));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				$this->test_model . '.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
			
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
					
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4012));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Save Fails
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateSaveFails() {
		
		$passed_conditions = array('key' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;

		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('save')
			->will($this->returnValue(false));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				$this->test_model . '.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5001));
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update - Empty Conditions
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdateEmptyConditions() {
		
		$passed_conditions = array();
		
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('save')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				'ApiResourceComponentThing.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
			
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with(4017);
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Update
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchUpdate() {
		
		$passed_conditions = array('key' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;
		
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withData(array(
			$this->test_model => array(
				'name' => 'Name'
			)
		));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('validates')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('save')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				'ApiResourceComponentThing.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canUpdate')
			->will($this->returnValue(true));
		
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource->Api
			->expects($this->never())
			->method('setResponseCode');
		
		$test = $this->ApiResource->batchUpdate();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Delete - Empty Model
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testDeleteEmptyModel() {
		
		$this->ApiResource->forModel();
		
		$this->ApiResource->withId();
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5002));
		
		$test = $this->ApiResource->delete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Delete - Empty Id
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testDeleteEmptyID() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId();
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4000));
		
		$test = $this->ApiResource->delete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Delete - Resource Not Found
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testDeleteResourceNotFound() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('exists')
			->will($this->returnValue(false));
			
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('read')
			->will($this->returnValue(false));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4004));
		
		$test = $this->ApiResource->delete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Delete - Permissions = Cant Delete
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testDeletePermissionsCantDelete() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('exists')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(false));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4013));
		
		$test = $this->ApiResource->delete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Delete - Fails
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testDeleteFails() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('exists')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('delete')
			->will($this->returnValue(false));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5002));
		
		$test = $this->ApiResource->delete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Delete
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testDelete() {
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withId(1);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('exists')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(true));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('delete')
			->will($this->returnValue(true));
		
		$test = $this->ApiResource->delete();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Batch Delete - Empty Model
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchDeleteEmptyModel() {
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5002));
		
		$test = $this->ApiResource->batchDelete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Delete - Empty Field Map
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchDeleteEmptyFieldMap() {
		
		$this->ApiResource->forModel($this->test_model);
				
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(false));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array()));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4017));
		
		$test = $this->ApiResource->batchDelete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Delete - Delete Fails
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchDeleteDeleteFails() {
		
		$passed_conditions = array('key' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);

		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
				
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('delete')
			->will($this->returnValue(false));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				'ApiResourceComponentThing.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(true));
		
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5002));
		
		$test = $this->ApiResource->batchDelete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Delete - Empty Conditions
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchDeleteEmptyConditions() {
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('delete')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				'ApiResourceComponentThing.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(true));
		
		$this->ApiResource->Api
			->expects($this->once())
			->method('setResponseCode')
			->with(4017);
		
		$test = $this->ApiResource->batchDelete();
		
		$this->assertFalse($test);
		
	}
	
	/**
	 * Test Batch Delete
	 * 
	 * @since   1.0
	 * @return  void 
	 */
	public function testBatchDelete() {
		
		$passed_conditions = array('attribute' => 'value');
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponentDouble',
			array(
				'rendersConditions'
			),
			array($this->ComponentCollection)
		);

		$this->ApiResource->modelClass = $this->test_model;

		$this->ApiResource->{$this->test_model} = $this->Model;
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);

		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->withPassedConditions($passed_conditions);
		
		$this->ApiResource->Api = $this->getMock(
			'ApiComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->Permissions = $this->getMock(
			'ApiPermissionsComponent',
			array(),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource->forModel($this->test_model);
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('attributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('getFieldMap')
			->will($this->returnValue(array(
				'id' => 'id',
				'name' => 'name'
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('find')
			->will($this->returnValue(array(
				1 => 1,
				3 => 3
			)));
		
		$this->ApiResource->Controller->{$this->test_model}
			->expects($this->any())
			->method('delete')
			->will($this->returnValue(true));
	
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withAttributes')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('on')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('allowAttributes')
			->will($this->returnValue(array(
				'id',
				'name'
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('withFields')
			->will($this->returnValue($this->ApiResource->Permissions));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('requireConditions')
			->will($this->returnValue(array(
				'ApiResourceComponentThing.id' => array(1, 3)
			)));
		
		$this->ApiResource->Permissions
			->expects($this->any())
			->method('canDelete')
			->will($this->returnValue(true));
		
		$conditions = array(
			$this->Model->alias .'.id' => array(1,3)
		);

		$this->ApiResource
			->expects($this->once())
			->method('rendersConditions')
			->with()
			->will($this->returnValue($conditions));
			
		$this->ApiResource->Api
			->expects($this->never())
			->method('setResponseCode');
		
		$test = $this->ApiResource->batchDelete();
		
		$this->assertTrue($test);
		
	}
	
	/**
	 * Test Save Associated Has One - No Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneNoModel() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(5001));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - No Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneNoData() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4005));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - Data Not Keyed by Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneDataNotKeyedByModel() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_data = array('key' => 'value');
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4005));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - With Numeric Array Keys
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneWithNumericArrayKeys() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_parent_model = 'ApiResourceComponentThing';
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_data = array(
			'ApiResourceComponentThing' => array(
				0 => array('key' => 'value'),
				1 => array('key' => 'value')
			)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4008));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - With Mismatched Foreign Key and Primary Key
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneWithMistmatchedForeignKeyAndPrimaryKey(){
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_parent_model = 'ApiResourceComponentThing';
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_id = 1;
		$this->ApiResource->_data = array(
			'ApiResourceComponentThing' => array(
				'thing_id' => 2
			)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with($this->equalTo(4002));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - With Successful Save One
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneWithSuccessfulSaveOne() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_parent_model = 'ApiResourceComponentThing';
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_id = 1;
		$this->ApiResource->_data = array(
			'ApiResourceComponentThing' => array(
				'key' => 'value'
			)
		);
		
		$this->ApiResource->Controller->ApiResourceComponentThing->ApiResourceComponentThing
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiResourceComponentThing.id'),
					'conditions' => array(
						'ApiResourceComponentThing.thing_id' => 1,
						'ApiResourceComponentThing.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(false));
		
		$this->ApiResource
			->expects($this->at(0))
			->method('withData')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(1))
			->method('withParentModel')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(2))
			->method('forModel')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(3))
			->method('withId')
			->with()
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(4))
			->method('withParentModel')
			->with('ApiResourceComponentThing')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(5))
			->method('forModel')
			->with('ApiResourceComponentThing')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(6))
			->method('withId')
			->with(null)
			->will($this->returnValue($this->ApiResource));
			
		$this->ApiResource
			->expects($this->at(7))
			->method('withFieldExceptions')
			->with(array('thing_id'))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->at(8))
			->method('withData')
			->with($this->equalTo(array(
				'ApiResourceComponentThing' => array(
					'key' => 'value',
					'thing_id' => 1
				)
			)))
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('saveOne')
			->will($this->returnValue(true));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertTrue($result);
		
	}
	
	/**
	 * Test Save Associated Has One - With Unsuccessful Save One
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasOneWithUnsuccessfulSaveOne() {
		
		$this->__setUpMocksForSaveAssociatedHasOneTests();
		
		$this->ApiResource->_parent_model = 'ApiResourceComponentThing';
		$this->ApiResource->_model = 'ApiResourceComponentThing';
		$this->ApiResource->_id = 1;
		$this->ApiResource->_data = array(
			'ApiResourceComponentThing' => array(
				'key' => 'value'
			)
		);
		
		$this->ApiResource
			->expects($this->any())
			->method('withParentModel')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->any())
			->method('forModel')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->any())
			->method('withId')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->any())
			->method('withFieldExceptions')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->any())
			->method('withData')
			->will($this->returnValue($this->ApiResource));
		
		$this->ApiResource
			->expects($this->once())
			->method('saveOne')
			->will($this->returnValue(false));
		
		$result = $this->ApiResource->saveAssociatedHasOne();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Save Associated Has One - Empty Model
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasManyEmptyModel() {
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with(5001)
			->will($this->returnValue(null));
			
		$this->assertFalse($this->ApiResource->saveAssociatedHasMany());
		
	}
	
	/**
	 * Test Save Associated Has One - Empty Data
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasManyEmptyData() {
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with(4005)
			->will($this->returnValue(null));
		
		$this->ApiResource->forModel('Test');
			
		$this->assertFalse($this->ApiResource->saveAssociatedHasMany());
		
	}
	
	/**
	 * Test Save Associated Has One - Empty Model Data
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveAssociatedHasManyEmptyModelData() {
		
		$data = array('OtherModel' => array('field' => 'value'));
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with(4005)
			->will($this->returnValue(null));
		
		$this->ApiResource->forModel('Test');
		
		$this->ApiResource->withData($data);
		
		$this->assertFalse($this->ApiResource->saveAssociatedHasMany());
		
	}
	
	/**
	 * Test Save One - Empty Model
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSaveOneEmptyModel() {
		
		$this->ApiResource = $this->getMock(
			'ApiResourceComponent',
			array('setResponseCode'),
			array($this->ComponentCollection)
		);
		
		$this->ApiResource
			->expects($this->once())
			->method('setResponseCode')
			->with(5001)
			->will($this->returnValue(null));
			
		$this->assertFalse($this->ApiResource->saveOne());
		
	}
	
	/**
	 * Test Returns Filtered Data Fields
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testReturnsFilteredDataFields() {
		
		$this->ApiResource->withFieldMap(array(
			'field1' => 'att1',
			'field2' => 'att2',
			'field3.sub1' => 'att3.sub1',
			'field3.sub2' => 'att3.sub2'
		));
		
		$this->ApiResource->withFieldExceptions(array(
			'field_exception_one'
		));
		
		$this->ApiResource->withDataFields(array(
			'field1' => 'value 1',
			'field2' => 'value 2',
			'field3' => array(
				'sub1' => 'value 3 sub 1'
			),
			'field4' => 'value 4',
			'field_exception_one' => 'field exception one value',
			'field_exception_two' => 'field_exception_two_value'
		));
		
		$result = $this->ApiResource->returnsFilteredDataFields();
		
		$expected = array(
			'field1' => 'value 1',
			'field2' => 'value 2',
			'field3' => array(
				'sub1' => 'value 3 sub 1'
			),
			'field_exception_one' => 'field exception one value'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Set Paginator Options - Invalid Model Object
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsInvalidModelObject() {
		
		$options = array(
			'test' => 'value'
		);
		
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'test' => 'value'
		);
		
		$modelObject = null;
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
	/**
	 * Test Set Paginator Options - Given Response Type Is CSV
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsGivenResponseTypeIsCsv() {
		
		$modelObject = $this->ApiResource->Controller->ApiResourceComponentThing;
		
		$options = array();
		
		$this->ApiResource->ApiRequestHandler = $this->getMock(
			'ApiRequestHandler',
			array('responseType')
		);
		
		$this->ApiResource->ApiRequestHandler
			->expects($this->once())
			->method('responseType')
			->with()
			->will($this->returnValue('csv'));
			
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'limit' => PHP_INT_MAX,
			'maxLimit' => PHP_INT_MAX
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
	/**
	 * Test Set Paginator Options - Set Limit From Has Many Association Setup
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsSetLimitFromHasManyAssociationSetup() {
	
		$modelObject = $this->ApiResource->Controller->ApiResourceComponentThing;
		
		$modelObject->limit = 1000;

		$limit = 9999;
		
		$this->ApiResource->Controller->ApiResourceComponentStuff = $this->getMock('ApiResourceComponentStuff');
		
		$this->ApiResource->Controller->ApiResourceComponentStuff->hasMany = array(
			'ApiResourceComponentThing' => array(
				'className' => 'ApiResourceComponentStuff',
				'foreignKey' => 'stuff_id',
				'require' => true,
				'limit' => $limit
			)
		);
		
		$options = array(
			'parentModel' => 'ApiResourceComponentStuff'
		);
		
		$this->ApiResource->ApiRequestHandler = $this->getMock(
			'ApiRequestHandler',
			array('responseType')
		);
		
		$this->ApiResource->ApiRequestHandler
			->expects($this->once())
			->method('responseType')
			->with()
			->will($this->returnValue('json'));
			
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'limit' => $limit,
			'parentModel' => 'ApiResourceComponentStuff'
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
	/**
	 * Test Set Paginator Options - Model Limit Is Set
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsGivenModelLimitIsSet() {
		
		$modelObject = $this->ApiResource->Controller->ApiResourceComponentThing;
		
		$modelObject->limit = 1000;
		
		$modelObject->limit_as_related = 9999;
		
		$options = array();
		
		$this->ApiResource->ApiRequestHandler = $this->getMock(
			'ApiRequestHandler',
			array('responseType')
		);
		
		$this->ApiResource->ApiRequestHandler
			->expects($this->once())
			->method('responseType')
			->with()
			->will($this->returnValue('json'));
			
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'limit' => $modelObject->limit
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
	/**
	 * Test Set Paginator Options - Given Model Limit Is Set To All
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsGivenModelLimitIsSetToAll() {
		
		$modelObject = $this->ApiResource->Controller->ApiResourceComponentThing;
		
		$modelObject->limit = 'all';
		
		$modelObject->limit_as_related = 9999;
		
		$options = array();
		
		$this->ApiResource->ApiRequestHandler = $this->getMock(
			'ApiRequestHandler',
			array('responseType')
		);
		
		$this->ApiResource->ApiRequestHandler
			->expects($this->once())
			->method('responseType')
			->with()
			->will($this->returnValue('json'));
			
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'limit' => PHP_INT_MAX,
			'maxLimit' => PHP_INT_MAX
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
	/**
	 * Test Set Paginator Options - Given Model Max Limit Is Set
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testSetPaginatorOptionsGivenModelMaxLimitIsSet() {
		
		$modelObject = $this->ApiResource->Controller->ApiResourceComponentThing;
		
		$modelObject->limit = 1000;
		
		$modelObject->limit_as_related = 9999;
		
		$modelObject->maxLimit = 5000;
		
		$options = array();
		
		$this->ApiResource->ApiRequestHandler = $this->getMock(
			'ApiRequestHandler',
			array('responseType')
		);
		
		$this->ApiResource->ApiRequestHandler
			->expects($this->once())
			->method('responseType')
			->with()
			->will($this->returnValue('json'));
			
		$expected = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'limit' => $modelObject->limit,
			'maxLimit' => $modelObject->maxLimit
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiResource->setPaginatorOptions($modelObject, $options)
		);
		
	}
	
}
