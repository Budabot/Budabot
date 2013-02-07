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

		$appender = \Logger::getRootLogger()->getAppender('appenderBuffer');
		if ($appender) {
			$appender->onEvent(function($event) {
				//print "BUFFER: $event\n";
			});
		}
	}

	public function handleRootResource($request, $response) {
		$response->writeHead(200);
		$response->end($this->renderTemplate('index.html'));
	}

	public function handleStaticResource($path) {
		return function ($request, $response) use ($path) {
			$response->writeHead(200);
			$response->end(file_get_contents($path));
		};
	}

	public function renderTemplate($name, $parameters = array()) {
		global $version;
		$parameters = array_merge(array(
			'botname' => $this->chatBot->vars['name'],
			'version' => $version
		), $parameters);
		return $this->twig->render($name, $parameters);
	}
}
