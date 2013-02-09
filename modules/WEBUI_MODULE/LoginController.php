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

	/** @Inject("WebUi\RootController") */
	public $root;

	private $loggedIn = false;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpApi->registerHandler("|^/{$this->moduleName}/login|i",
			array($this, 'handleLoginResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/do_login|i",
			array($this, 'handleDoLoginResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/logout|i",
			array($this, 'handleLogoutResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/js/login.js|i",
			$this->root->handleStaticResource(__DIR__ .'/resources/js/login.js'));
	}

	public function isLoggedIn() {
		return $this->loggedIn;
	}

	public function handleLoginResource($request, $response) {
		if ($this->isLoggedIn()) {
			$this->httpApi->redirectToPath($response, "/{$this->moduleName}/");
		} else {
			$response->writeHead(200);
			$response->end($this->root->renderTemplate('login.html'));
		}
	}

	public function handleDoLoginResource($request, $response, $data) {
		$isValid = false;
		list($user, $pass) = self::parseCredentialsFromQuery($data);
		if ($user && $pass) {
			$isValid = $this->checkCredentials($user, $pass);
		}

		$this->loggedIn = $isValid;

		$response->writeHead(200);
		$response->end($isValid? '1': '0');
	}

	public function handleLogoutResource($request, $response) {
		$this->loggedIn = false;
		$this->httpApi->redirectToPath($response, "/{$this->moduleName}/login");
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
}
