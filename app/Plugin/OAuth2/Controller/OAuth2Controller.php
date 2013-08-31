<?php
App::uses('OAuth2AppController', 'OAuth2.Controller');

/**
 * OAuth2 Controller
 *
 * PHP 5
 *
 * Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Anthony Putignano <anthony@wizehive.com>
 * @since       0.1
 * @package     OAuth2
 * @subpackage  OAuth2.Controller
 * @copyright   Copyright (c) WizeHive, Inc. (http://www.wizehive.com)
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 */
class OAuth2Controller extends OAuth2AppController {
	
	/**
	 * Components
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	public $components = array(
		'RequestHandler', 
		'Session',
		'OAuth2.OAuth2'
	);
	
	/**
	 * Authorize action settings
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	public $authorizeActionSettings = array(
		'userIdKey' => 'User.id',
		'loginUrl' => array(
			'controller' => 'users',
			'action' => 'login',
			'plugin' => null
		)
	);
	
	
	/**
	 * Parent Before Render Callback
	 *
	 * De-coupled for easy testing
	 * 
	 * @since   0.1
	 * @var	    array
	 */
	public function __parentBeforeRender() {
		return parent::beforeRender();		
	}
	
	/**
	 * Override of core `Controller` method which unsets the `AuthComponent`
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @return	boolean 
	 */
	public function constructClasses() {
		
		$this->_mergeControllerVars();
		
		unset($this->components['Auth']);
		
		$this->Components->init($this);
		if ($this->uses) {
			$this->uses = (array) $this->uses;
			list(, $this->modelClass) = pluginSplit(current($this->uses));
		}
		return true;
		
	}
	
	/**
	 * Before render callback
	 * 
	 * Looks for an `OAuth2_Response` object and uses it to build a response,
	 * if possible. 
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @return	void
	 */
	public function beforeRender() {
		
		$response = $this->OAuth2->getResponse();
		
		if (
			empty($response) && 
			$this->request->is('get') &&
			$this->request->params['action'] === 'authorize'
		) {
			return true;
		}
		
		$type = $this->RequestHandler->responseType();
		
		if (empty($type) || !in_array($type, array('json', 'xml'))) {
			$this->viewClass = 'Json';
		}
		
		if (!empty($response)) {
		
			$this->response->statusCode($response->getStatusCode());
			$headers = $response->getHttpHeaders();
			foreach ($headers as $header => $value) {
				$this->response->header($header, $value);
			}
			$this->viewVars = array_merge(
				$response->getParameters(),
				is_array($this->viewVars) ? $this->viewVars : array()
			);
			
		}
		
		$this->viewVars = array(
			'data' => $this->viewVars,
			'_serialize' => 'data'
		);
		
		return $this->__parentBeforeRender();
		
	}
	
	/**
	 * Authorize user
	 * 
	 * Accepts a `client_id` and optional `client_secret` query string and, 
	 * based on a `response_type`, returns a newly minted `code` or `token`.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @return	void
	 */
	public function authorize() {
		
		$user_id = $this->Session->read('Auth.' . $this->authorizeActionSettings['userIdKey']);
		if (empty($user_id)) {
			$this->Session->write('Auth.redirect', Router::reverse($this->request));
			return $this->redirect($this->authorizeActionSettings['loginUrl'], 401);
		}
		
		$client_data = $this->OAuth2->validateAuthorizeRequest();
		
		if (empty($client_data)) {
			return false;
		}
		
		$api_key = $client_data['client_id'];
		
		$post_scope = !empty($this->request->data['scope']) ? $this->request->data['scope'] : null;
		$get_scope = !empty($this->request->query['scope']) ? $this->request->query['scope'] : null;
		$scope = !empty($post_scope) ? $post_scope : $get_scope;
		
		if (!isset($this->OAuth2Authorization)) {
			$this->loadModel('OAuth2.OAuth2Authorization');
		}
		
		$existing_authorization = $this->OAuth2Authorization->getExisting(
			$api_key,
			$user_id,
			$scope
		);
			
		$show_permissions_page = false;
		if (empty($existing_authorization) && $this->request->is('get')) {
			$show_permissions_page = true;
		}
		
		$proceed_with_authorization = false;
		if (!empty($existing_authorization) || $this->request->is('post')){
			$proceed_with_authorization = true;
		}
		
		if ($show_permissions_page) {
		
			$this->set('client', $client_data);
			
		} elseif ($proceed_with_authorization) {
			
			$allow = false;
			if (!empty($existing_authorization) || !empty($this->request->data['allow'])) {
				$allow = true;
			}
			
			$response = $this->OAuth2->handleAuthorizeRequest($allow, $user_id);
			
			if (empty($response)) {
				return false;
			}
			
			return $this->redirect($response->getHttpHeader('Location'), $response->getStatusCode());
			
		}
		
	}
	
	/**
	 * Grant token
	 * 
	 * Accepts a `client_id` and `client_secret` query string and
	 * grants a token.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @return	void
	 */
	public function grant() {
		
		$this->OAuth2->handleGrantRequest();
		
	}
	
	/**
	 * Verify token
	 * 
	 * Accepts a `access_token` query string and sends back data
	 * which verifies it.
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	0.1
	 * @return	void
	 */
	public function token() {
		
		$response = $this->OAuth2->getAccessTokenData();
		
		if (empty($response)) {
			return false;
		}
		
		$this->set($response);
		
	}
	
}
