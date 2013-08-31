<?php
App::uses('Controller', 'Controller');

/**
 * A lot of the logic in here can debatably be part of the Api and/or OAuth2 plugins.
 * However, we have chosen to view them as configurations which will likely vary
 * heavily from application to application. Simply customize to suit.
 */

class AppController extends Controller {

	public $uses = array();

	public $components = array(
		'Acl' => array(),
		'Auth' => array(
			'unauthorizedRedirect' => false,
			'authenticate' => array(
				'OAuth2.OAuth2' => array(
					'access_lifetime' => 3600, // 1 hour
					'refresh_token_lifetime' => 1209600, // 14 days
					'userModel' => 'User',
					'fields' => array(
						'username' => 'username',
						'password' => 'password'
					),
					'contain' => array('Role'),
				),
				'OAuth2.OAuth2Public' => array()
			),
			'authError' => 'Authentication failure.',
			'realm' => 'API Demo',
			'authorize' => array(
				'Actions' => array(
					'actionPath' => 'controllers'
				)
			),
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'login',
				'plugin' => null
			)
		),
		// This needs to be before the `Api.Api` Component in order
		// for the uploads to be properly parsed
		'Api.ApiRequestHandler',
		'Api.Api'
	);
	
	public $codes = array(
		2000 => 'OK'
	);
	
	protected function _abort($message = '') {
		
		die($message);
		
	}
	
	/**
	 * This handles our application's custom auth rules, with a lot of aid from
	 * the components above.
	 *
	 * What's happening here is that IF there has not been an application error,
	 * then our custom ACL adapter is instantiated. From there, the application
	 * checks to see if this is a public request. If so, it doesn't require an
	 * auth token (but will accept one if that's what's available). If it's a 
	 * private request, then an auth token is always required. If the proper
	 * public or private credentials are not presented, errors are output.
	 */
	public function beforeFilter() {
		
		if (!empty($this->name) && $this->name !== 'AppError') {
			
			$controller = strtolower(str_replace('_', '', $this->request->params['controller']));
			$action = strtolower($this->request->params['action']);
			
			$this->Acl->adapter('ApiPhpAcl');

			// If the action is defined as Public and there's a valid Client ID, tell `Auth` to allow it
			if ($this->Acl->check('Role/public', 'controllers/' . $controller . '/' . $action)) {
				$allow = true;
				if (!isset($this->OAuth2)) {
					$this->OAuth2 = $this->Components->load('OAuth2.OAuth2');
				}
				$this->OAuth2->initialize($this);
				$client_id = $this->OAuth2->getClientId();
				if (empty($client_id)) {
					if (empty($this->request->query['access_token'])) {
						$this->OAuth2->getResponse()->send();
						$this->_abort();
						return false;
					}
					$allow = false;
				}
				if (!$this->OAuth2->checkClientCredentials($client_id)) {
					if (empty($this->request->query['access_token'])) {
						$this->OAuth2
							->errorResponse(400, 'invalid_client', 'The client credentials are invalid')
							->send();
						$this->_abort();
						return false;
					}
					$allow = false;
				}
				if ($allow) {
					
					unset($this->Auth->authenticate['OAuth2.OAuth2']);
					$this->Auth->constructAuthenticate();
					
				}
			}
			
		}
		
		return parent::beforeFilter();
		
	}
	
	/**
	 * If a redirect is occuring, then make sure the current request is considered
	 * an API request. If so, then render out a response rather than redirecting.
	 */
	public function beforeRedirect($url, $status = null) {
		
		if (in_array($this->params['action'], $this->Api->exemptActions())) {
			return true;
		}
		
		$this->Api->statusCode($status);
		
		$this->render();
		
		$this->response->send();
		$this->_abort();
		
		return false;
		
	}

	/**
	 * Before rendering, make sure the current request is considered an API request.
	 * If so, 
	 */
	public function beforeRender() {
		
		if (in_array($this->params['action'], $this->Api->exemptActions())) {
			return true;
		}

		$type = $this->ApiRequestHandler->responseType();
		
		switch ($type) {
			
			case 'xml':
				$this->Api->parseViewVars();
				$this->Api->formatXmlResponse();
				break;
				
			// Include a CSV view in your app and your API automagically handles .csv extensions!
			case 'csv':
				$this->Api->formatCsvResponse();
				break;
				
			// JSON is the default format
			default:
				$this->Api->parseViewVars();
				$this->Api->formatJsonResponse();
			
		}
		
		return;
		
	}
	
	// Default index action. Overridable/extendable from each controller.
	public function index() {
		
		$response = $this->Api->Resource
			->forModel($this->modelClass)
			->returnsApiResponse();
		
		if ($response === false) {
			$this->Api->setResponseCode(404);
			return;
		}
		
		$this->set($response);
		
		return $response;
		
	}
	
	// Default view action. Overridable/extendable from each controller.
	public function view($id = null) {
		
		$key = $this->{$this->modelClass}->primaryKey;
		
		if (empty($this->request->query[$key])) {
			$this->request->query[$key] = $id;
		}
		
		if (
			is_null($this->request->query['id']) &&
			!$this->{$this->modelClass}->isDefaultObjectEnabled()
		) {
			$this->Api->setResponseCode(400);
			return;
		}
		
		$response = $this->Api->Resource
			->forModel($this->modelClass)
			->requiringASingleResult()
			->returnsApiResponse();
		
		if (empty($response)) {
			$response_code = $this->Api->Resource->wasDeleted() ? 410 : 404;
			$this->Api->setResponseCode($response_code);
			return;
		}
		
		$this->set($response);
		
		return;
		
	}
	
	// Default add action. Overridable/extendable from each controller.
	public function add() {
		
		$result = $this->Api->Resource
			->forModel($this->modelClass)
			->withId(0)
			->withData($this->request->data)
			->save();

		if (empty($result)) {
			return false;
		}
		
		$this->request->query = array('related' => implode(',', $this->Api->InputData->related()));

		$id = $this->Api->Resource->hasId();
		
		$this->view($id);
		
		$this->Api->setResponseCode(200);
		return true;
		
	}
	
	// Default edit action. Overridable/extendable from each controller.
	public function edit($id = 0) {
		
		$key = $this->{$this->modelClass}->primaryKey;
		
		if (empty($id) && !empty($this->request->query[$key])) {
			$id = $this->request->query[$key];
		}
		
		if (empty($id)) {
			return $this->add();
		}
		
		$result = $this->Api->Resource
			->forModel($this->modelClass)
			->withId($id)
			->withData($this->request->data)
			->save();
		
		if (empty($result)) {
			return false;
		}
		
		$this->request->query['related'] = implode(',', $this->Api->InputData->related());
		
		$id = $this->Api->Resource->hasId();
		
		$this->view($id);
		
		$this->Api->setResponseCode(200);
		return true;
		
	}
	
	// Default delete action. Overridable/extendable from each controller.
	public function delete($id = 0) {
		
		$key = $this->{$this->modelClass}->primaryKey;
		
		if (empty($id) && !empty($this->request->query[$key])) {
			$id = $this->request->query[$key];
		}
		
		if (empty($id)) {
			$this->Api->setResponseCode(400);
			return false;
		}

		$result = $this->Api->Resource
			->forModel($this->modelClass)
			->withId($id)
			->delete();
		
		if (empty($result)) {
			return false;
		}
		
		$this->Api->setResponseCode(200);
		return true;
		
	}
	
	// Default count action. Overridable/extendable from each controller.
	public function count() {
		
		return $this->index();
		
	}
	
}
