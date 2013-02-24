<?php

namespace WebUi;

/**
 * @Instance
 */
class RootController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $httpApi;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $accessManager;

	/** @Inject("WebUi\LoginController") */
	public $login;

	/** @Inject("WebUi\Template") */
	public $template;

	const LOG_EVENTS_TOPIC = 'http://localhost/logEvents';

	/**
	 * @Setup
	 */
	public function setup() {

		$this->settingManager->add(
			$this->moduleName,
			'log_console_access_level',
			'Minimum access level required for access to the log console',
			'edit',
			'options',
			'superadmin',
			'superadmin;admininistrator;moderator;raidleader;guild;member;all',
			'',
			'admin'
		);

		$this->httpApi->registerHandler("|^/{$this->moduleName}/$|i",
			array($this, 'handleRootResource'));

		$this->httpApi->registerHandler("|^/{$this->moduleName}/wsendpoint$|i",
			array($this, 'handleWsResource'));

		$this->onLogEventsPublishToClients();
		$this->onSubscribeSendLogEvents();
	}

	private function onSubscribeSendLogEvents() {
		$that = $this;
		$this->httpApi->onWampSubscribe(self::LOG_EVENTS_TOPIC, function ($connection, $topic) use ($that) {
			if ($that->hasAccessToLogConsole($connection->session)) {
				$events = $that::getBufferAppender()->getEvents();
				foreach ($events as $event) {
					$connection->event($that::LOG_EVENTS_TOPIC, $event);
				}
			} else {
				$topic->remove($connection);
			}
		});
	}

	private function onLogEventsPublishToClients() {
		$that = $this;
		self::getBufferAppender()->onEvent(function ($event) use ($that) {
			$that->httpApi->wampPublish($that::LOG_EVENTS_TOPIC, $event);
		});
	}

	public static function getBufferAppender() {
		$appender = \Logger::getRootLogger()->getAppender('appenderBuffer');
		if (!$appender) {
			$appender = new LoggerAppenderBuffer();
		}
		return $appender;
	}

	public function handleRootResource($request, $response, $body, $session) {
		if ($this->login->isLoggedIn($session)) {
			$response->writeHead(200, array('Content-type' => 'text/html; charset=utf-8'));
			$response->end($this->template->render('index.html', $session, array(
				'webSocketUri' => $this->httpApi->getWebSocketUri(
					"/{$this->moduleName}/wsendpoint"),
				'logEventsTopic' => self::LOG_EVENTS_TOPIC,
				'logConsoleAllowed' => $this->hasAccessToLogConsole($session)
			)));
		} else {
			$this->httpApi->redirectToPath($response, "/{$this->moduleName}/login");
		}
	}

	public function hasAccessToLogConsole($session) {
		$user = $session->getData('user');
		return $this->login->isLoggedIn($session) &&
			$this->accessManager->checkAccess($user, $this->setting->log_console_access_level);
	}

	public function handleWsResource($request, $response, $body, $session) {
		if ($request->isWebSocketHandshake()) {
			if ($this->login->isLoggedIn($session)) {
				$this->httpApi->upgradeToWebSocket($request, $response, $session);
			} else {
				$response->writeHead(403);
				$response->end();
			}
		}
	}
}
