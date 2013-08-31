<?php
App::uses('ApiResponseCode', 'Api.Model');

class ApiResponseCodeTest extends CakeTestCase {
	
	/**
	 * Setup
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	*/
	public function setUp() {
		
		parent::setUp();
		
		$this->ApiResponseCode = new ApiResponseCode();
		
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
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testInstanceSetup() {
		
		$this->assertObjectHasAttribute('records', $this->ApiResponseCode);
	}
	
	/**
	 * Test Find By Id - Not Found
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFindByIdNotFound() {
		
		$id = uniqid();
		
		$this->assertFalse($this->ApiResponseCode->findById($id));
		
	}
	
	/**
	 * Test Find By Id - Test 5 Codes
	 *
	 * @author  Everton Yoshitani <everton@wizehive.com>
	 * @since   1.0
	 * @return  void
	 */
	public function testFindById() {
		
		$codes = array_rand($this->ApiResponseCode->records, 5);

		foreach ($codes as $code) {
			
			$data = $this->ApiResponseCode->records[$code];
			
			$expected = array($this->ApiResponseCode->alias => $data);
			
			$results = $this->ApiResponseCode->findById($data['id']);
			
			$this->assertEquals($expected, $results);
			
		}
		
	}
	
}
