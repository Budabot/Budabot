<?php
namespace WebUiTest;

require_once 'Phake.php';
require_once "PHPUnit/Autoload.php";
require_once "tests/helpers/BudabotTestCase.php";
require_once "modules/WEBUI_MODULE/LoginController.php";
require_once "core/HTTPAPI_MODULE/HttpApiController.class.php";

interface MockRequest {
}

interface MockResponse {
	function writeHead();
	function end();
}

class LoginControllerTest extends \BudabotTestCase {

	private $ctrl;

	function setUp() {
		$this->ctrl = new \WebUi\LoginController();
		$this->ctrl->moduleName = 'WEBUI_MODULE';
		$this->httpApi = $this->injectMock($this->ctrl, 'httpapi', 'HttpApiController');
	}

	function testIsAutoInstanced() {
		$this->assertTrue($this->isAutoInstanced($this->ctrl));
	}

	function testHasSetupHandler() {
		$this->assertTrue($this->hasSetupHandler($this->ctrl));
	}

	function testHasHttpApiInject() {
		$this->assertTrue($this->hasInjection($this->ctrl, 'httpapi'));
	}

	function testSetupHandlerRegistersLoginResource() {
		$this->callSetupHandler($this->ctrl);
		\Phake::verify($this->httpApi)->registerHandler("|^/WEBUI_MODULE/login|i", $this->isCallable());
	}

	function testLoginHandlerWritesLoginHtmlResource() {
		list($request, $response) = $this->getHandlerMocks();
		$this->callSetupHandler($this->ctrl);
		$this->callHandlerCallback("|^/WEBUI_MODULE/login|i", $request, $response);

		\Phake::verify($response)->writeHead(200);
		\Phake::verify($response)->end(file_get_contents("modules/WEBUI_MODULE/resources/login.html"));
	}

	private function getHandlerMocks() {
		return array(
			\Phake::mock('WebUiTest\MockRequest'),
			\Phake::mock('WebUiTest\MockResponse')
		);
	}

	private function callHandlerCallback($path, $request, $response) {
		$callback = null;
		\Phake::verify($this->httpApi)->registerHandler($path, \Phake::capture($callback));
		call_user_func($callback, $request, $response);
	}

	function testSetupHandlerRegistersLoginJsResource() {
		$this->callSetupHandler($this->ctrl);
		\Phake::verify($this->httpApi)->registerHandler("|^/WEBUI_MODULE/js/login.js|i", $this->isCallable());
	}

	function testLoginJsHandlerWritesLoginJsResource() {
		list($request, $response) = $this->getHandlerMocks();
		$this->callSetupHandler($this->ctrl);
		$this->callHandlerCallback("|^/WEBUI_MODULE/js/login.js|i", $request, $response);

		\Phake::verify($response)->writeHead(200);
		\Phake::verify($response)->end(file_get_contents("modules/WEBUI_MODULE/resources/js/login.js"));
	}
}
