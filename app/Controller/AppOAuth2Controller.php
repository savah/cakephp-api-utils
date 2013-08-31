<?php
App::uses('OAuth2Controller', 'OAuth2.Controller');

/**
 * Configure oAuth 2.
 * 
 * Very importantly, ensure that oAuth 2 actions are NOT considered automagic API actions,
 * as they have their own rendering methods.
 */

class AppOAuth2Controller extends OAuth2Controller {

	public $authorizeActionSettings = array(
		'userIdKey' => 'User.id',
		'loginUrl' => array(
			'controller' => 'users',
			'action' => 'login',
			'plugin' => null
		)
	);
	
	public function beforeFilter() {
		
		$this->Api->exemptActions(array(
			'authorize',
			'grant',
			'token'
		));
		
		return parent::beforeFilter();
		
	}
	
	public function beforeRender() {
		
		App::build(array('View' => array(App::pluginPath('OAuth2') . 'View' . DS)));
		$this->viewPath = 'OAuth2';
		
		return parent::beforeRender();
		
	}
	
}
