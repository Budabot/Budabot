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
		$this->httpApi->registerHandler("|^/{$this->moduleName}/js/login.js|i", array($this, 'handleLoginJsResource'));
	}

	public function handleLoginResource($request, $response) {
		$response->writeHead(200);
		$response->end(file_get_contents(__DIR__ .'/resources/login.html'));
	}

	public function handleLoginJsResource($request, $response) {
		$response->writeHead(200);
		$response->end(file_get_contents(__DIR__ .'/resources/js/login.js'));
	}
}
