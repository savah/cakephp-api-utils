<?php
App::uses('AppController', 'Controller');

/**
 * In the case of an error, we disable a bunch of components so that they do not
 * run and cause issues.
 */

class AppErrorController extends AppController {

	public $uses = false;

	public function __construct($request = null, $response = null) {
		
		parent::__construct($request, $response);
		
		$this->_mergeControllerVars();
		
		unset($this->components['Acl']);
		unset($this->components['Auth']);
		
		$this->Components->init($this);
		if ($this->uses) {
			$this->uses = (array)$this->uses;
			list(, $this->modelClass) = pluginSplit(current($this->uses));
		}
		
		if (
			count(Router::extensions()) &&
			!$this->Components->attached('RequestHandler')
		) {
			$this->RequestHandler = $this->Components->load('RequestHandler');
		}
		
		if ($this->Components->enabled('Auth')) {
			$this->Components->disable('Auth');
		}
		
		if ($this->Components->enabled('Security')) {
			$this->Components->disable('Security');
		}
		
		$this->_set(array('cacheAction' => false, 'viewPath' => 'Errors'));
		
	}

}
