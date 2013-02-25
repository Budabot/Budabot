<?php
/**
 * @Instance
 */
class StaticController {

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
		$this->registerStaticResource("|^/{$this->moduleName}/css/style.css|i",
			__DIR__ .'/resources/css/style.css');
		$this->registerStaticResource("|^/{$this->moduleName}/js/jquery.pubsub.min.js|i",
			__DIR__ .'/resources/js/jquery.pubsub.min.js');
		$this->registerStaticResource("|^/{$this->moduleName}/js/logconsole.js|i",
			__DIR__ .'/resources/js/logconsole.js');
		$this->registerStaticResource("|^/{$this->moduleName}/js/wampconnection.js|i",
			__DIR__ .'/resources/js/wampconnection.js');
		$this->registerStaticResource("|^/{$this->moduleName}/js/login.js|i",
			__DIR__ .'/resources/js/login.js');
	}

	private function registerStaticResource($uriPath, $filePath) {
		$this->httpApi->registerHandler($uriPath, $this->handleStaticResource($filePath));
	}

	private function handleStaticResource($path) {
		$type = $this->extensionToContentType(
			pathinfo($path, PATHINFO_EXTENSION));

		return function ($request, $response) use ($path, $type) {
			$response->writeHead(200, array('Content-Type' => $type));
			$response->end(file_get_contents($path));
		};
	}

	private function extensionToContentType($extension) {
		switch (strtolower($extension)) {
			case 'css':
				return 'text/css; charset=utf-8';
			case 'js':
				return 'text/javascript; charset=utf-8';
		}
		return 'text/plain; charset=utf-8';
	}
}
