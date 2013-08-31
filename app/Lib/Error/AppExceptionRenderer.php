<?php
App::uses('ExceptionRenderer', 'Error');
App::uses('Controller', 'Controller');
App::uses('AppErrorController', 'Controller');

/**
 * Handle errors gracefully and output them in a friendly manner to the API response
 */

class AppExceptionRenderer extends ExceptionRenderer {
	
	protected function _getController($exception) {

		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}
		
		$response = new CakeResponse();

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}

		try {
			$controller = new AppErrorController($request, $response);
			$controller->startupProcess();
		} catch (Exception $e) {
			if (!empty($controller) && $controller->Components->enabled('RequestHandler')) {
				$controller->RequestHandler->startup($controller);
			}
		}
		if (empty($controller)) {
			$controller = new Controller($request, $response);
			$controller->viewPath = 'Errors';
		}
		
		return $controller;
	}
	
	/**
	 * Render
	 *
	 * @since 	1.0
	 * @return 	void
	 */
    public function render() {
		
		$code = $this->error->getCode();
		if ($code >= 506 || !is_integer($code)) {
			$code = 500;
		}
		$response = $this->controller->response->httpCodes($code);
		$message = is_array($response) ? array_shift($response) : null;
		
		$status = $code;
		$userMessage = $message;
		$developerMessage = $message;
		
		$this->controller->Api->specialViewVars(compact(
			'code',
			'status',
			'userMessage',
			'developerMessage'
		));
		
		/**
		 * For development only... 
		 */
		
			if (Configure::read('debug') > 0) {
				$systemMessage = array($this->error->getMessage());
				if (empty($this->controller->name)) {
					$systemMessage[] = 'AppErrorController could not boot up';
				}
				$this->controller->Api->specialViewVars(compact('systemMessage'));
			}

			if (
				Configure::read('debug') > 0 &&
				empty($this->controller->name)
			) {
				$this->controller->response->type('json');
				$this->controller->response->body(json_encode($this->controller->viewVars));
			}
		
		if (!empty($this->controller->name)) {
		
			$this->controller->viewClass = 'Json';

			$this->controller->render();
			$this->controller->afterFilter();
		
		}
		
		$this->controller->response->send();
		
	}
	
}