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

	/** @Inject("WebUi\LoginController") */
	public $login;

	/** @Inject("WebUi\Template") */
	public $template;

	const LOG_EVENTS_TOPIC = 'http://localhost/logEvents';

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpApi->registerHandler("|^/{$this->moduleName}/$|i",
			array($this, 'handleRootResource'));
		$this->httpApi->registerHandler("|^/{$this->moduleName}/css/style.css|i",
			$this->handleStaticResource(__DIR__ .'/resources/css/style.css'));

		$that = $this;
		self::getBufferAppender()->onEvent(function($event) use ($that) {
			$that->httpApi->wampPublish($that::LOG_EVENTS_TOPIC, $event);
		});

		$this->httpApi->onWampSubscribe(self::LOG_EVENTS_TOPIC, function($client) use ($that) {
			$events = $that::getBufferAppender()->getEvents();
			foreach ($events as $event) {
				$client->event($that::LOG_EVENTS_TOPIC, $event);
			}
		});
	}

	public static function getBufferAppender() {
		$appender = \Logger::getRootLogger()->getAppender('appenderBuffer');
		if (!$appender) {
			$appender = new LoggerAppenderBuffer();
		}
		return $appender;
	}

	public function handleRootResource($request, $response) {
		if (!$this->login->isLoggedIn()) {
			$this->httpApi->redirectToPath($response, "/{$this->moduleName}/login");
			return;
		}

		$response->writeHead(200);
		$response->end($this->template->render('index.html', array(
			'webSocketUri' => $this->httpApi->getWebSocketUri(),
			'logEventsTopic' => self::LOG_EVENTS_TOPIC
		)));
	}

	public function handleStaticResource($path) {
		$mimeType = $this->extensionToMimeType(
			pathinfo($path, PATHINFO_EXTENSION));

		return function ($request, $response) use ($path, $mimeType) {
			$response->writeHead(200, array('Content-Type' => $mimeType));
			$response->end(file_get_contents($path));
		};
	}

	public function extensionToMimeType($extension) {
		switch (strtolower($extension)) {
			case 'css':
				return 'text/css';
		}
		return 'text/plain';
	}
}
