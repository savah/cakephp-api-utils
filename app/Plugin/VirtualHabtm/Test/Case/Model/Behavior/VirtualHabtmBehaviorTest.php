<?php
App::uses('AppModel', 'Model');
App::uses('ModelBehavior', 'Model');
App::uses('VirtualHabtmBehavior', 'VirtualHabtm.Model' . DS . 'Behavior');

/**
 * Test Models
 *
 */
if (!class_exists('Thing')) {
	class Thing extends AppModel {}
}

if (!class_exists('Stuff')) {
	class Stuff extends AppModel {}
}

if (!class_exists('ThingsStuff')) {
	class ThingsStuff extends AppModel {}
}

/**
 * Virtual Habtm Behavior Test
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
 * @subpackage  Api.Test.Case.Model.Behavior
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class VirtualHabtmBehaviorTest extends CakeTestCase {
	
	/**
	 * Setup
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->Thing = $this->getMock('Thing', array('getAssociated', 'bindModel'));
		
		$this->Thing
			->expects($this->once())
			->method('getAssociated')
			->will($this->returnValue(array()));
		
		$this->Thing
			->expects($this->once())
			->method('bindModel')
			->with($this->equalTo(array(
				'hasMany' => array(
					'ThingsStuff' => array(
						'className' => 'ThingsStuff',
						'foreignKey' => 'thing_id',
						'dependent' => true,
						'request' => true
					)
				)
			)))
			->will($this->returnValue(true));
		
		$this->Thing->Stuff = $this->getMock('Stuff', array(
			'exists'
		));
		
		$this->Thing->ThingsStuff = $this->getMock('ThingsStuff', array(
			'deleteAll', 
			'saveMany'
		));
		
		$this->VirtualHabtmBehavior = new VirtualHabtmBehavior();
		
		$this->VirtualHabtmBehavior->setup($this->Thing, array(
			'fields' => array(
				'foreign_ids' => array(
					'foreignModel' => 'Stuff',
					'joinModel' => 'ThingsStuff',
					'foreignKey' => 'thing_id',
					'associationKey' => 'stuff_id'
				)
			)
		));
		
	}
	
	/**
	 * tearDown
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function tearDown () {
		
		parent::tearDown();

		unset($this->VirtualHabtmBehavior);
		unset($this->Thing);
		ClassRegistry::flush();
		
	}
	
	/**
	 * Test Instance Setup
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	public function testInstanceSetup() { }
	
	/**
	 * Test validation
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testValidation() {
		
		$this->assertArrayHasKey('foreign_ids', $this->Thing->validate);
		
	}
	
	/**
	 * Test `afterSave` - created
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testAfterSaveCreated() {
		
		$this->Thing->ThingsStuff
			->expects($this->never())
			->method('deleteAll');
		
		$this->Thing->ThingsStuff
			->expects($this->once())
			->method('saveMany')
			->with($this->equalTo(array(
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 1
					)
				),
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 2
					)
				),
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 3
					)
				)
			)), $this->equalTo(array(
				'validate' => false,
				'atomic' => false,
				'callbacks' => false
			)))
			->will($this->returnValue(true));
		
		$this->Thing->data = array(
			$this->Thing->alias => array(
				'foreign_ids' => '1,2,3'
			)
		);
		
		$this->Thing->id = 1;
		$this->assertTrue($this->VirtualHabtmBehavior->afterSave($this->Thing, true));
		
	}
	
	/**
	 * Test `afterSave` - updated
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testAfterSaveUpdated() {
		
		$this->Thing->ThingsStuff
			->expects($this->once())
			->method('deleteAll')
			->with($this->equalTo(array(
				$this->Thing->ThingsStuff->alias . '.thing_id' => 1
			)), $this->equalTo(false), $this->equalTo(false))
			->will($this->returnValue(true));
		
		$this->Thing->ThingsStuff
			->expects($this->once())
			->method('saveMany')
			->with($this->equalTo(array(
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 1
					)
				),
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 2
					)
				),
				array(
					$this->Thing->ThingsStuff->alias => array(
						'thing_id' => 1,
						'stuff_id' => 3
					)
				)
			)), $this->equalTo(array(
				'validate' => false,
				'atomic' => false,
				'callbacks' => false
			)))
			->will($this->returnValue(true));
		
		$this->Thing->data = array(
			$this->Thing->alias => array(
				'foreign_ids' => '[1,2,3]'
			)
		);
		
		$this->Thing->id = 1;
		$this->assertTrue($this->VirtualHabtmBehavior->afterSave($this->Thing, false));
		
	}
	
	/**
	 * Test `validateHabtmRelation` - non-numeric values
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testValidateHabtmRelationNonNumericValues() {
		
		$this->Thing->Stuff
			->expects($this->never())
			->method('exists');
		
		$this->assertFalse($this->VirtualHabtmBehavior->validateHabtmRelation($this->Thing, array('foreign_ids' => '1,abc,3')));
		
	}
	
	/**
	 * Test `validateHabtmRelation` - duplicate foreign IDs
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testValidateHabtmRelationDuplicateForeignIds() {
		
		$this->Thing->Stuff
			->expects($this->exactly(3))
			->method('exists')
			->will($this->returnCallback(function($foreign_id){
				if (!in_array($foreign_id, array(1,2,3))) {
					return false;
				}
				return true;
			}));
		
		$this->assertFalse($this->VirtualHabtmBehavior->validateHabtmRelation($this->Thing, array('foreign_ids' => '1,2,3,2')));
		
	}
	
	/**
	 * Test `validateHabtmRelation` - invalid foreign IDs
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testValidateHabtmRelationInvalidForeignIds() {
		
		$this->Thing->Stuff
			->expects($this->exactly(2))
			->method('exists')
			->will($this->returnCallback(function($foreign_id){
				if (!in_array($foreign_id, array(1,3))) {
					return false;
				}
				return true;
			}));
		
		$this->assertFalse($this->VirtualHabtmBehavior->validateHabtmRelation($this->Thing, array('foreign_ids' => '1,2,3')));
		
	}
	
	/**
	 * Test `validateHabtmRelation` - valid foreign IDs
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testValidateHabtmRelationValidForeignIds() {
		
		$this->Thing->Stuff
			->expects($this->exactly(3))
			->method('exists')
			->will($this->returnCallback(function($foreign_id){
				if (!in_array($foreign_id, array(1,2,3))) {
					return false;
				}
				return true;
			}));
		
		$this->assertTrue($this->VirtualHabtmBehavior->validateHabtmRelation($this->Thing, array('foreign_ids' => '1,2,3')));
		
	}
	
	/**
	 * Test `getHabtmAssociationIdArray`
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	public function testGetHabtmAssociationIdArray() {
		
		$result = $this->VirtualHabtmBehavior->getHabtmAssociationIdArray($this->Thing, '1,2,3');
		$expected = array(1, 2, 3);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->VirtualHabtmBehavior->getHabtmAssociationIdArray($this->Thing, '1, 2, 3');
		$expected = array(1, 2, 3);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->VirtualHabtmBehavior->getHabtmAssociationIdArray($this->Thing, '[1,2,3]');
		$expected = array(1, 2, 3);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->VirtualHabtmBehavior->getHabtmAssociationIdArray($this->Thing, '["1,2,3"]');
		$expected = array(1, 2, 3);
		
		$this->assertEquals($expected, $result);
		
		$result = $this->VirtualHabtmBehavior->getHabtmAssociationIdArray($this->Thing, array(1, 2, 3));
		$expected = array(1, 2, 3);
		
		$this->assertEquals($expected, $result);
		
	}
	
}
