<?php
App::uses('Model', 'Model');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('ApiPaginatorComponent', 'Api.Controller' . DS . 'Component');

/**
 * Api Paginator Component Test
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
class ApiPaginatorComponentTest extends CakeTestCase {

	/**
	 * Setup
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function setUp() {
		
		parent::setUp();
		
		$this->Controller = $this->getMock('Controller');
		
		$this->ComponentCollection = $this->getMock('ComponentCollection', array(
			'getController'
		));

		$this->ComponentCollection->expects($this->any())
			->method('getController')
			->will($this->returnValue($this->Controller));
		
		$this->ApiPaginator = new ApiPaginatorComponent($this->ComponentCollection);
		
	}
	
	/**
	 * Tear Down
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
	 * Test Instance Setup
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testInstanceSetup() {
		
		$settings = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring'
		);
		
		$this->ApiPaginator = new ApiPaginatorComponent(
			$this->ComponentCollection,
			$settings
		);
		
		$this->assertEquals($settings, $this->ApiPaginator->settings);
		
	}
	
	/**
	 * Test Validate Sort
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testValidateSort() {
		
		$Object = $this->getMock('Model', array(
			'hasField'
		));
		
		$Object
			->expects($this->atLeastOnce())
			->method('hasField')
			->with('id')
			->will($this->returnValue(true));
			
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring'
		);
		
		$whitelist = array();
		
		$options2 = array_merge(
			$options,
			array('order' => array('id' => 'asc'))
		);
		
		$this->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array('multipleSort', 'defaultOrder'),
			array($this->ComponentCollection)
		);
		
		$this->ApiPaginator
			->expects($this->once())
			->method('multipleSort')
			->with($Object, $options, $whitelist)
			->will($this->returnValue($options));
			
		$this->ApiPaginator
			->expects($this->once())
			->method('defaultOrder')
			->with($Object, $options, $whitelist)
			->will($this->returnValue($options2));
			
		$result = $this->ApiPaginator->validateSort($Object, $options, $whitelist);
		
		$expected = array(
			'page' => 1,
			'limit' => $options['limit'],
			'maxLimit' => $options['maxLimit'],
			'paramType' => 'querystring',
			'order' => array(
				$Object->alias .'.id' => 'asc'
			)
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Multiple Sort - Empty Sort Attributes
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMultipleSortEmptySortAttributes() {
		
		$Object = $this->getMock('Model');
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring'
		);
		
		$whitelist = array();
		
		$result = $this->ApiPaginator->multipleSort($Object, $options, $whitelist);
		
		$this->assertEquals($options, $result);
		
	}
	
	/**
	 * Test Multiple Sort - Single Sort Attribute
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMultipleSingleSortAttribute() {
		
		$options = array(
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'querystring',
			'sort' => 'workspace_id',
			'direction' => 'desc'
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$expected = array(
			'page' => $options['page'],
			'limit' => $options['limit'],
			'maxLimit' => $options['maxLimit'],
			'paramType' => $options['paramType'],
			'order' => array(
				'workspace_id' => 'desc'
			)
		);
		
		$result = $this->ApiPaginator->multipleSort($Object, $options, $whitelist);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Multiple Sort - Default Direction (Ascend/Asc)
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMultipleDefaultDirection() {
		
		$options = array(
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'querystring',
			'sort' => 'workspace_id',
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$expected = array(
			'page' => $options['page'],
			'limit' => $options['limit'],
			'maxLimit' => $options['maxLimit'],
			'paramType' => $options['paramType'],
			'order' => array(
				'workspace_id' => 'asc'
			)
		);
		
		$result = $this->ApiPaginator->multipleSort($Object, $options, $whitelist);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Multiple Sort - Multiple Sort Attributes And Directions
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMultipleMultipleSortAttributesAndDirections() {
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring',
			'sort' => array('id', 'workspace_id'),
			'direction' => array('asc', 'desc')
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$expected = array(
			'page' => $options['page'],
			'limit' => $options['limit'],
			'maxLimit' => $options['maxLimit'],
			'paramType' => $options['paramType'],
			'order' => array(
				'id' => 'asc',
				'workspace_id' => 'desc'
			)
		);
		
		$result = $this->ApiPaginator->multipleSort($Object, $options, $whitelist);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Multiple Sort - Multiple Sort Attributes But Not Equal Directions
	 * 
	 * Fallbacks to default direction (asc) or the first passed
	 *
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMultipleMultipleSortAttributesButNotEqualDirections() {
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring',
			'sort' => array('id', 'workspace_id', 'user_id'),
			'direction' => array('desc', 'asc')
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$expected = array(
			'page' => $options['page'],
			'limit' => $options['limit'],
			'maxLimit' => $options['maxLimit'],
			'paramType' => $options['paramType'],
			'order' => array(
				'id' => 'desc',
				'workspace_id' => 'asc',
				'user_id' => 'desc'
			)
		);
		
		$result = $this->ApiPaginator->multipleSort($Object, $options, $whitelist);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Default Order - With Order Array Set
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testDefaultOrderWithOrderArraySet() {
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring',
			'order' => array(
				'workspace_id' => 'desc'
			)
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$result = $this->ApiPaginator->defaultOrder($Object, $options, $whitelist);
		
		$this->assertEquals($options, $result);
		
	}
	
	/**
	 * Test Default Order - With Order Array Set
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testDefaultOrderWithModelOrderPropertySet() {
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring'
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$Object->order = array('workspace_id' => 'desc');
		
		$result = $this->ApiPaginator->defaultOrder($Object, $options, $whitelist);
		
		$this->assertEquals($options, $result);
		
	}
	
	/**
	 * Test Default Order - With Order Array Set
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testDefaultOrderApplyDefaultOrder() {
		
		$options = array(
			'page' => 1,
			'limit' => rand(),
			'maxLimit' => rand(),
			'paramType' => 'querystring'
		);

		$whitelist = array();
		
		$Object = $this->getMock('Model');
		
		$Object->primaryKey = 'custom_primary_id';
		
		$result = $this->ApiPaginator->defaultOrder($Object, $options, $whitelist);
		
		$expected = array_merge(
			$options,
			array('order' => array($Object->primaryKey => 'asc'))
		);
		
		$this->assertEquals($expected, $result);
		
	}
	
	/**
	 * Test Merge Options
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMergeOptions() {
		
		$alias = 'User';
		
		$defaults = array(
			'paramType' => 'querystring',
			'contain' => false,
			'parseTypes' => true,
			'parentModel' => 'Workspace'
		);
		
		$this->ApiPaginator = $this->getMock(
			'ApiPaginatorComponent',
			array('getDefaults'),
			array($this->ComponentCollection)
		);
		
		$this->ApiPaginator
			->expects($this->once())
			->method('getDefaults')
			->with($alias)
			->will($this->returnValue($defaults));
			
		$this->assertEquals($defaults, $this->ApiPaginator->mergeOptions($alias));
		
	}
	
	/**
	 * Test Merge Options - Parent Model
	 * 
	 * @author 	Everton Yoshitani <everton@wizehive.com>
	 * @since 	1.0
	 * @return 	void
	 */
	public function testMergeOptionsEmptyParentModel() {
		
		$limit = 9999;
		
		$alias = 'User';
		
		$defaults = $this->ApiPaginator->getDefaults(null);
		
		$expected = array_merge(
			$defaults,
			array('limit' => $limit)
		);
		
		$this->ApiPaginator->Controller->request->params['named']['limit'] = $limit;
		
		$this->assertEquals($expected, $this->ApiPaginator->mergeOptions($alias));
		
	}
	
}
