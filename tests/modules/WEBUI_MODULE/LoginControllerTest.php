<?php
namespace WebUiTest;

use Budabot\User\Modules\WebUi\LoginController;

require_once 'Phake.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../../tests/helpers/BudabotTestCase.php';
require_once __DIR__ . '/../../../modules/WEBUI_MODULE/LoginController.php';
require_once __DIR__ . '/../../../core/HTTPAPI_MODULE/HttpApiController.class.php';
require_once __DIR__ . '/../../../modules/WEBUI_MODULE/Template.php';
require_once __DIR__ . '/../../../core/PREFERENCES/Preferences.class.php';

interface MockRequest {
}

interface MockResponse {
	function writeHead();
	function end();
}

class LoginControllerTest extends \BudabotTestCase {

	private $ctrl;

	function setUp() {
		$this->ctrl = new LoginController();
		$this->ctrl->moduleName = 'WEBUI_MODULE';
		$this->httpApi = $this->injectMock($this->ctrl, 'httpapi', 'Budabot\Core\Modules\HttpApiController');
		$this->preferences = $this->injectMock($this->ctrl, 'preferences', 'Budabot\Core\Preferences');
		$this->template = $this->injectMock($this->ctrl, 'template', 'Budabot\User\Modules\WebUi\Template');
		$this->session = \Phake::mock('\Session');
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

	function testHasPreferencesInject() {
		$this->assertTrue($this->hasInjection($this->ctrl, 'preferences'));
	}

	function testSetupHandlerRegistersLoginResource() {
		$this->callSetupHandler($this->ctrl);
		\Phake::verify($this->httpApi)->registerHandler("|^/WEBUI_MODULE/login|i", $this->isCallable());
	}

	function testLoginHandlerWritesLoginHtmlResource() {
		list($request, $response) = $this->getHandlerMocks();
		$this->callHandlerCallback("|^/WEBUI_MODULE/login|i", $request, $response, null, $this->session);

		\Phake::verify($this->template)->render('login.html', $this->session);
	}

	private function getHandlerMocks() {
		return array(
			\Phake::mock('WebUiTest\MockRequest'),
			\Phake::mock('WebUiTest\MockResponse')
		);
	}

	private function callHandlerCallback($path, $request, $response, $data = '', $session = null) {
		$this->callSetupHandler($this->ctrl);
		$callback = null;
		\Phake::verify($this->httpApi)->registerHandler($path, \Phake::capture($callback));
		call_user_func($callback, $request, $response, $data, $session);
	}

	function testSetupHandlerRegistersCheckLoginResource() {
		$this->callSetupHandler($this->ctrl);
		\Phake::verify($this->httpApi)->registerHandler("|^/WEBUI_MODULE/do_login|i", $this->isCallable());
	}

	function testCheckLoginHandlerWritesSuccessOnValidCredentials() {
		$this->setApiPassword('fooman', 'foopass');
		list($request, $response) = $this->getHandlerMocks();
		$this->callHandlerCallback("|^/WEBUI_MODULE/do_login|i", $request, $response, http_build_query(array(
			'username' => 'fooman',
			'password' => 'foopass'
		)), $this->session);

		\Phake::verify($response)->writeHead(200);
		\Phake::verify($response)->end('1');
	}

	private function setApiPassword($username, $password) {
		\Phake::when($this->preferences)->get($username, 'apipassword')->thenReturn($password);
	}

	function testCheckLoginHandlerDeniesAccessOnWrongPassword() {
		$this->setApiPassword('fooman', 'wrong');
		list($request, $response) = $this->getHandlerMocks();
		$this->callHandlerCallback("|^/WEBUI_MODULE/do_login|i", $request, $response, http_build_query(array(
			'username' => 'fooman',
			'password' => 'foopass'
		)), $this->session);

		\Phake::verify($response)->end('0');
	}

	function testCheckLoginHandlerDeniesAccessOnEmptyPassword() {
		$this->setApiPassword('fooman', '');
		list($request, $response) = $this->getHandlerMocks();
		$this->callHandlerCallback("|^/WEBUI_MODULE/do_login|i", $request, $response, http_build_query(array(
			'username' => 'fooman',
			'password' => ''
		)), $this->session);

		\Phake::verify($response)->end('0');
	}
}
