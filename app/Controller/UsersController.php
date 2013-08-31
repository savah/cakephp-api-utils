<?php
App::uses('AppController', 'Controller');

/**
 * Handles login, logout & signup, in addition to the general `users` resource in the API
 */

class UsersController extends AppController {
	
	protected $_oauth2_authorize_route = array(
		'plugin' => null,
		'controller' => 'AppOAuth2',
		'action' => 'authorize'
	);
	
	// Paths used by auth which render a normal view and are not API-driven
	protected $_auth_paths = array(
		'login',
		'logout',
		'signup'
	);
	
	public $components = array(
		'Session'
	);
	
	// Sets up `Auth` component and exempts login methods from API processing.
	public function beforeFilter() {
		
		call_user_func_array(array($this->Auth, 'allow'), $this->_auth_paths);
		
		if (in_array($this->request->params['action'], $this->_auth_paths)) {
			
			$this->Auth->authenticate = array(
				'Form' => array(
					'fields' => array(
						'username' => 'username',
						'password' => 'password'
					),
					'userModel' => 'User',
					'contain' => array('Role')
				)
			);
			
			$this->Api->exemptActions($this->_auth_paths);
			
		}
		
		return parent::beforeFilter();
		
	}
	
	public function login() {
		
		if (!empty($this->data)) {

			if ($this->Auth->login()) {
				
				// calling it this way clears the redirect in the session
				$this->redirect($this->Auth->redirectUrl());
				return;

			} else {

				$user_id = $this->User->field('id', array(
					'username' => $this->request->data['User']['username']
				));

				if (!empty($user_id)) {

					// User found, but bad password
					$this->Session->setFlash('Login failed', 'default');
					$this->redirect(array('action' => 'login'));
					return false;

				} else {

					// User not found
					$this->Session->setFlash('Login failed', 'default');
					$this->redirect(array('action' => 'login'));
					return false;

				}

			}

		}

		$redirect = $this->Session->read('Auth.redirect');
		
		$oauth2_authorize_url = Router::url($this->_oauth2_authorize_route);
		
		if (substr($redirect, 0, strlen($oauth2_authorize_url)) !== $oauth2_authorize_url) {
			$this->response->statusCode(403);
			$this->Session->setFlash('Invalid redirect', 'default');
			$this->render('error');
			return false;
		}
		
	}
	
	// `me` Convenience Resource for Returning Data About the Currently Authenticated User
	public function me() {
		
		$this->view($this->Auth->user('id'));
		return;
		
	}

	public function logout() {
		
		$this->Auth->logout();
		// You would put your primary frontend client here
		$this->redirect('http://client.api-demo.dev');
		return;
		
	}
	
	public function signup() {

		if (!empty($this->data)) {

			$this->view = 'signup';
			
			$this->User->begin();
			
			$this->User->create(false);
			
			if (!$this->User->save($this->request->data, array(
				'fieldList' => array(
					'username',
					'password',
					'display_name',
					'email'
				)
			))) {
				
				$this->User->rollback();

				$this->response->statusCode(400);
				$this->Session->setFlash(
					'Please fix the errors below and try again.',
					'default'
				);
				return false;

			}
			
			$this->User->commit();
			
			$this->response->statusCode(200);
			$this->Session->setFlash(
				'Your account has been created.<br />Please sign in below.',
				'default'
			);
			$this->redirect(array(
				'action' => 'login'
			));
			return true;

		}

	}
	
	/**
	 * Override the default `add` method so that when a user is added, the API
	 * outputs a friendly message saying that to view the user or do anything as
	 * the user, the current API consumer needs to re-authenticate as that user
	 */
	public function add() {
		
		$result = $this->Api->Resource
			->forModel('User')
			->withId(0)
			->withData($this->request->data)
			->save();

		if (empty($result)) {
			return false;
		}
		
		$this->Api->setResponseCode(2000);
		
		$this->Api->specialViewVars(array(
			'developerMessage' => 'The user was successfully created. To view and/or act as the user, ' .
									'you must authenticate and retrieve an access token.'
		));
		
		return true;
		
	}
	
}
