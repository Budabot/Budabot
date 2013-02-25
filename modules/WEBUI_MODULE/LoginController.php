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

	/** @Inject("WebUi\Template") */
	public $template;

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
	}

	public function isLoggedIn($session) {
		$session->start();
		return $session->getData('logged in');
	}

	public function handleLoginResource($request, $response, $body, $session) {
		if ($this->isLoggedIn($session)) {
			$this->httpApi->redirectToPath($response, "/{$this->moduleName}/");
		} else {
			$response->writeHead(200, array('Content-type' => 'text/html; charset=utf-8'));
			$response->end($this->template->render('login.html', $session));
		}
	}

	public function handleDoLoginResource($request, $response, $data, $session) {
		$isValid = false;
		list($user, $pass) = self::parseCredentialsFromQuery($data);
		if ($user && $pass) {
			$isValid = $this->checkCredentials($user, $pass);
		}

		if ($isValid) {
			$session->start();
			$session->setData('logged in', true);
			$session->setData('user', $user);
		}

		$response->writeHead(200);
		$response->end($isValid? '1': '0');
	}

	public function handleLogoutResource($request, $response, $data, $session) {
		$session->start();
		$session->setData('logged in', false);
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
