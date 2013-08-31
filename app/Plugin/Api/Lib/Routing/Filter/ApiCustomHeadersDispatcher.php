<?php
App::uses('DispatcherFilter', 'Routing');

/**
 * Api Custom Headers Dispatcher
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
 * @subpackage  Api.Lib.Routing.Filter
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiCustomHeadersDispatcher extends DispatcherFilter {

	/**
	 * Priority
	 *
	 * @since   1.0
	 * @var	    integer
	 */
	public $priority = 1;

	/**
	 * Before Dispatch - Set Custom Headers
	 * 
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @param	CakeEvent		$event
	 * @return	CakeResponse
	 */
	public function afterDispatch(CakeEvent $event) {
		
		$request = $event->data['request'];
		$response = $event->data['response'];
		
		if (defined('X_RESPONSE_TIME') && X_RESPONSE_TIME === true) {
			$time = microtime(true) - TIME_START;
			$response->header('X-Response-Time: ' . round($time, 3));
		}
		
		// Add here any other custom response headers
		
		return $response;
		
    }
	
}
