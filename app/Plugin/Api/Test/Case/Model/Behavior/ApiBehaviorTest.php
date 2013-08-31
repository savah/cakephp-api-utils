<?php
App::uses('ModelBehavior', 'Model');
App::uses('AppModel', 'Model');
App::uses('ApiBehavior', 'Api.Model' . DS . 'Behavior');

/**
 * Test Model
 *
 */
if (!class_exists('Thing')) {
	class Thing extends AppModel {}
}

/**
 * Api Behavior Test
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
 * @subpackage  Api.Model
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiBehaviorTest extends CakeTestCase {
	
	/**
	 * Setup
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->TestModel = $this->getMock('Thing');
		
		$this->ApiBehavior = new ApiBehavior();
		
		$this->ApiBehavior->setup($this->TestModel);
		
	}
	
	/**
	 * Tear Down
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function tearDown() {
		
		parent::tearDown();

		unset($this->ApiBehavior);
		
		unset($this->Thing);
		
		ClassRegistry::flush();
		
	}
	
	/**
     * Test id
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testIdEmptyId() {
		
		$id = null;
		
		$results = $this->ApiBehavior->id($this->TestModel, $id);
		
		$this->assertEquals($results, $this->TestModel);
		
		$this->assertEquals($id, $this->TestModel->id);
		
	}
	
	/**
     * Test Id
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testId() {
		
		$id = uniqid();
		
		$results = $this->ApiBehavior->id($this->TestModel, $id);
		
		$this->assertEquals($results, $this->TestModel);
		
		$this->assertEquals($id, $this->TestModel->id);
		
	}
	
	/**
     * Test User Id
     *
     * @author	Anthony Putignano <anthony@wizehive.com>
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testUserId() {
		
		$result = $this->ApiBehavior->userId($this->TestModel);
		
		$this->assertNull($result);
		
		$result = $this->ApiBehavior->userId($this->TestModel, 123);
		
		$this->assertInstanceOf('AppModel', $result);
		
		$result = $this->ApiBehavior->userId($this->TestModel);
		
		$expected = 123;
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test User Role
     *
     * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testUserRole() {
		
		$result = $this->ApiBehavior->userRole($this->TestModel);
		
		$this->assertNull($result);
		
		$result = $this->ApiBehavior->userRole($this->TestModel, 'default');
		
		$this->assertInstanceOf('AppModel', $result);
		
		$result = $this->ApiBehavior->userRole($this->TestModel);
		
		$expected = 'default';
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Attributes
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testAttributes() {
		
		$this->TestModel
			->expects($this->exactly(2))
			->method('schema')
			->with()
			->will($this->returnValue(array(
				'some_field' => array('type' => 'integer'),
				'some_other_field' => array('type' => 'string'),
				'created' => array('type' => 'datetime')
			)));
		
		$attributes = $this->TestModel->_attributes;
		
		$attributes['someAttribute'] = array('field' => 'some_field');
		$attributes['someOtherAttribute'] = array('field' => 'some_other_field');
		$attributes['created'] = array();
		
		$expected = Hash::normalize($attributes);
		$expected['someAttribute']['type'] = 'int';
		$expected['someOtherAttribute']['type'] = 'string';
		$expected['created'] = array(
			'field' => 'created',
			'type' => 'datetime'
		);
		
		$result = $this->ApiBehavior->attributes($this->TestModel, $attributes);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->ApiBehavior->attributes($this->TestModel);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Has Unique ID
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testHasUniqueID() {
		
		$test_model = 'Thing';
		
		$test_id = 1;
		
		$this->assertFalse($this->ApiBehavior->hasUniqueID($this->TestModel));
		
		$this->TestModel->uniqueID = $test_id;
		
		$this->assertFalse($this->ApiBehavior->hasUniqueID($this->TestModel));
		
		$this->TestModel->uniqueID = array($test_model => $test_id);
		
		$this->assertFalse($this->ApiBehavior->hasUniqueID($this->TestModel));
	
		$this->assertTrue($this->ApiBehavior->hasUniqueID($this->TestModel, $test_model));
		
	}
	
	/**
	 * Test Is Default Object Enabled
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIsDefaultObjectEnabled() {
		
		$result = $this->ApiBehavior->isDefaultObjectEnabled($this->TestModel);
		
		$this->assertFalse($result);
		
		$this->TestModel->defaultObject = true;
		
		$result = $this->ApiBehavior->isDefaultObjectEnabled($this->TestModel);
		
		$this->assertTrue($result);
		
	}
	
	/**
	 * Test Is Relation Saveable
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIsRelationSaveable() {
		
		$this->TestModel = $this->getMock('Thing', array(
			'getAssociated',
		));
		
		$this->TestModel
			->expects($this->any())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'Owner' => 'belongsTo',
				'Children' => 'hasMany',
				'Things' => 'hasMany'
			)));
		
		$this->TestModel->belongsTo = array(
			'Owner' => array()
		);
		$this->TestModel->hasMany = array(
			'Children' => array('saveable' => true),
			'Things' => array('saveable' => false)
		);
		
		$result = $this->ApiBehavior->isRelationSaveable($this->TestModel, 'Faulty');
		
		$this->assertFalse($result);
		
		$result = $this->ApiBehavior->isRelationSaveable($this->TestModel, 'Owner');
		
		$this->assertTrue($result);
		
		$result = $this->ApiBehavior->isRelationSaveable($this->TestModel, 'Children');
		
		$this->assertTrue($result);
		
		$result = $this->ApiBehavior->isRelationSaveable($this->TestModel, 'Things');
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Is Relation Findable
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testIsRelationFindable() {
		
		$this->TestModel = $this->getMock('Thing', array(
			'getAssociated'
		));
		
		$this->TestModel
			->expects($this->any())
			->method('getAssociated')
			->with()
			->will($this->returnValue(array(
				'Owner' => 'belongsTo',
				'Children' => 'hasMany',
				'Things' => 'hasMany'
			)));
		
		$this->TestModel->belongsTo = array(
			'Owner' => array()
		);
		$this->TestModel->hasMany = array(
			'Children' => array('findable' => true),
			'Things' => array('findable' => false)
		);
		
		$result = $this->ApiBehavior->isRelationFindable($this->TestModel, 'Faulty');
		
		$this->assertFalse($result);
		
		$result = $this->ApiBehavior->isRelationFindable($this->TestModel, 'Owner');
		
		$this->assertTrue($result);
		
		$result = $this->ApiBehavior->isRelationFindable($this->TestModel, 'Children');
		
		$this->assertTrue($result);
		
		$result = $this->ApiBehavior->isRelationFindable($this->TestModel, 'Things');
		
		$this->assertFalse($result);
		
	}
	
	/**
	 * Test Get Default Object
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetDefaultObject() {
		
		$this->TestModel
			->expects($this->once())
			->method('schema')
			->with()
			->will($this->returnValue(array(
				'field1' => array(
					'default' => null
				),
				'field2' => array(
					'default' => 0
				),
				'field3' => array(
					'default' => 'abc'
				)
			)));
		
		$result = $this->ApiBehavior->getDefaultObject($this->TestModel, null);
		
		$expected = array(
			'field1' => null,
			'field2' => 0,
			'field3' => 'abc'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Get Unique Conditions
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetUniqueConditions() {
		
		$test_model = 'Thing';
		
		$test_foreign_id = 1;
		
		$test_data = array(
			'testFieldOne' => 1,
			'testFieldTwo' => 2
		);
		
		$test_unique_id = array(
			$test_model => array(
				'foreignId' => 'foreign_id',
				'testFieldOne',
				'testFieldTwo'
			)
		);
		
		$this->TestModel->uniqueID = $test_unique_id;
		
		$results = $this->ApiBehavior->getUniqueConditions(
			$this->TestModel,
			$test_model,
			$test_foreign_id,
			$test_data
		);
		
		$expected = array(
			'foreignId' => 1,
			'testFieldOne' => 1,
			'testFieldTwo' => 2
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
     * Test Get Unique Id - True
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetUniqueIdTrue() {
		
		$test_conditions = array('User.id' => 1);
		
		$this->TestModel = $this->getMock('Thing', array('field'));
		
		$this->TestModel
			->expects($this->once())
			->method('field')
			->with('id', array($test_conditions))
			->will($this->returnValue(true));
		
		$this->assertTrue($this->ApiBehavior->getUniqueID($this->TestModel, $test_conditions));
		
	}
	
	/**
     * Test Get Unique Id - False
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetUniqueIdFalse() {
		
		$this->assertFalse($this->ApiBehavior->getUniqueID($this->TestModel));
		
	}
	
	/**
     * Test Get Default Attributes
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetDefaultAttributes() {
		
		$this->ApiBehavior = $this->getMock(
			'ApiBehavior',
			array(
				'attributes',
				'getBelongsToField'
			)
		);
		
		$this->ApiBehavior
			->expects($this->once())
			->method('attributes')
			->with($this->TestModel)
			->will($this->returnValue(array(
				'att1' => array(),
				'att2' => array(),
				'att3' => array()
			)));
		
		$result = $this->ApiBehavior->getDefaultAttributes($this->TestModel);
		
		$expected = array('att1', 'att2', 'att3');
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
     * Test Get Field Map
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetFieldMap() {
		
		$this->assertEquals(
			array(),
			$this->ApiBehavior->getFieldMap($this->TestModel)
		);
		
		$attributes = array(
			'attribute1' => array(
				'type' => 'string',
				'field' => 'attribute1'
			),
			'attribute2' => array(
				'type' => 'string',
				'field' => 'attribute2'
			),
			'attributeThree' => array(
				'type' => 'string',
				'field' => 'attribute_three'
			)
		);
		
		$results = $this->ApiBehavior->getFieldMap($this->TestModel, $attributes);
		
		$expected = array(
			'attribute1' => 'attribute1',
			'attribute2' => 'attribute2',
			'attribute_three' => 'attributeThree'
		);
		
		$this->assertEquals($results, $expected);
		
	}
	
	/**
	 * Test Get Options - With Valid Field
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetOptionsWithValidField() {
		
		$this->TestModel->_some_field_options = array(
			'black' => 'Black',
			'white' => 'White',
			'grey' => 'Grey'
		);
		
		$result = $this->ApiBehavior->getOptions($this->TestModel, 'some_field');
		
		$expected = array(
			'black' => 'Black',
			'white' => 'White',
			'grey' => 'Grey'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get Options - With Invalid Field
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetOptionsWithInvalidField() {
		
		$result = $this->ApiBehavior->getOptions($this->TestModel, 'some_field');
		
		$expected = array();
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get Options - With Options List Method
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetOptionsWithOptionsListMethod() {
		
		$this->TestModel = $this->getMock('Thing', array('someFakeFieldOptionsList'));
		
		$this->TestModel
			->expects($this->once())
			->method('someFakeFieldOptionsList')
			->with()
			->will($this->returnValue(array(1 => 'one', 2 => 'two')));
			
		$result = $this->ApiBehavior->getOptions($this->TestModel, 'some_fake_field');
		
		$expected = array(
			1 => 'one',
			2 => 'two'
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Get All Field Names
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetAllFieldNames() {
		
		$this->TestModel
			->expects($this->once())
			->method('schema')
			->with()
			->will($this->returnValue(array(
				'field_one' => array('some' => 'settings'),
				'field_two' => array('some' => 'settings')
			)));
		
		$this->TestModel
			->expects($this->once())
			->method('getVirtualField')
			->with()
			->will($this->returnValue(array(
				'virtual_field_one' => 'SELECT *',
				'virtual_field_two' => 'SELECT 3'
			)));
		
		$result = $this->ApiBehavior->getAllFieldNames($this->TestModel);
		
		$expected = array(
			$this->TestModel->alias .'.field_one',
			$this->TestModel->alias .'.field_two',
			$this->TestModel->alias .'.virtual_field_one',
			$this->TestModel->alias .'.virtual_field_two'
		);
		
		$this->assertEquals($expected, $result);
		
	}

	/**
     * Test getFieldNames
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
	 * @author	Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetFieldNames() {
		
		$this->assertEquals(array(), $this->ApiBehavior->getFieldNames($this->TestModel));
		
		$this->TestModel->alias = 'TestModel';
		
		$field_map = array(
			'field1' => 'attribute1',
			'field2' => 'attribute2',
			'field3' => 'attribute3',
			'Metadatum.something' => 'someThing'
		);
		
		$expected = array(
			$this->TestModel->alias . '.field1',
			$this->TestModel->alias . '.field2',
			$this->TestModel->alias . '.field3'
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiBehavior->getFieldNames($this->TestModel, $field_map)
		);
		
	}
	
	/**
     * Test Get Metadata Field Names
     *
     * @author  Anthony Putignano <anthony@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetMetadataFieldNames() {
		
		$this->assertEquals(
			array(),
			$this->ApiBehavior->getMetadataFieldNames($this->TestModel)
		);
		
		$field_map = array(
			'field1' => 'attribute1',
			'field2' => 'attribute2',
			'field3' => 'attribute3',
			'Metadatum.something' => 'someThing',
			'Metadatum.something.else' => 'someThingElse'
		);
		
		$expected = array(
			'something',
			'something.else'
		);
		
		$this->assertEquals(
			$expected,
			$this->ApiBehavior->getMetadataFieldNames($this->TestModel, $field_map)
		);
		
	}
	
	/**
     * Test Get Denormalized Fields - Empty `Model::$_denormalized_fields` Property
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetDenormalizedFieldsEmptySettings() {
		
		$this->assertFalse($this->ApiBehavior->getDenormalizedFields($this->TestModel));
		
	}
	
	/**
     * Test Get Denormalized Fields - With One Model And One Set
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetDenormalizedFieldsWithOneModelAndOneSet() {
		
		$_denormalized_fields = array(
			'workspace_id' => array(
				'field' => 'Form.workspace_id',
				'conditions' => array(
					'Form.id' => 'form_id'
				)
			)
		);
		
		$workspace_id = 244;
		
		$data = array(
			'form_id' => 1822,
			'validate' => 'true',
			'is_valid' => 1,
			'is_complete' => false
		);
		
		$this->TestModel->Form = $this->getMock(
			'Form',
			array('field')
		);
		
		$field_conditions = array(
			array('Form.id' => 1822)
		);
		
		$this->TestModel->Form
			->expects($this->once())
			->method('field')
			->with('workspace_id', $field_conditions)
			->will($this->returnValue($workspace_id));
		
		$this->TestModel->_denormalized_fields = $_denormalized_fields;
		
		$results = $this->ApiBehavior->getDenormalizedFields($this->TestModel, $data);
		
		$expected = array('workspace_id' => $workspace_id);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
     * Test Get Denormalized Fields - With Multiple Models And Settings
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetDenormalizedFieldsWithMultipleModelsAndSettings() {
		
		$_denormalized_fields = array(
			'workspace_id' => array(
				array(
					'field' => 'FormResponse.field_id',
					'conditions' => array(
						'FormResponse.id' => 'response_id'
					)
				),
				array(
					'field' => 'FormField.form_id',
					'conditions' => array(
						'FormField.id' => 'FormResponse.field_id'
					)
				),
				array(
					'field' => 'Form.workspace_id',
					'conditions' => array(
						'Form.id' => 'FormField.form_id'
					)
				)
			)
		);
		
		$form_response_id = 10;
		
		$form_field_id = 20;
		
		$form_id = 30;
		
		$workspace_id = 40;
		
		$data = array(
			'response_id' => $form_response_id,
			'value' => 'something'
		);
		
		// FormResponse
		
		$this->TestModel->FormResponse = $this->getMock(
			'FormResponse',
			array('field')
		);
		
		$form_response_conditions = array(
			array('FormResponse.id' => $form_response_id)
		);
		
		$this->TestModel->FormResponse
			->expects($this->once())
			->method('field')
			->with('field_id', $form_response_conditions)
			->will($this->returnValue($form_field_id));

		// FormField
		
		$this->TestModel->FormField = $this->getMock(
			'FormField',
			array('field')
		);
		
		$form_field_conditions = array(
			array('FormField.id' => $form_field_id)
		);
		
		$this->TestModel->FormField
			->expects($this->once())
			->method('field')
			->with('form_id', $form_field_conditions)
			->will($this->returnValue($form_id));

		// Form
		
		$this->TestModel->Form = $this->getMock(
			'Form',
			array('field')
		);
		
		$form_conditions = array(
			array('Form.id' => $form_id)
		);
		
		$this->TestModel->Form
			->expects($this->once())
			->method('field')
			->with('workspace_id', $form_conditions)
			->will($this->returnValue($workspace_id));
		
		$this->TestModel->_denormalized_fields = $_denormalized_fields;
		
		$results = $this->ApiBehavior->getDenormalizedFields($this->TestModel, $data);
		
		$expected = array('workspace_id' => $workspace_id);
		
		$this->assertEquals($expected, $results);
		
	}
	
	/**
     * Test Denormalized Fields - With Closure on 
     * `Model::$_denormalized_fields` Property
     *
     * @author  Everton Yoshitani <everton@wizehive.com>
     * @since	1.0
     * @return  void
     */
	public function testGetDenormalizedFieldsWithClosureSettings() {
		
		$_denormalized_fields = array(
			'record_id' => function($data) {
				
				if (
					empty($data['model']) ||
					empty($data['foreign_id'])
				) {
					return false;
				}
				
				$settings = array(
					'field' => $data['model'] .'.record_id',
					'conditions' => array(
						$data['model'] .'.id' => $data['foreign_id']
					)
				);
				
				return $settings;
				
			}
		);
		
		$record_id = 244;
		
		$data = array(
			'workspace_id' => 300,
			'model' => 'Event',
			'foreign_id' => 101
		);
		
		$this->TestModel->Event = $this->getMock(
			'Event',
			array('field')
		);
		
		$field_conditions = array(
			array(
				'Event.id' => $data['foreign_id']
			)
		);
		
		$this->TestModel->Event
			->expects($this->once())
			->method('field')
			->with('record_id', $field_conditions)
			->will($this->returnValue($record_id));
		
		$this->TestModel->_denormalized_fields = $_denormalized_fields;
		
		$results = $this->ApiBehavior->getDenormalizedFields($this->TestModel, $data);
		
		$expected = array('record_id' => $record_id);
		
		$this->assertEquals($expected, $results);
		
	}
	
}
