<?php
App::uses('AppModel', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('NullableForeignIdsBehavior', 'NullableForeignIds.Model' . DS . 'Behavior');

/**
 * Test Models
 *
 */
class NullableForeignIdsModel extends AppModel {}

/**
 * Nullable Foreign Ids Behavior Test
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
 * @package     NullableForeignIds
 * @subpackage  NullableForeignIds.Test.Case.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class NullableForeignIdsBehaviorTest extends CakeTestCase {

	/**
	 * Setup
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function setUp() {

		parent::setUp();

		$this->NullableForeignIdsBehavior = new NullableForeignIdsBehavior();

		$this->NullableForeignIdsModel = $this->getMock('NullableForeignIdsModel', array('attributes', 'schema'));
		
		$this->NullableForeignIdsModel
			->expects($this->any())
			->method('attributes')
			->with()
			->will($this->returnValue(array(
				'att1' => array(),
				'att2' => array('request' => true),
				'att3' => array()
			)));
		
		$this->NullableForeignIdsModel
			->expects($this->any())
			->method('schema')
			->with()
			->will($this->returnValue(array(
				'id' => array(),
				'slug' => array(),
				'first_foreign_id' => array(
					'type' => 'integer',
					'null' => false
				),
				'second_foreign_id' => array(
					'type' => 'integer',
					'null' => true
				),
				'third_foreign_id' => array(
					'type' => 'integer',
					'null' => true
				),
				'fourth_foreign_id' => array(
					'type' => 'integer',
					'null' => true
				),
				'fifth_foreign_id' => array(
					'type' => 'integer',
					'null' => true
				)
			)));

		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel, array(
			'first_foreign_id',
			'third_foreign_id',
			'fourth_foreign_id',
			'fifth_foreign_id'
		));

	}

	/**
	 * tearDown
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function tearDown () {

		parent::tearDown();

		unset($this->NullableForeignIdsBehavior);
		ClassRegistry::flush();

	}

	/**
	 * Test Instance Setup
	 * 
	 * @since	1.0
	 * @return	void 
	 */
	public function testInstanceSetup() {

		$this->assertIsA($this->NullableForeignIdsModel, 'Model');

		$expected = array(
			$this->NullableForeignIdsModel->alias => array(
				'nullableForeignIds' => array(
					'first_foreign_id',
					'third_foreign_id',
					'fourth_foreign_id',
					'fifth_foreign_id'
				),
				'schemaNullable' => null
			)
		);

		$this->assertEquals($expected, $this->NullableForeignIdsBehavior->settings);
	}

	/**
	 * Test beforeSave - no changes should be made when all foreign ids have valid values or are set to actual null
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeSaveNoChanges() {
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 1,
			'second_foreign_id' => null,
			'third_foreign_id' => 7,
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeSave($this->NullableForeignIdsModel);
		$this->assertSame($sample_data, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeValidate - no changes should be made when all foreign ids have valid values or are set to actual null
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeValidateNoChanges() {
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 1,
			'second_foreign_id' => null,
			'third_foreign_id' => 7,
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeValidate($this->NullableForeignIdsModel);
		$this->assertSame($sample_data, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeSave - change 0/empty string to actual null where allowed
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeSaveNullify() {
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => array(),
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => null,
			'second_foreign_id' => 0,
			'third_foreign_id' => null,
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeSave($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeValidate - change 0/empty string to actual null where allowed
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeValidateNullify() {
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => array(),
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => null,
			'second_foreign_id' => 0,
			'third_foreign_id' => null,
			'fourth_foreign_id' => 8,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeValidate($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeSave - change 0/empty string to actual null where allowed - model with _nullableForeignIds attribute specified
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeSaveNullifyWithSomeNullableFieldsAttr() {
		$this->NullableForeignIdsBehavior->cleanup($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel, array(
			'third_foreign_id',
			'fifth_foreign_id'
		));
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => null,
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeSave($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeValidate- change 0/empty string to actual null where allowed - model with _nullableForeignIds attribute specified
	 * 
	 * @author	Paul W. Smith <paul@wizehive.com>
	 * @since	1.0
	 * @return	void
	 */
	public function testBeforeValidateNullifyWithSomeNullableFieldsAttr() {
		$this->NullableForeignIdsBehavior->cleanup($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel, array(
			'third_foreign_id',
			'fifth_foreign_id'
		));
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => null,
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeValidate($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeSave - no options specified (should use DB schema instead of options)
	 * 
	 * @return	void
	 */
	public function testBeforeSaveNullifyWithNoSettings() {
		$this->NullableForeignIdsBehavior->cleanup($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel);
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => null,
			'third_foreign_id' => null,
			'fourth_foreign_id' => null,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeSave($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Test beforeValidate - no options specified (should use DB schema instead of options)
	 * 
	 * @return	void
	 */
	public function testBeforeValidateNullifyWithNoSettings() {
		$this->NullableForeignIdsBehavior->cleanup($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel);
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => false
		));
		
		$expected = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => null,
			'third_foreign_id' => null,
			'fourth_foreign_id' => null,
			'fifth_foreign_id' => null
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		$this->NullableForeignIdsBehavior->beforeValidate($this->NullableForeignIdsModel);
		$this->assertSame($expected, $this->NullableForeignIdsModel->data);
	}
	
	/**
	 * Make sure schema function is only called once, even when both beforeValidate and beforeSave are called
	 * 
	 * @return	void
	 */
	public function testBeforeValidateAndBeforeSave() {
		$this->NullableForeignIdsBehavior->cleanup($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->setup($this->NullableForeignIdsModel);
		
		$this->NullableForeignIdsModel
			->expects($this->once())
			->method('schema');
		
		$sample_data = array($this->NullableForeignIdsModel->alias => array(
			'id' => 5,
			'slug' => 'something',
			'first_foreign_id' => 0,
			'second_foreign_id' => 0,
			'third_foreign_id' => '',
			'fourth_foreign_id' => '',
			'fifth_foreign_id' => false
		));
		
		$this->NullableForeignIdsModel->data = $sample_data;
		
		$this->NullableForeignIdsBehavior->beforeValidate($this->NullableForeignIdsModel);
		$this->NullableForeignIdsBehavior->beforeSave($this->NullableForeignIdsModel);
	}

}
