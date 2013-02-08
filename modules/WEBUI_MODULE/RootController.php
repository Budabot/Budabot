<?php

namespace WebUi;

/**
 * @Instance("WebUiRootController")
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

	private $twig;

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
			$that->httpApi->wampPublish($that->logEventWampUri(), $event);
		});

		$this->httpApi->onWampSubscribe($this->logEventWampUri(), function() use ($that) {
			$events = $that::getBufferAppender()->getEvents();
			foreach ($events as $event) {
				$that->httpApi->wampPublish($that->logEventWampUri(), $event);
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
		$response->writeHead(200);
		$response->end($this->renderTemplate('index.html', array(
			'webSocketUri' => $this->httpApi->getWebSocketUri(),
			'logEventWampUri' => $this->logEventWampUri()
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
			'version' => $version
		), $parameters);
		return $this->twig->render($name, $parameters);
	}

	public function logEventWampUri() {
		return $this->httpApi->getUri('/logEvents');
	}
}
