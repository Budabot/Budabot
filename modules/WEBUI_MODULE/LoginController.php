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

	/**
	 * @Setup
	 */
	public function setup() {
		$this->httpApi->registerHandler("|^/{$this->moduleName}/login|i", array($this, 'handleLoginResource'));
	}

	public function handleLoginResource($request, $response) {
		$response->writeHead(200);
		$response->end(file_get_contents(__DIR__ .'/login.html'));
	}
}
