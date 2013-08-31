<?php
App::uses('Component', 'Controller');

/**
 * Api Component
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
class ApiComponent extends Component {
	
	/**
	 * Default Status Code
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_status_code = 200;
	
	/**
	 * Default Code
	 *
	 * @since   1.0
	 * @var     string
	 */
	protected $_code = 2000;
	
	/**
	 * Default Speical View Vars
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_special_view_vars = array(
		'status',
		'code',
		'developerMessage',
		'systemMessage',
		'userMessage'
	);
	
	/**
	 * Return Special View Vars
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_return_special_view_vars = array();
	
	/**
	 * Exempt Actions
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_exempt_actions = array();
	
	/**
	 * Action Errors
	 *
	 * @since   1.0
	 * @var     array
	 */
	protected $_action_errors = array(
		'add' => 'Error saving data',
		'delete' => 'Error deleting data',
		'edit' => 'Error saving data',
		'index' => 'Error listing data',
		'view' => 'Error viewing data'
	);
	
	/**
	 * Components
	 *
	 * @since   1.0
	 * @var     array
	 */
	public $components = array(
		'ApiRequestHandler' => array(
			'className' => 'Api.ApiRequestHandler'
		),
		'ApiPaginator' => array(
			'className' => 'Api.ApiPaginator'
		),		
		'Query' => array(
			'className' => 'Api.ApiQuery'
		),
		'InputData' => array(
			'className' => 'Api.ApiInputData'
		),
		'Permissions' => array(
			'className' => 'Api.ApiPermissions',
		),
		'Resource' => array(
			'className' => 'Api.ApiResource'
		)
	);
	
	/**
	 * JSON encode a CSV response
	 *
	 * CSVs are flattened EXCEPT for values which have PURELY NUMERIC keys. 
	 * Those values are JSON encoded.
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$array
	 * @return  array
	 */
	private function __jsonEncodeCsvResponse($array = array()) {
		
		if (empty($array)) {
			return $array;
		}
		
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if (Hash::numeric(array_keys($value))) {
					$array[$key] = json_encode($value);
				} else {
					$array[$key] = $this->__jsonEncodeCsvResponse($value);
				}
			} else {
				$array[$key] = $value;
			}
		}
		
		return $array;
		
	}
	
	/**
	 * Constructor
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function __construct(ComponentCollection $Collection, $settings = array()) {
		
		parent::__construct($Collection, $settings);

		$this->Controller = $Collection->getController();

		if (!empty($settings['permissionsBehavior'])) {
			$this->Permissions->withPermissionsBehavior($settings['permissionsBehavior']);
		}
		
	}
	
	/**
	 * Startup
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com
	 * @since   1.0
	 * @param   Controller	$controller 
	 * @return  boolean
	 */
	public function startup(Controller $Controller) {
		
		if (
			$Controller->name === 'AppError' ||
			in_array($Controller->action, $this->exemptActions())
		) {
			return true;
		}
		
		$this->Query
			->onModel($this->Controller->modelClass)
			->prepare();
			
		$this->InputData
			->forModel($this->Controller->modelClass)
			->prepare();
		
		return true;
	
	}

	/**
	 * Actions Which Are Exempt from Api Processing
	 *
	 * @author	Anthony Putignano <anthony@wizehive.com
	 * @since   1.0
	 * @param   array	$actions
	 * @return  array
	 */
	public function exemptActions($actions = null) {
		
		if (!is_null($actions)) {
			
			if (is_string($actions)) {
				$actions = array($actions);
			}
			
			$this->_exempt_actions = $actions;
			
		}
		
		return $this->_exempt_actions;
		
	}
	
	/**
	 * Response Code
	 *
	 * @since   1.0
	 * @param   string $code Response code.
	 * @return  string Returns response code.
	 */
	public function code($code = null) {
		
		if (!empty($code)) {
			$this->_code = $code;
		}
		
		return $this->_code;
		
	}
	
	/**
	 * Response Status Code
	 *
	 * @since   1.0
	 * @param   string $status_code Response status code. 
	 * @return  string
	 */
	public function statusCode($status_code = null) {
		
		if (!empty($status_code)) {
			$this->_status_code = $status_code;
		}
		
		return $this->_status_code;
		
	}
	
	/**
	 * Action Errors
	 *
	 * @since   1.0
	 * @param   array $errors Errors array.
	 * @return  array
	 */
	public function actionErrors($errors = null) {
		
		if (!empty($errors)) {
			
			if (is_string($errors)) {
				if (array_key_exists($errors, $this->_action_errors)) {
					return $this->_action_errors[$errors];
				} else {
					return '';
				}
			}

			if (is_array($errors)) {
				foreach ($errors as $action => $error) {
					$this->_action_errors[$action] = $error;
				}
			}
			
		}
		
		return $this->_action_errors;
		
	}
	
	/**
	 * Set Response Code
	 *
	 * @since   1.0
	 * @param   string $code App code.
	 * @return  boolean
	 */
	public function setResponseCode($code = 0) {
		
		if (!isset($this->ApiResponseCode)) {
			$this->ApiResponseCode = ClassRegistry::init('Api.ApiResponseCode');
		}
		
		$response_code = $this->ApiResponseCode->findById($code);
		
		if (empty($response_code)) {
			return false;
		}
		
		$status = $response_code['ApiResponseCode']['httpCode'];

		if ($code >= 4000) {
			$userMessage = $this->actionErrors($this->Controller->request->action);
		}
		
		$developerMessage = $response_code['ApiResponseCode']['message'];
		
		$this->specialViewVars(compact(
			'code',
			'status',
			'userMessage',
			'developerMessage'
		));
		
		return true;
		
	}

	/**
	 * Has Response Code
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	mixed
	 */
	public function hasResponseCode() {
		
		$special_view_vars = $this->specialViewVars();
		
		if (!empty($special_view_vars['code'])) {
			return $special_view_vars['code'];
		}
		
		return false;
		
	}
	
	/**
	 * Is Single Action
	 * 
	 * @author	Wes DeMoney <wes@wizehive.com>
	 * @since	1.0
	 * @return	boolean
	 */
	public function isSingleAction() {
		
		$singleActions = array('add', 'edit');
		
		return in_array($this->Controller->request->params['action'], $singleActions);
		
	}
	
	/**
	 * Get/Set Special View Vars to Return
	 * 
	 * @author	Anthony Putignano <anthony@wizehive.com>
	 * @since	1.0
	 * @param	array	$vars
	 * @return	array
	 */
	public function specialViewVars($vars = null) {
		
		if (!is_null($vars)) {
			$this->_return_special_view_vars = array_merge(
				$this->_return_special_view_vars,
				$vars
			);
		}
		
		return $this->_return_special_view_vars;
		
	}
	
	/**
	 * Parse View Vars
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function parseViewVars() {
		
		$special_view_vars = $this->specialViewVars();
		
		if (!empty($special_view_vars)) {
			
			foreach ($this->_special_view_vars as $var) {
				
				if (array_key_exists($var, $special_view_vars)) {
					${$var} = $special_view_vars[$var];
				}
				
			}
			
		}
		
		if (!empty($status)) {
			$this->statusCode($status);
		}
		
		if (!empty($code)) {
			$this->code($code);
		}
		
		$this->Controller->response->statusCode($this->statusCode());
		
		$validationErrors = $this->Resource->hasValidationErrors();
		
		$default_view_vars = array(
			'status' => $this->statusCode(),
			'code' => $this->code()
		);
		
		$data = array(
			'status' => $this->statusCode(),
			'code' => $this->code()
		);
		
		if (!empty($userMessage)) {
			$data['userMessage'] = $userMessage;
		}
		
		if (!empty($developerMessage)) {
			$data['developerMessage'] = $developerMessage;
		}
		
		if (!empty($systemMessage)) {
			$data['systemMessage'] = $systemMessage;
		}
		
		if (!empty($validationErrors)) {
			
			if ($this->isSingleAction()) {
				$validationErrors = array_shift($validationErrors);
			}
		
			$data['validationErrors'] = $validationErrors;
		}

		if (in_array($this->Controller->action, array('index', 'view', 'count'))) {
			$data['totalCount'] = 0;
		}
		
		if ($this->Controller->action === 'count') {
			
			if (!empty($this->Controller->viewVars['totalCount'])) {
				$data['totalCount'] = $this->Controller->viewVars['totalCount'];
			}
			
		} else {
		
			if (!empty($this->Controller->request->params['paging'][$this->Controller->modelClass]['count'])) {
				$data['totalCount'] = $this->Controller->request->params['paging'][$this->Controller->modelClass]['count'];
			}
			
			if (isset($this->Controller->request->params['paging'][$this->Controller->modelClass]['limit'])) {
				$data['limit'] = $this->Controller->request->params['paging'][$this->Controller->modelClass]['limit'];
				if ($data['limit'] === PHP_INT_MAX) {
					$data['limit'] = $data['totalCount'];
				}
			}
			
			if (isset($this->Controller->request->params['paging'][$this->Controller->modelClass]['page'])) {
				$data['offset'] = $this->Controller->request->params['paging'][$this->Controller->modelClass]['page'] - 1;
			}
			
			if (!empty($this->Controller->viewVars)) {
				
				$data['data'] = $this->Controller->viewVars;
				
			}
		
		}	
		
		$this->Controller->viewVars = array(
			'data' => $data,
			'_serialize' => 'data'
		);
		
		return;
		
	}
	
	/**
	 * Format CSV Response
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function formatCsvResponse() {
		
		$this->Controller->viewClass = 'Csv';
		
		if (!Hash::numeric(array_keys($this->Controller->viewVars))) {
			$this->Controller->viewVars = array($this->Controller->viewVars);
		}
		
		foreach ($this->Controller->viewVars as $key => $val) {
			$this->Controller->viewVars[$key] = Hash::flatten($this->__jsonEncodeCsvResponse($val));
		}
		
		$this->Controller->viewVars = array(
			'data' => $this->Controller->viewVars,
			'_header' => array_keys($this->Controller->viewVars[0]),
			'_serialize' => 'data'
		);
		
		return;
		
	}
	
	/**
	 * Format XML Response
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function formatXmlResponse() {
		
		$this->Controller->viewVars = array(
			'data' => array(
				'response' => $this->Controller->viewVars['data']
			),
			'_serialize' => 'data'
		);
		
		return;
		
	}
	
	/**
	 * Format Json Response
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function formatJsonResponse() {
		
		$this->Controller->viewClass = 'Json';
		$this->setJsonPrettyPrint();
		$this->formatJsonpResponse();
		
		return;
		
	}
	
	/**
	 * Set Json Pretty Print
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function setJsonPrettyPrint() {
		
		if ($this->Controller->request->query('pretty')) {
			Configure::write('jsonPretty', true);
		}
		
		return;
		
	}
	
	/**
	 * Format Jsonp Response
	 *
	 * @since   1.0
	 * @return  void
	 */
	public function formatJsonpResponse() {
		
		// If a callback function is requested, pass the callback name to the controller
		// responds if following query parameters present: jsoncallback, callback
		$callback = false;
		
		$json_callback_keys = array('jsoncallback', 'callback');
		
		foreach ($json_callback_keys as $key) {
			
			if (array_key_exists($key, $this->Controller->request->query)) {
				$callback = $this->Controller->request->query[$key];
			}
			
		}
		
		if ($callback) {
			
			if (preg_match('/\W/', $callback)) {
				return $this->Controller->_abort(
					'Prevented request. Your callback is vulnerable to XSS attacks.'
				);
			}
			
			$this->Controller->set('callbackFunc', $callback);
		}
		
		return;
		
	}
	
}
