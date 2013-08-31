<?php
App::uses('DispatcherFilter', 'Routing');

/**
 * Api Cors Preflight Dispatcher
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
class ApiCorsPreflightDispatcher extends DispatcherFilter {

	/**
	 * Priority
	 *
	 * @since   1.0
	 * @var	    integer
	 */
	public $priority = 1;

	/**
	 * Before Dispatch - Set CORS access control headers
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	CakeEvent		$event
	 * @return	CakeResponse
	 */
	public function beforeDispatch(CakeEvent $event) {
		
		$request = $event->data['request'];
		$response = $event->data['response'];
		
		$origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		
		$response->header('Access-Control-Allow-Origin: ' . $origin);
		$response->header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		
		if (!empty($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			$response->header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
		}
		
		// Currently exit on all OPTIONS requests
		// Fix for swagger-ui CORS preflight requests
		if ($request->is('OPTIONS')) {
			
			if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
				$response->header('Cache-Control: no-cache');
			} else {
				$response->header('Cache-Control: max-age=3600');
			}
			
			$event->stopPropagation();
			
			return $response;
			
		}
		
    }
	
}
