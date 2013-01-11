<?php
namespace WebUiTest;

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

	function testSetupHandlerRegistersLoginHandlerWithCorrectPath() {
		$this->httpApi->expects($this->once())->method('registerHandler')->with("|^/WEBUI_MODULE/login|i");
		$this->callSetupHandler($this->ctrl);
	}

	function testSetupHandlerRegistersLoginHandlerWithCallback() {
		$this->httpApi->expects($this->once())
			->method('registerHandler')
			->with($this->anything(), $this->isCallable());
		$this->callSetupHandler($this->ctrl);
	}

	function testLoginHandlerWritesResponse() {
		$request = $this->getMock('WebUiTest\MockRequest');
		$response = $this->getMock('WebUiTest\MockResponse');
		$response->expects($this->once())->method('writeHead')->with(200);
		$response->expects($this->once())->method('end');
		$this->ctrl->handleLoginResource($request, $response);
	}
}
