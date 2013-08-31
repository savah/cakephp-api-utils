<?php
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');
App::uses('AppModel', 'Model');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('ApiInputDataComponent', 'Api.Controller' . DS . 'Component');
App::uses('ApiQueryComponent', 'Api.Controller' . DS . 'Component');
App::uses('CakeTime', 'Utility');

/**
 * ApiInputDataComponentThing Model
 *
 */
if (!class_exists('ApiInputDataComponentThing')) {
	class ApiInputDataComponentThing extends AppModel {}
}

/**
 * ApiInputDataComponentThings Controller
 *
 */
if (!class_exists('ApiInputDataComponentThingsController')) {
	class ApiInputDataComponentThingsController extends AppController {}
}

/**
 * ApiInputDataComponentStuff Model
 *
 */
if (!class_exists('ApiInputDataComponentStuff')) {
	class ApiInputDataComponentStuff extends AppModel {}
}

/**
 * Api Input Data Component Double
 *
 */
if (!class_exists('ApiInputDataComponentDouble')) {
	class ApiInputDataComponentDouble extends ApiInputDataComponent {
		public $_save_all_keys = array();
		public $_attributes = array();
		public $_timezone = array();
		public $_model = null;
		public $_ModelObject = null;
		public $_related = array();
	}
}

/**
 * Api Input Data Component Test
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
class ApiInputDataComponentTest extends CakeTestCase {

	/**
	 * Test Controller Name
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @var     string
	 */
	protected $test_controller = 'TestController';
	
	/**
	 * Test Model Name
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @var     string
	 */
	protected $test_model = 'ApiInputDataComponentThing';
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function setUp() {

		parent::setUp();
		
		$this->Model = $this->getMock($this->test_model);
		
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
			
		$this->InputData = new ApiInputDataComponentDouble($this->ComponentCollection);
		
		$this->InputData->modelClass = $this->test_model;
		
		$this->InputData->{$this->test_model} = $this->Model;
		
		$this->InputData->Query = $this->getMock(
			'ApiQueryComponent',
			array('getFromRoute'),
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
	 * Test Instance Setup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testInstanceSetup() {
		
		$components = Hash::normalize($this->InputData->components);
		
		$this->assertArrayHasKey('Query', $components);
		
	}
	
	/**
	 * Test For Model
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testForModel() {
		
		$this->InputData->forModel();
		
		$this->assertNull($this->InputData->_model);
		
		$results = $this->InputData->forModel($this->test_model);
		
		$this->assertEquals($this->test_model, $this->InputData->_model);
		
		$this->assertInstanceOf('ApiInputDataComponent', $results);
		
	}
	
	/**
	 * Test Is Save All - With Single Record Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIsSaveAllWithSingleRecordData() {
		
		$data = array(
			'key' => 'value'
		);
		
		$result = $this->InputData->isSaveAll($data);
		
		$this->assertFalse($result);
		
		$this->InputData->Controller->request->data = $data;
		
		$result = $this->InputData->isSaveAll();
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Is Save All - With Multi-Record Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIsSaveAllWithMultiRecordData() {
		
		$data = array(
			array(
				'key' => 'value'
			),
			array(
				'key' => 'value'
			)
		);
		
		$result = $this->InputData->isSaveAll($data);
		
		$this->assertTrue($result);
		
		$this->InputData->Controller->request->data = $data;
		
		$result = $this->InputData->isSaveAll();
		
		$this->assertTrue($result);
		
	}

	/**
	 * Test Fix Post And Put Underscores - Empty Data
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFixPostAndPutUnderscoresEmptyData() {
		
		$data = null;
		
		$this->assertNull($this->InputData->__fixPostAndPutUnderscoresRecursive($data));
	}
	
	/**
	 * Test Fix Post And Put Underscores - Recursive
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFixPostAndPutUnderscoresRecursive() {
		
		$data = array(
			'settings_stuff' => 'value',
			'nested' => array(
				'settings_more_stuff' => 'value',
				'other_key' => 'value'
			),
			'other_key' => 'value',
			'fileAttr' => array(
				'name' => 'some_name.png',
				'type' => 'image/png',
				'tmp_name' => 'some_tmp_name',
				'error' => 0
			)
		);
		
		$result = $this->InputData->__fixPostAndPutUnderscoresRecursive($data);
		
		$expected = array(
			'settings.stuff' => 'value',
			'nested' => array(
				'settings.more.stuff' => 'value',
				'other.key' => 'value'
			),
			'other.key' => 'value',
			'fileAttr' => array(
				'name' => 'some_name.png',
				'type' => 'image/png',
				'tmp_name' => 'some_tmp_name',
				'error' => 0
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Fix Post And Put Underscores - With No Request Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFixPostAndPutUnderscoresWithNoRequestData() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponent', 
			array('__fixPostAndPutUnderscoresRecursive'), 
			array($this->ComponentCollection)
		);
		
		$this->InputData
			->expects($this->never())
			->method('__fixPostAndPutUnderscoresRecursive');
		
		$this->InputData->Controller->request->data = array();
		
		$return = $this->InputData->fixPostAndPutUnderscores();
		
		$this->assertEquals(array(), $this->InputData->Controller->request->data);
		
		$this->assertInstanceOf('ApiInputDataComponent', $return);
		
	}
	
	/**
	 * Test Fix Post And Put Underscores - With Request Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testFixPostAndPutUnderscoresWithRequestData() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponent', 
			array('__fixPostAndPutUnderscoresRecursive'), 
			array($this->ComponentCollection)
		);
		
		$this->InputData
			->expects($this->once())
			->method('__fixPostAndPutUnderscoresRecursive')
			->with($this->equalTo(array('settings_key' => 'value')))
			->will($this->returnValue(array('settings.key' => 'value')));
		
		$this->InputData->Controller->request->data = array('settings_key' => 'value');
		
		$return = $this->InputData->fixPostAndPutUnderscores();
		
		$this->assertEquals(
			array('settings.key' => 'value'),
			$this->InputData->Controller->request->data
		);
		
		$this->assertInstanceOf('ApiInputDataComponent', $return);
		
	}
	
	/**
	 * Test Normalize - With Empty Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalizeEmpty() {
		
		$this->InputData->forModel('Model')->normalize();
		$test = $this->InputData->Controller->request->data;
		$this->assertEmpty($test);
		
	}
	
	/**
	 * Test Normalize - With Empty Model
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalizeModelEmpty() {
		
		$original = array(
			'id' => 1,
			'name' => 'Name'
		);
		$this->InputData->Controller->request->data = $original;
		
		$this->InputData->normalize();
		
		$expected = $this->InputData->Controller->request->data;
		
		$this->assertEqual($original, $expected);
		
	}
	
	/**
	 * Test Normalize - With Model Present
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalizeModelPresent() {
		
		$data = array(
			'ApiInputDataComponentThing' => array(
				'id' => 1,
				'name' => 'Name'
			)
		);
		
		$this->InputData->Controller->request->data = $data;
		
		$this->InputData->forModel('ApiInputDataComponentThing');
		
		$this->InputData->normalize();
		
		$test = $this->InputData->Controller->request->data;
		
		$expected = array('ApiInputDataComponentThing' => $data);
		
		$this->assertEqual($expected, $test);
		
	}
	
	/**
	 * Test Normalize
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalize() {
		
		$data = array(
			'id' => 1,
			'name' => 'Name',
			'tags' => array('one', 'two', 'three')
		);
		
		$this->InputData->Controller->request->data = $data;
		
		$this->InputData->forModel('ApiInputDataComponentThing');
		
		$this->InputData->normalize();
		
		$test = $this->InputData->Controller->request->data;
		
		$this->assertEqual($test, array('ApiInputDataComponentThing' => $data));
		
	}
	
	/**
	 * Test Normalize With Related Data
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalizeRelated() {
		
		$data = array(
			'id' => 1,
			'name' => 'Name',
			'ApiInputDataComponentStuff' => array(
				'fname' => 'First',
				'lname' => 'Last'
			),
			'tags' => array('one', 'two', 'three')
		);
		
		$this->InputData->Controller->request->data = $data;
		
		$this->InputData->forModel('ApiInputDataComponentThing');
		
		$this->InputData->normalize();
		
		$test = $this->InputData->Controller->request->data;
		
		$expected = array(
			'ApiInputDataComponentThing' => $data
		);
		
		$this->assertEqual($expected, $test);
		
	}
	
	/**
	 * Test Normalize Save All
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testNormalizeSaveAll() {
		
		$data = array(
			array(
				'id' => 1,
				'name' => 'Name',
				'ApiInputDataComponentStuff' => array(
					'fname' => 'First',
					'lname' => 'Last'
				),
				'tags' => array('one', 'two', 'three')
			),
			array(
				'name' => 'Name',
				'ApiInputDataComponentStuff' => array(
					'fname' => 'First',
					'lname' => 'Last'
				),
				'tags' => array('one', 'two', 'three')
			),
		);
		
		$this->InputData->Controller->request->data = $data;
		
		$this->InputData->forModel('ApiInputDataComponentThing');
		
		$this->InputData->normalize();
		
		$test = $this->InputData->Controller->request->data;
		
		$expected = array();
		
		foreach ($data as $row) {
			$expected[] = array('ApiInputDataComponentThing' => $row);
		}
		
		$this->assertEqual($expected, $test);
		
	}
	
	/**
	 * Test Prepare - Empty Model
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testPrepareEmptyModel() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'forModel'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData
			->expects($this->never())
			->method('forModel');
		
		$this->InputData->Controller->request->data = array('__testAttribute' => 'value');
			
		$this->assertEquals(
			$this->InputData->Controller->request->data,
			$this->InputData->prepare()
		);
		
	}
	
	/**
	 * Test Prepare
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testPrepare() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'forModel',
				'fixPostAndPutUnderscores',
				'normalize',
				'convertAllToSaveAll',
				'prepareNormalizedDataLoops',
				'integrateRouteParent',
				'convertAttributesToFields',
				'denormalizedFields',
				'convertSinglesBackToNormal'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData
			->expects($this->at(0))
			->method('forModel')
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(1))
			->method('fixPostAndPutUnderscores')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(2))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(3))
			->method('normalize')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(4))
			->method('convertAllToSaveAll')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(5))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(6))
			->method('prepareNormalizedDataLoops')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(7))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(8))
			->method('integrateRouteParent')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(9))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(10))
			->method('convertAttributesToFields')
			->with()
			->will($this->returnValue($this->InputData));
			
		$this->InputData
			->expects($this->at(11))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
			
		$this->InputData
			->expects($this->at(12))
			->method('denormalizedFields')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(13))
			->method('convertSinglesBackToNormal')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->Controller->request->data = array('key' => 'value');
		
		$result = $this->InputData->prepare();
		
		$expected = array('key' => 'value');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Convert Iso8601 To Sql Datetime - Results
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testConvertIso8601ToSqlDatetime() {
		
		$data = array(
			'id' => 1,
			'name' => 'Name',
			'tags' => array('one', 'two', 'three'),
			'lastView.tricky' => '2013-03-05T13:56:02+0900',
			'created' => '2013-03-01T13:00:00+0000'
		);
		
		$this->InputData->_timezone = 'UTC';
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThing');
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'text'
			),
			'tag' => array(
				'type' => 'text'
			),
			'lastView.tricky' => array(
				'field' => 'last_view.tricky',
				'type' => 'datetime'
			),
			'created' => array(
				'type' => 'datetime'
			)
		);
		
		$result = $this->InputData->convertIso8601ToSqlDatetime($data);
		
		$expected = $data;
		
		$expected['lastView.tricky'] = CakeTime::toServer($expected['lastView.tricky'], 'UTC');
		
		$expected['created'] = CakeTime::toServer($expected['created'], 'UTC');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert Boolean Literals to `0` or `1`
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertBooleanLiterals() {
		
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThing');
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'text'
			),
			'is_disabled' => array(
				'type' => 'boolean'
			),
			'settings.required' => array(
				'type' => 'boolean'
			),
			'settings.email' => array(
				'field' => 'metadata.email',
				'type' => 'boolean'
			),
			'settings.phone' => array(
				'field' => 'metadata.phone',
				'type' => 'boolean'
			),
			'settings.not_boolean' => array(
				'field' => 'metadata.not_boolean',
				'type' => 'string'
			),
			'settings.not_boolean2' => array(
				'field' => 'metadata.not_boolean2',
				'type' => 'string'
			),
			'created' => array(
				'type' => 'datetime'
			)
		);
		
		$data = array(
			'id' => 1,
			'name' => 'Name',
			'is_disabled' => true,
			'settings.required' => 'true',
			'settings.email' => false,
			'settings.phone' => 'false',
			'settings.not_boolean' => 'true',
			'settings.not_boolean2' => 'false',
			'created' => '2013-03-01T13:00:00+0000'
		);
		
		$result = $this->InputData->convertBooleanLiterals($data);
		
		$expected = $data;
		$expected['is_disabled'] = 1;
		$expected['settings.required'] = 1;
		$expected['settings.email'] = 0;
		$expected['settings.phone'] = 0;
		$expected['settings.not_boolean'] = 'true';
		$expected['settings.not_boolean2'] = 'false';
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert String Null to Null Datatype
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testConvertStringNulls() {
		
		$data = array(
			'id' => 1,
			'name' => 'Name',
			'tags' => 'null',
			'lastView.tricky' => 'null',
			'created' => '2013-03-01T13:00:00+0000'
		);
		
		$result = $this->InputData->convertStringNulls($data);
		
		$expected = $data;
		$expected['tags'] = null;
		$expected['lastView.tricky'] = null;
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Set PolyMorphic Related Model - Existing Model (Should Override)
     *
     * @author	Paul Smith <paul@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testSetPolymorphicRelatedModelExisting() {
		
		$data = array(
			'id' => 1,
			'someField' => 'someValue',
			'resource' => 'Something'
		);
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble');
		
		$this->InputData->_model = 'ApiInputDataComponentStuff';
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'resource' => array(
				'type' => 'string',
				'field' => 'model',
				'polymorphic_model' => true
			),
			'someField' => array(
				'type' => 'string',
				'field' => 'some_field'
			)
		);
		
		$expected = $data;
		$expected['resource'] = 'ApiInputDataComponentStuff'; // overridden
		
		$result = $this->InputData->setPolymorphicRelatedModel($data, $this->InputData->_model);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Set PolyMorphic Related Model
     *
     * @author	Paul Smith <paul@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testSetPolymorphicRelatedModel() {
		
		$data = array(
			'id' => 1,
			'someField' => 'someValue'
		);
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble');
		
		$this->InputData->_model = 'ApiInputDataComponentStuff';
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'resource' => array(
				'type' => 'string',
				'field' => 'model',
				'polymorphic_model' => true
			),
			'someField' => array(
				'type' => 'string',
				'field' => 'some_field'
			)
		);
		
		$expected = $data;
		$expected['resource'] = 'ApiInputDataComponentStuff';
		
		$result = $this->InputData->setPolymorphicRelatedModel($data, $this->InputData->_model);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Set PolyMorphic Related Model - With Excluded Model
     *
     * @author	Paul Smith <paul@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testSetPolymorphicRelatedModelWithExcludedModel() {
		
		$data = array(
			'id' => 1,
			'someField' => 'someValue'
		);
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble');
		
		$this->InputData->_model = 'ApiInputDataComponentStuff';
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'resource' => array(
				'type' => 'string',
				'field' => 'model',
				'polymorphic_model' => true,
				'polymorphic_exclusions' => array(
					'ApiInputDataComponentStuff'
				)
			),
			'someField' => array(
				'type' => 'string',
				'field' => 'some_field'
			)
		);
		
		$expected = $data;
		
		$result = $this->InputData->setPolymorphicRelatedModel($data, $this->InputData->_model);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent - Empty Model
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentEmptyModel() {
		
		$this->InputData->Query = $this->getMock(
			'ApiQueryComponent',
			array('getParent'),
			array($this->ComponentCollection)
		);
		
		$this->InputData->Query
			->expects($this->never())
			->method('getParent');

		$this->assertEquals(
			$this->InputData,
			$this->InputData->integrateRouteParent()
		);
		
	}
	
	/**
	 * Test Integrate Route Parent Has Many Into Primary Belongs To
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentHasManyIntoPrimaryBelongsTo() {
		
		$result = $this->InputData->integrateRouteParentHasManyIntoPrimaryBelongsTo(
			array(
				'ApiInputDataComponentStuff' => array(
					0 => array(
						'name' => 'test'
					)
				)
			),
			array(
				'primaryModelName' => 'ApiInputDataComponentStuff',
				'foreign_key_attribute' => 'thingId',
				'parent_model_id' => 999
			)
		);
		
		$expected = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test',
					'thingId' => 999
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent Has One Into Primary Belongs To
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentHasOneIntoPrimaryBelongsTo() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'integrateRouteParentHasManyIntoPrimaryBelongsTo'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData->ApiInputDataComponentStuff = $this->getMock('ApiInputDataComponentStuff', array('find'));
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			)
		);
		
		$options = array(
			'primaryModelName' => 'ApiInputDataComponentStuff',
			'foreign_key_attribute' => 'thingId',
			'foreign_key_field' => 'thing_id',
			'foreign_conditions' => array('ApiInputDataComponentStuff.name' => 'ABC'),
			'parent_model_id' => 999,
			'primary_model_primary_key_name' => 'id'
		);
		
		$this->InputData
			->expects($this->once())
			->method('integrateRouteParentHasManyIntoPrimaryBelongsTo')
			->with($this->equalTo($data), $this->equalTo($options))
			->will($this->returnValue(array(
				'ApiInputDataComponentStuff' => array(
					0 => array(
						'name' => 'test',
						'thingId' => 999
					)
				)
			)));
		
		$this->InputData->ApiInputDataComponentStuff
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ApiInputDataComponentStuff.id'),
					'conditions' => array(
						'ApiInputDataComponentStuff.thing_id' => 999,
						'ApiInputDataComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array(
				'ApiInputDataComponentStuff' => array(
					'id' => 998
				)
			)));
		
		$result = $this->InputData->integrateRouteParentHasOneIntoPrimaryBelongsTo($data, $options);
		
		$expected = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'id' => 998,
					'thingId' => 999,
					'name' => 'test'
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent Belongs To Into Primary Has Many - With Existing Primary
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentBelongsToIntoPrimaryHasManyWithExistingPrimary() {
		
		$this->InputData->ApiInputDataComponentStuff = $this->getMock('ApiInputDataComponentStuff');
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias = $this->getMock('ApiInputDataComponentStuff', array('find'));
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					array('not' => 'legal')
				)
			)
		);
		
		$options = array(
			'primaryModelName' => 'ApiInputDataComponentStuff',
			'primary_model_primary_key_name' => 'id',
			'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
			'parent_model_primary_key_name' => 'id',
			'parent_model_id' => 999,
			'foreign_key_field' => 'stuff_id',
			'foreign_key_attribute' => 'stuffId',
			'foreign_conditions' => array('ParentApiInputDataComponentStuffAlias.name' => 'ABC')
		);
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ParentApiInputDataComponentStuffAlias.stuff_id'),
					'conditions' => array(
						'ParentApiInputDataComponentStuffAlias.id' => 999,
						'ParentApiInputDataComponentStuffAlias.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(array(
				'ParentApiInputDataComponentStuffAlias' => array(
					'stuff_id' => 998
				)
			)));
		
		$result = $this->InputData->integrateRouteParentBelongsToIntoPrimaryHasMany($data, $options);
		
		$expected = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'id' => 998,
					'name' => 'test'
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent Belongs To Into Primary Has Many - With New Primary
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentBelongsToIntoPrimaryHasManyWithNewPrimary() {
		
		$this->InputData->ApiInputDataComponentStuff = $this->getMock('ApiInputDataComponentStuff');
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias = $this->getMock('ApiInputDataComponentStuff', array('find'));
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					array('not' => 'legal')
				)
			)
		);
		
		$options = array(
			'primaryModelName' => 'ApiInputDataComponentStuff',
			'primary_model_primary_key_name' => 'id',
			'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
			'parent_model_primary_key_name' => 'id',
			'parent_model_id' => 999,
			'foreign_key_field' => 'stuff_id',
			'foreign_key_attribute' => 'stuffId',
			'foreign_conditions' => array('ParentApiInputDataComponentStuffAlias.name' => 'ABC')
		);
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('find')
			->with(
				$this->equalTo('first'),
				$this->equalTo(array(
					'fields' => array('ParentApiInputDataComponentStuffAlias.stuff_id'),
					'conditions' => array(
						'ParentApiInputDataComponentStuffAlias.id' => 999,
						'ParentApiInputDataComponentStuffAlias.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue(false));
		
		$result = $this->InputData->integrateRouteParentBelongsToIntoPrimaryHasMany($data, $options);
		
		$expected = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					'id' => 999
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent Belongs To Into Primary Has One
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentBelongsToIntoPrimaryHasOne() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'integrateRouteParentBelongsToIntoPrimaryHasMany'
			), 
			array($this->ComponentCollection)
		);
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					array('not' => 'legal')
				)
			)
		);
		
		$options = array(
			'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias'
		);
		
		$this->InputData
			->expects($this->once())
			->method('integrateRouteParentBelongsToIntoPrimaryHasMany')
			->with(
				$this->equalTo($data),
				$this->equalTo($options)
			)
			->will($this->returnValue(array(
				'ApiInputDataComponentStuff' => array(
					0 => array(
						'name' => 'test'
					)
				),
				'ParentApiInputDataComponentStuffAlias' => array(
					0 => array(
						'id' => 999
					)
				)
			)));
		
		$result = $this->InputData->integrateRouteParentBelongsToIntoPrimaryHasOne($data, $options);
		
		$expected = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					'id' => 999
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent - With Primary Has One
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentWithPrimaryHasOne() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'integrateRouteParentBelongsToIntoPrimaryHasOne'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData->ApiInputDataComponentStuff = $this->getMock('ApiInputDataComponentStuff', array(
			'getAssociated'
		));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias = $this->getMock('ApiInputDataComponentStuff', array(
			'getAssociated',
			'getFieldMap',
			'attributes'
		));
		
		$this->InputData->Query = $this->getMock(
			'ApiQueryComponent', 
			array(
				'getParent'
			), 
			array($this->ComponentCollection)
		);
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					array('not' => 'legal')
				)
			)
		);
		
		$this->InputData->Controller->request->data = array($data);
		
		$this->InputData->Controller->request->params = array(
			'modelAlias' => 'ChildApiInputDataComponentStuff'
		);
		
		$this->InputData->Query
			->expects($this->once())
			->method('getParent')
			->with()
			->will($this->returnValue(array(
				'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
				'parent_model_id' => 999
			)));
	
		$this->InputData->ApiInputDataComponentStuff->primaryKey = 'id';
		
		$this->InputData->ApiInputDataComponentStuff
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ParentApiInputDataComponentStuffAlias' => 'hasOne'
			)));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias->primaryKey = 'id';
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias->belongsTo['ChildApiInputDataComponentStuff'] = array(
			'foreignKey' => 'stuff_id',
			'conditions' => array(
				'ChildApiInputDataComponentStuff.name' => 'ABC'
			)
		);
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ChildApiInputDataComponentStuff' => 'belongsTo'
			)));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array(
				'stuffId' => array('field' => 'stuff_id')
			)));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array(
				'stuffId' => array('field' => 'stuff_id')
			)))
			->will($this->returnValue(array(
				'stuff_id' => 'stuffId'
			)));
		
		$return = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					'id' => 999
				)
			)
		);
		
		$this->InputData
			->expects($this->once())
			->method('integrateRouteParentBelongsToIntoPrimaryHasOne')
			->with(
				$this->equalTo($data),
				$this->equalTo(array(
					'primaryModelName' => 'ApiInputDataComponentStuff',
					'primary_model_primary_key_name' => 'id',
					'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
					'parentModelName' => $this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias->alias,
					'parent_model_primary_key_name' => 'id',
					'parent_model_id' => 999,
					'foreign_key_field' => 'stuff_id',
					'foreign_key_attribute' => 'stuffId',
					'foreign_conditions' => array(
						'ChildApiInputDataComponentStuff.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue($return));
		
		$result = $this->InputData->forModel('ApiInputDataComponentStuff')->integrateRouteParent();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data; 
		
		$expected = array($return);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Integrate Route Parent - With Primary Belongs To
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIntegrateRouteParentWithPrimaryBelongsTo() {
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'integrateRouteParentHasOneIntoPrimaryBelongsTo'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData->ApiInputDataComponentStuff = $this->getMock('ApiInputDataComponentStuff', array(
			'getAssociated',
			'getFieldMap',
			'attributes'
		));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias = $this->getMock('ApiInputDataComponentStuff', array(
			'getAssociated'
		));
		
		$this->InputData->Query = $this->getMock(
			'ApiQueryComponent', 
			array(
				'getParent'
			), 
			array($this->ComponentCollection)
		);
		
		$data = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'name' => 'test'
				)
			),
			'ParentApiInputDataComponentStuffAlias' => array(
				0 => array(
					array('not' => 'legal')
				)
			)
		);
		
		$this->InputData->Controller->request->data = array($data);
		
		$this->InputData->Controller->request->params = array(
			'modelAlias' => 'ChildApiInputDataComponentStuff'
		);
		
		$this->InputData->Query
			->expects($this->once())
			->method('getParent')
			->with()
			->will($this->returnValue(array(
				'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
				'parent_model_id' => 999
			)));
	
		$this->InputData->ApiInputDataComponentStuff->primaryKey = 'id';
		
		$this->InputData->ApiInputDataComponentStuff->belongsTo['ParentApiInputDataComponentStuffAlias'] = array(
			'foreignKey' => 'parent_stuff_id',
			'conditions' => array(
				'ParentApiInputDataComponentStuffAlias.name' => 'ABC'
			)
		);
		
		$this->InputData->ApiInputDataComponentStuff
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ParentApiInputDataComponentStuffAlias' => 'belongsTo'
			)));
		
		$this->InputData->ApiInputDataComponentStuff
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue(array(
				'parentApiInputDataComponentStuffId' => array('field' => 'parent_stuff_id')
			)));
		
		$this->InputData->ApiInputDataComponentStuff
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo(array(
				'parentApiInputDataComponentStuffId' => array('field' => 'parent_stuff_id')
			)))
			->will($this->returnValue(array(
				'parent_stuff_id' => 'parentApiInputDataComponentStuffId'
			)));
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias->primaryKey = 'id';
		
		$this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias
			->expects($this->once())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'ChildApiInputDataComponentStuff' => 'hasOne'
			)));
		
		$return = array(
			'ApiInputDataComponentStuff' => array(
				0 => array(
					'id' => 998,
					'parentApiInputDataComponentStuffId' => 999,
					'name' => 'test'
				)
			)
		);
		
		$this->InputData
			->expects($this->once())
			->method('integrateRouteParentHasOneIntoPrimaryBelongsTo')
			->with(
				$this->equalTo($data),
				$this->equalTo(array(
					'primaryModelName' => 'ApiInputDataComponentStuff',
					'primary_model_primary_key_name' => 'id',
					'parentModelAlias' => 'ParentApiInputDataComponentStuffAlias',
					'parentModelName' => $this->InputData->ApiInputDataComponentStuff->ParentApiInputDataComponentStuffAlias->alias,
					'parent_model_primary_key_name' => 'id',
					'parent_model_id' => 999,
					'foreign_key_field' => 'parent_stuff_id',
					'foreign_key_attribute' => 'parentApiInputDataComponentStuffId',
					'foreign_conditions' => array(
						'ParentApiInputDataComponentStuffAlias.name' => 'ABC'
					)
				))
			)
			->will($this->returnValue($return));
		
		$result = $this->InputData->forModel('ApiInputDataComponentStuff')->integrateRouteParent();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array($return);
		
		$this->assertEquals($expected, $result);
		
	}

	/**
     * Test Convert Attribute Options - Not In Original Attributes Array
     *
     * @author	Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testConvertAttributeOptionsNotInOriginalAttributesArray() {
		
		$data = array(
			'id' => 1,
			'normalOptions' => 'one',
			'specialOptions' => 'special-two',
			'name' => 'Name'
		);
		
		$this->InputData->_timezone = 'UTC';
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble', array(
			'getOptions'
		));
		
		$this->InputData->_ModelObject
			->expects($this->any())
			->method('getOptions')
			->will($this->returnCallback(function($options_key){
				switch ($options_key) {
					case 'normal_options':
						$return = array(1 => 'one', 2 => 'two');
						break;
					case 'special_options':
						$return = array(1 => 'special-one', 2 => 'special-two');
						break;
					default:
						$return = array();
						break;
				}
				return $return;
			}));
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'specialOptions' => array(
				'type' => 'string',
				'field' => 'some_completely_different_field_name',
				'values' => array(
					'options' => 'special_options'
				)
			),
			'name' => array(
				'type' => 'text'
			)
		);
		
		$result = $this->InputData->convertAttributeOptions($data);

		$expected = $data;
		
		$expected['normalOptions'] = 'one';
		$expected['specialOptions'] = 2;
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Convert Attribute Options - Attribute Value Is An Array
     *
     * @author	Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testConvertAttributeOptionsAttributeValueIsAnArray() {
		
		$data = array(
			'id' => 1,
			'normalOptions' => array('one'),
			'specialOptions' => 'special-two',
			'name' => 'Name'
		);
		
		$this->InputData->_timezone = 'UTC';
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble', array(
			'getOptions'
		));
		
		$this->InputData->_ModelObject
			->expects($this->any())
			->method('getOptions')
			->will($this->returnCallback(function($options_key){
				switch ($options_key) {
					case 'normal_options':
						$return = array(1 => 'one', 2 => 'two');
						break;
					case 'special_options':
						$return = array(1 => 'special-one', 2 => 'special-two');
						break;
					default:
						$return = array();
						break;
				}
				return $return;
			}));
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'normalOptions' => array(
				'type' => 'string',
				'field' => 'normal_options'
			),
			'specialOptions' => array(
				'type' => 'string',
				'field' => 'some_completely_different_field_name',
				'values' => array(
					'options' => 'special_options'
				)
			),
			'name' => array(
				'type' => 'text'
			)
		);
		
		$result = $this->InputData->convertAttributeOptions($data);
		
		$expected = $data;
		
		$expected['normalOptions'] = array('one');
		$expected['specialOptions'] = 2;
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Convert Attribute Options - Results
     *
     * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testConvertAttributeOptions() {
		
		$data = array(
			'id' => 1,
			'normalOptions' => 'one',
			'specialOptions' => 'special-two',
			'name' => 'Name'
		);
		
		$this->InputData->_timezone = 'UTC';
			
		$this->InputData->_ModelObject = $this->getMock('ApiInputDataComponentThingDouble', array(
			'getOptions'
		));
		
		$this->InputData->_ModelObject
			->expects($this->any())
			->method('getOptions')
			->will($this->returnCallback(function($options_key){
				switch ($options_key) {
					case 'normal_options':
						$return = array(1 => 'one', 2 => 'two');
						break;
					case 'special_options':
						$return = array(1 => 'special-one', 2 => 'special-two');
						break;
					default:
						$return = array();
						break;
				}
				return $return;
			}));
		
		$this->InputData->_model = $this->test_model;
		
		$this->InputData->_attributes['original'] = array(
			'id' => array(
				'type' => 'integer'
			),
			'normalOptions' => array(
				'type' => 'string',
				'field' => 'normal_options'
			),
			'specialOptions' => array(
				'type' => 'string',
				'field' => 'some_completely_different_field_name',
				'values' => array(
					'options' => 'special_options'
				)
			),
			'name' => array(
				'type' => 'text'
			)
		);
		
		$result = $this->InputData->convertAttributeOptions($data);
		
		$expected = $data;
		
		$expected['normalOptions'] = 1;
		$expected['specialOptions'] = 2;
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert All To Save All - With No Saveall Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testConvertAllToSaveAllWithNoSaveAllData() {
		
		$this->InputData->Controller->request->data = array(
			'MainModel' => array(
				'att1' => 'value1',
				'att2' => 'value2'
			)
		);
		
		$result = $this->InputData->convertAllToSaveAll();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array(
			0 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => 'value2'
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_save_all_keys;
		
		$expected = array(
			'_main' => false,
			'MainModel' => array(0 => false)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert All To Save All - With All Saveall Data
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testConvertAllToSaveAllWithAllSaveAllData() {
	
		$this->InputData->Controller->request->data = array(
			0 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => 'value2'
					),
					1 => array(
						'att1' => 'value1 diff',
						'att2' => 'value2 diff'
					)
				)
			),
			1 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1 mod',
						'att2' => 'value2 mod'
					)
				)
			)
		);
		
		$result = $this->InputData->convertAllToSaveAll();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array(
			0 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => 'value2'
					),
					1 => array(
						'att1' => 'value1 diff',
						'att2' => 'value2 diff'
					)
				)
			),
			1 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1 mod',
						'att2' => 'value2 mod'
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_save_all_keys;
		
		$expected = array(
			'_main' => true,
			'MainModel' => array(
				0 => true,
				1 => true
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
		
	/**
	 * Test Convert All To Save All - With Mixed Data and Related Models
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testConvertAllToSaveAllWithMixedDataAndRelatedModels() {
		
		$this->InputData->Controller->request->data = array(
			'MainModel' => array(
				'att1' => 'value1',
				'att2' => 'value2'
			),
			'RelatedModel' => array(
				'att3' => 'value3',
				'att4' => 'value4'
			),
			'RelatedModelTwo' => array(
				0 => array(
					'att3' => 'value3 diff',
					'att4' => 'value4 diff'
				)
			)
		);
		
		$result = $this->InputData->convertAllToSaveAll();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array(
			0 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => 'value2'
					)
				),
				'RelatedModel' => array(
					0 => array(
						'att3' => 'value3',
						'att4' => 'value4'
					)
				),
				'RelatedModelTwo' => array(
					0 => array(
						'att3' => 'value3 diff',
						'att4' => 'value4 diff'
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_save_all_keys;
		
		$expected = array(
			'_main' => false,
			'MainModel' => array(0 => false),
			'RelatedModel' => array(0 => false),
			'RelatedModelTwo' => array(0 => true)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert Single Back To Normal
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testConvertSingleBackToNormal() {
		
		$this->InputData->Controller->request->data = array(
			0 => array(
				'MainModel' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => 'value2'
					)
				),
				'RelatedModel' => array(
					0 => array(
						'att3' => 'value3',
						'att4' => 'value4'
					)
				),
				'RelatedModelTwo' => array(
					0 => array(
						'att3' => 'value3 diff',
						'att4' => 'value4 diff'
					)
				)
			)
		);
		
		$this->InputData->_save_all_keys = array(
			'_main' => false,
			'MainModel' => array(0 => false),
			'RelatedModel' => array(0 => false),
			'RelatedModelTwo' => array(0 => true)
		);
		
		$result = $this->InputData->convertSinglesBackToNormal();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array(
			'MainModel' => array(
				'att1' => 'value1',
				'att2' => 'value2'
			),
			'RelatedModel' => array(
				'att3' => 'value3',
				'att4' => 'value4'
			),
			'RelatedModelTwo' => array(
				0 => array(
					'att3' => 'value3 diff',
					'att4' => 'value4 diff'
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_save_all_keys;
		
		$expected = array();
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Normalize Attributes
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testNormalizeAttributes() {
		
		$this->InputData->_attributes = array(
			'unsorted' => array(
				'att1',
				'att2',
				'attribute3',
				'attribute4.sub1',
				'attribute4.sub2',
				'att5'
			),
			'sorted' => array(
				'attribute4.sub1',
				'attribute4.sub2',
				'attribute3',
				'att1',
				'att2',
				'att5'
			)
		);
		
		$data = array(
			'att1' => 'value1',
			'att2' => 'value2',
			'attribute3' => 'value3',
			'attribute4' => array(
				'sub1' => 'value4 sub1',
				'sub2' => 'value4 sub2'
			),
			'att5' => array(
				'value5-1',
				'value5-2'
			)
		);
		
		$result = $this->InputData->normalizeAttributes($data);
		
		$expected = $data;
		
		$expected['attribute4.sub1'] = $data['attribute4']['sub1'];
		$expected['attribute4.sub2'] = $data['attribute4']['sub2'];
		unset($expected['attribute4']);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Convert Attributes To Fields
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testConvertAttributesToFields() {
		
		$this->InputData->ApiInputDataComponentThing = $this->getMock('ApiInputDataComponentThingDouble', array(
			'attributes',
			'getFieldMap'
		));
		
		$thing_attributes = array(
			'att1' => array(
				'field' => 'field1'
			),
			'att2.sub1' => array(
				'field' => 'field2.subf1'
			),
			'att2.sub2' => array(
				'field' => 'field2.subf2'
			)
		);
		
		$this->InputData->ApiInputDataComponentThing
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($thing_attributes));
		
		$this->InputData->ApiInputDataComponentThing
			->expects($this->once())
			->method('getFieldMap')
			->with($this->equalTo($thing_attributes))
			->will($this->returnValue(array(
				'field1' => 'att1',
				'field2.subf1' => 'att2.sub1',
				'field2.subf2' => 'att2.sub2'
			)));
		
		$this->InputData->Controller->request->data = array(
			array(
				'ApiInputDataComponentThing' => array(
					array(
						'att1' => 'value1',
						'att2.sub1' => 'value1 sub1',
						'att2.sub2' => array(
							'value2 sub2-1',
							'value2 sub2-2'
						)
					)
				)
			)
		);
		
		$result = $this->InputData->forModel('ApiInputDataComponentThing')->convertAttributesToFields();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = array(
			array(
				'ApiInputDataComponentThing' => array(
					array(
						'field1' => 'value1',
						'field2' => array(
							'subf1' => 'value1 sub1',
							'subf2' => array(
								'value2 sub2-1',
								'value2 sub2-2'
							)
						)
					)
				)
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Prepare Normalized Data Loops
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testPrepareNormalizedDataLoops() {
		
		$data = array(
			0 => array(
				'ApiInputDataComponentThing' => array(
					0 => array(
						'att1' => 'value1',
						'att2' => array(
							'sub1' => 'value1 sub1',
							'sub2' => array(
								'value2 sub2-1',
								'value2 sub2-2'
							)
						)
					)
				)
			)
		);
		
		$this->InputData = $this->getMock(
			'ApiInputDataComponentDouble', 
			array(
				'forModel',
				'addRelated',
				'normalizeAttributes',
				'convertAttributeOptions',
				'convertIso8601ToSqlDatetime',
				'convertBooleanLiterals',
				'convertStringNulls'
			), 
			array($this->ComponentCollection)
		);
		
		$this->InputData->modelClass = $this->test_model;
		
		$this->InputData->{$this->test_model} = $this->Model;
		
		$this->InputData
			->expects($this->never())
			->method('addRelated');
		
		$this->InputData
			->expects($this->at(0))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
			
		$this->InputData
			->expects($this->at(1))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));

		$this->InputData
			->expects($this->at(2))
			->method('normalizeAttributes')
			->with($this->equalTo($data[0]['ApiInputDataComponentThing'][0]))
			->will($this->returnValue($data[0]['ApiInputDataComponentThing'][0]));
		
		$this->InputData
			->expects($this->at(3))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(4))
			->method('convertAttributeOptions')
			->with($this->equalTo($data[0]['ApiInputDataComponentThing'][0]))
			->will($this->returnValue($data[0]['ApiInputDataComponentThing'][0]));
		
		$this->InputData
			->expects($this->at(5))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(6))
			->method('convertIso8601ToSqlDatetime')
			->with($this->equalTo($data[0]['ApiInputDataComponentThing'][0]))
			->will($this->returnValue($data[0]['ApiInputDataComponentThing'][0]));
		
		$this->InputData
			->expects($this->at(7))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(8))
			->method('convertBooleanLiterals')
			->with($this->equalTo($data[0]['ApiInputDataComponentThing'][0]))
			->will($this->returnValue($data[0]['ApiInputDataComponentThing'][0]));
		
		$this->InputData
			->expects($this->at(9))
			->method('forModel')
			->with()
			->will($this->returnValue($this->InputData));
		
		$this->InputData
			->expects($this->at(10))
			->method('convertStringNulls')
			->with($this->equalTo($data[0]['ApiInputDataComponentThing'][0]))
			->will($this->returnValue($data[0]['ApiInputDataComponentThing'][0]));
		
		$this->InputData->Query = $this->getMock(
			'ApiQueryComponent',
			array('getTimezone'),
			array($this->ComponentCollection)
		);
		
		$this->InputData->Query
			->expects($this->once())
			->method('getTimezone')
			->with()
			->will($this->returnValue('UTC'));
		
		$this->InputData->ApiInputDataComponentThing = $this->getMock('ApiInputDataComponentThingDouble', array(
			'attributes',
			'getFieldMap'
		));
		
		$thing_attributes = array(
			'att1' => array(
				'field' => 'field1'
			),
			'att2.sub1' => array(
				'field' => 'field2.subf1'
			),
			'att2.sub2' => array(
				'field' => 'field2.subf2'
			)
		);
		
		$this->InputData->ApiInputDataComponentThing
			->expects($this->once())
			->method('attributes')
			->with()
			->will($this->returnValue($thing_attributes));
		
		$this->InputData->_model = 'ApiInputDataComponentThing';
		
		$this->InputData->Controller->request->data = $data;
		
		$result = $this->InputData->prepareNormalizedDataLoops();
		
		$this->assertInstanceOf('ApiInputDataComponent', $result);
		
		$result = $this->InputData->Controller->request->data;
		
		$expected = $data;
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_attributes['original'];
		
		$expected = $thing_attributes;
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_attributes['sorted'];
		
		$expected = array(
			'att2.sub2',
			'att2.sub1',
			'att1'
		);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->InputData->_attributes['unsorted'];
		
		$expected = array(
			'att1',
			'att2.sub1',
			'att2.sub2'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Add Related
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testAddRelated() {
		
		$this->InputData->addRelated('RelatedModelOne');
		$this->InputData->addRelated('RelatedModelTwo');
		
		$result = $this->InputData->_related;
		
		$expected = array(
			'RelatedModelOne',
			'RelatedModelTwo'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Related
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testRelated() {
		
		$this->InputData->_related = array(
			'RelatedModelOne',
			'RelatedModelTwo'
		);
		
		$result = $this->InputData->related();
		
		$expected = array(
			'RelatedModelOne',
			'RelatedModelTwo'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Related - Set/Get
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testRelatedSetGet() {
		
		$related = uniqid();
		
		$this->assertEquals($related, $this->InputData->related($related));

		$this->assertEquals($related, $this->InputData->related());
		
	}
	
}
