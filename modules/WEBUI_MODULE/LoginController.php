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
		list($user, $pass) = self::parseCredentialsFromQuery($data);
		if ($user && $pass) {
			$isValid = $this->checkCredentials($user, $pass);
		}

		$response->writeHead(200);
		$response->end($isValid? '1': '0');
	}

	private static function parseCredentialsFromQuery($data) {
		$params = array();
		parse_str($data, $params);
		return array(
			isset($params['username'])? $params['username']: '',
			isset($params['password'])? $params['password']: ''
		);
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
