<?php

namespace WebUi;

/**
 * @Instance
 */
class LoginController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $httpApi;

	/** @Inject */
	public $preferences;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpApi->registerHandler("|^/{$this->moduleName}/login|i", array($this, 'handleLoginResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/check_login|i", array($this, 'handleCheckLoginResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/js/login.js|i", array($this, 'handleLoginJsResource'));
	}

	public function handleLoginResource($request, $response) {
		$response->writeHead(200);
		$response->end(file_get_contents(__DIR__ .'/resources/login.html'));
	}

	public function handleCheckLoginResource($request, $response, $data) {
		$isValid = false;
		$params = self::parseQueryParams($data);
		if (isset($params['username']) && isset($params['password'])) {
			$isValid = $this->checkCredentials($params['username'], $params['password']);
		}

		$response->writeHead(200);
		if ($isValid) {
			$response->end('1');
		}
	}

	private static function parseQueryParams($data) {
		$params = array();
		parse_str($data, $params);
		return $params;
	}

	private function checkCredentials($username, $password) {
		$validPassword = $this->preferences->get($username, 'apipassword');
		return $validPassword === $password;
	}

	public function handleLoginJsResource($request, $response) {
		$response->writeHead(200);
		$response->end(file_get_contents(__DIR__ .'/resources/js/login.js'));
	}
}
