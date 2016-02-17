<?php

namespace Budabot\User\Modules\WebUi;

/**
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'apipassword',
 *		accessLevel = 'all',
 *		description = 'Set your api password',
 *		help        = 'apipassword.txt'
 *	)
 */
class LoginController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $httpServerController;

	/** @Inject */
	public $preferences;

	/** @Inject */
	public $template;
	
	/** @Logger */
	public $logger;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/login|i",
			array($this, 'handleLoginResource'));
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/do_login|i",
			array($this, 'handleDoLoginResource'));
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/logout|i",
			array($this, 'handleLogoutResource'));
	}

	public function isLoggedIn($session) {
		$session->start();
		return $session->getData('logged in');
	}

	public function handleLoginResource($request, $response, $body, $session) {
		if ($this->isLoggedIn($session)) {
			$this->httpServerController->redirectToPath($response, "/{$this->moduleName}/");
		} else {
			$response->writeHead(200, array('Content-type' => 'text/html; charset=utf-8'));
			$response->end($this->template->render('login.html', $session));
		}
	}

	public function handleDoLoginResource($request, $response, $data, $session) {
		$isValid = false;
		list($user, $pass) = self::parseCredentialsFromQuery($data);
		$user = ucfirst(strtolower($user));
		if ($user && $pass) {
			$isValid = $this->checkCredentials($user, $pass);
		}

		if ($isValid) {
			$session->start();
			$session->setData('logged in', true);
			$session->setData('user', $user);
			$this->logger->log('INFO', "$user logged in to web console");
		} else {
			$this->logger->log('DEBUG', "$user failed authentication while attempting to log in to web console");
		}

		$response->writeHead(200);
		$response->end($isValid? '1': '0');
	}

	public function handleLogoutResource($request, $response, $data, $session) {
		$session->start();
		$session->setData('logged in', false);
		$this->httpServerController->redirectToPath($response, "/{$this->moduleName}/login");
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

	/**
	 * This command handler either sets or clears character's API password.
	 *
	 * @HandlesCommand("apipassword")
	 * @Matches("/^apipassword (.*)$/i")
	*/
	public function apipasswordCommand($message, $channel, $sender, $sendto, $arr) {
		if ($arr[1] == 'clear') {
			$this->preferences->save($sender, 'apipassword', '');
			$sendto->reply("Your API password has been cleared successfully.");
		} else {
			$this->preferences->save($sender, 'apipassword', $arr[1]);
			$sendto->reply("Your API password has been updated successfully.");
		}
	}
}
