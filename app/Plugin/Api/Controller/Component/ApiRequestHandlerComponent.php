<?php
App::uses('RequestHandlerComponent', 'Controller' . DS . 'Component');

/**
 * Api Request Handler Component
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
 * @subpackage  Api.Controller.Component
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ApiRequestHandlerComponent extends RequestHandlerComponent {
	
	/**
	 * Process $_FILES
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @return	void 
	 */
	protected function __processFiles() {
		
		if (!empty($_FILES)) {
			$dimensions = Hash::dimensions($_FILES);
			if ($dimensions === 2) {
				$this->Controller->request->data = Hash::merge(
					$this->Controller->request->data,
					$_FILES
				);
			} else {
				foreach ($_FILES as $key => $data) {
					$parsed = array();
					foreach (array(
						'name',
						'type',
						'tmp_name',
						'error'
					) as $file_key) {
						$flattened = Hash::flatten($_FILES[$key][$file_key]);
						foreach ($flattened as $flat_key => $flat_value) {
							$reflattened = $key . '.' . $flat_key . '.' . $file_key;
							$parsed[$reflattened] = $flat_value;
						}
					}
					$parsed = Hash::expand($parsed);
					$this->Controller->request->data = Hash::merge(
						$this->Controller->request->data,
						$parsed
					);
				}
			}
		}
		
	}
	
	/**
	 * Calls `parent::startup()`
	 * 
	 * De-coupled for easier unit testing
	 *
	 * @author	Everton Yoshitani <everton@wizehive.com>
	 * @since	1.0
	 * @return  void
	 */
	protected function __callParentStartup(Controller $Controller) {
		return parent::startup($Controller);
	}
	
	/**
	 * Constructor. Parses the accepted content types accepted by the client using HTTP_ACCEPT
	 *
	 * @param   ComponentCollection $collection ComponentCollection object.
	 * @param   array $settings Array of settings.
	 * @return  void
	 */
	public function __construct(ComponentCollection $Collection, $settings = array()) {
		
		parent::__construct($Collection, $settings + array('checkHttpCache' => true));
		
		$this->Controller = $Collection->getController();
		
		if ($this->requestedWith('json')) {
			$this->__callParentStartup($this->Controller);
		}
		
	}
	
	/**
	 * Startup
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	Controller	$controller
	 * @return	void 
	 */
	public function startup(Controller $Controller) {
		
		if (!$this->requestedWith('json')) {
			$this->__callParentStartup($Controller);
		}
		
		$this->__processFiles();
		
	}
	
	/**
	 * Gets Remote Client IP
	 * 
	 * Overridden to account for comma-delimited IP formats that proxies create. 
	 * Strips away everything but first address (the client IP)
	 * 
	 * @since   1.0
	 * @author  Anthony Putignano <anthony@wizehive.com>
	 * @param   bool	$safe
	 * @return  string
	 */
	public function getClientIP($safe = false) {

		$result = parent::getClientIP($safe);

		$clientIp = explode(',', $result);

		foreach ($clientIp as $ipAddress) {
			$cleanIpAddress = trim($ipAddress);

			if (false !== filter_var($cleanIpAddress, FILTER_VALIDATE_IP)) {
				return $cleanIpAddress;
			}
		}

		return '';

	}
	
	/**
	 * Get The Subdomain a Request Is Originating From
	 * 
	 * @since   1.0
	 * @author  Anthony Putignano <anthony@wizehive.com>
	 * @param	string		Base URL. optional. If missing, then FULL_BASE_URL is attempted
	 * @return  mixed		Subdomain, or false if no subdomain
	 */
	public function getSubdomain($base_url = '') {
		
		if (empty($base_url) && defined('FULL_BASE_URL')) {
			$base_url = FULL_BASE_URL;
		}
		
		if (!empty($base_url)) {
			$url = parse_url($base_url);
			extract($url);
		}
		
		if (
			empty($base_url) || 
			empty($host) ||
			empty($scheme) ||
			(
				$scheme !== 'http' &&
				$scheme !== 'https'
			)
		) {
			return false;
		}
		
		$parts = explode('.', $host);
		
		$num_parts = count($parts);
		
		if ($num_parts <= 2) {
			return false;
		}
		
		$subdomain = '';
		$count = 1;
		
		foreach ($parts as $key => $part) {
			$num_remaining_parts = ($num_parts - $count);
			$subdomain .= $part;
			if ($num_remaining_parts > 2) {
				$subdomain .= '.';
			} else {
				break;
			}
			$count++;
		}
		
		return $subdomain;
		
	}
	
	/**
	 * Overload respondAs to Ignore Debug Status
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @param	mixed	$type
	 * @param	array	$options 
	 * @return  array
	 */
	public function respondAs($type, $options = array()) {
		
		// Get Current Debug Level
		$debug = Configure::read();
		
		// Disable Debug Mode if Enabled
		if (!($debug < 2)) {
			Configure::write('debug', 0);
		}
		
		// Call RequestHandler respondAs
		$result = parent::respondAs($type, $options);
		
		// Re Enable Debug Mode if Previously Enabled
		if (!($debug < 2)) {
			Configure::write('debug', $debug);
		}
		
		return $result;		
	}
	
	/**
	 * HTTP Header Wrapper
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since   1.0
	 * @param	string	$header
	 * @return	void
	 */
	public function header($header) {
		header($header);
	}
	
}
