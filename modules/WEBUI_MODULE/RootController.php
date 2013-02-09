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

	private $twig;

	const LOG_EVENTS_TOPIC = 'http://localhost/logEvents';

	/**
	 * @Setup
	 */
	public function setup() {
		$loader = new \Twig_Loader_Filesystem( __DIR__ . '/resources/tmpl');
		$this->twig = new \Twig_Environment($loader, array());

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
		$response->end($this->renderTemplate('index.html', array(
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

	public function renderTemplate($name, $parameters = array()) {
		global $version;
		$parameters = array_merge(array(
			'botname' => $this->chatBot->vars['name'],
			'version' => $version,
			'loggedIn' => $this->login->isLoggedIn()
		), $parameters);
		return $this->twig->render($name, $parameters);
	}
}
