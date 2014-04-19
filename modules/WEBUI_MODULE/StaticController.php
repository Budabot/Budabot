<?php

namespace Budabot\User\Modules\WebUi;

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
	public $httpServerController;

	/** @Inject */
	public $chatBot;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/css/(.+)\\.css$|i", array($this, 'handleStaticResource'));
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/js/(.+)\\.js$|i", array($this, 'handleStaticResource'));
	}

	public function handleStaticResource($request, $response, $body, $session, $data) {
		if (preg_match("|^/{$this->moduleName}/(.+)$|i", $request->getPath(), $matches)) {
			$path = __DIR__ . "/resources/" . $matches[1];
			
			$type = $this->extensionToContentType(pathinfo($path, PATHINFO_EXTENSION));

			$response->writeHead(200, array('Content-Type' => $type));
			$response->end(file_get_contents($path));
		}
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
