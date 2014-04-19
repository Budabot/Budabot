<?php

namespace Budabot\User\Modules\WebUi;

use LoggerAppenderBuffer;
use Budabot\Core\CommandReply;

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
	public $httpServerController;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $setting;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $accessManager;

	/** @Inject */
	public $loginController;
	
	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $template;
	
	/** @Inject */
	public $text;
	
	/** @Logger */
	public $logger;

	const LOG_EVENTS_TOPIC = 'http://localhost/logEvents';

	/**
	 * @Setup
	 */
	public function setup() {

		$this->settingManager->add(
			$this->moduleName,
			'log_console_access_level',
			'Minimum access level required for access to the log console',
			'edit',
			'options',
			'superadmin',
			'superadmin;administrator;moderator;raidleader;guild;member;all',
			'',
			'superadmin'
		);

		$this->httpServerController->registerHandler("|^/{$this->moduleName}/$|i",
			array($this, 'handleRootResource'));

		$this->httpServerController->registerHandler("|^/{$this->moduleName}/wsendpoint$|i",
			array($this, 'handleWsResource'));
			
		$this->httpServerController->registerHandler("|^/{$this->moduleName}/command|i",
			array($this, 'handleCommandResource'));

		$this->onLogEventsPublishToClients();
		$this->onSubscribeSendLogEvents();
	}

	private function onSubscribeSendLogEvents() {
		$that = $this;
		$this->httpServerController->onWampSubscribe(self::LOG_EVENTS_TOPIC, function ($connection, $topic) use ($that) {
			if ($that->hasAccessToLogConsole($connection->session)) {
				$events = $that::getBufferAppender()->getEvents();
				foreach ($events as $event) {
					$connection->event($that::LOG_EVENTS_TOPIC, $event);
				}
			} else {
				$topic->remove($connection);
			}
		});
	}

	private function onLogEventsPublishToClients() {
		$that = $this;
		self::getBufferAppender()->onEvent(function ($event) use ($that) {
			$that->httpServerController->wampPublish($that::LOG_EVENTS_TOPIC, $event);
		});
	}

	public static function getBufferAppender() {
		$appender = \Logger::getRootLogger()->getAppender('appenderBuffer');
		if (!$appender) {
			$appender = new LoggerAppenderBuffer();
			$appender->setLogLimit(2000);
		}
		return $appender;
	}

	public function handleRootResource($request, $response, $body, $session) {
		if ($this->loginController->isLoggedIn($session)) {
			$response->writeHead(200, array('Content-type' => 'text/html; charset=utf-8'));
			$response->end($this->template->render('index.html', $session, array(
				'webSocketUri' => $this->httpServerController->getWebSocketUri("/{$this->moduleName}/wsendpoint"),
				'logEventsTopic' => self::LOG_EVENTS_TOPIC,
				'logConsoleAllowed' => $this->hasAccessToLogConsole($session)
			)));
		} else {
			$this->httpServerController->redirectToPath($response, "/{$this->moduleName}/login");
		}
	}

	public function hasAccessToLogConsole($session) {
		$user = $session->getData('user');
		return $this->loginController->isLoggedIn($session) &&
			$this->accessManager->checkAccess($user, $this->setting->log_console_access_level);
	}

	public function handleWsResource($request, $response, $body, $session) {
		if ($request->isWebSocketHandshake()) {
			if ($this->loginController->isLoggedIn($session)) {
				$this->httpServerController->upgradeToWebSocket($request, $response, $session);
			} else {
				$response->writeHead(403);
				$response->end();
			}
		}
	}
	
	public function handleCommandResource($request, $response, $data, $session) {
		if ($this->loginController->isLoggedIn($session)) {
			$params = array();
			parse_str($data, $params);
			$cmd = $params['cmd'];
			$commandOutput = "";
			if (!empty($cmd)) {
				$sendto = new WebCommandReply();
				$sender = $session->getData('user');
				
				$this->logger->log_chat("Inc. Web Console", $sender, $cmd);
				
				$this->commandManager->process("msg", $cmd, $sender, $sendto);
				$commandOutput = $sendto->getMsg();
				
				if (is_array($commandOutput)) {
					$commandOutput = implode("\n\n", $commandOutput);
				}
				
				$this->logger->log_chat("Out. Web Console", $sender, $commandOutput);
			}
			$response->writeHead(200, array('Content-type' => 'text/html; charset=utf-8'));
			$response->end($this->convertAOMLToHTML($commandOutput));
		} else {
			$this->httpServerController->redirectToPath($response, "/{$this->moduleName}/login");
		}
	}
	
	private function convertAOMLToHTML($input) {
		$input = $this->text->format_message($input);
		$input = preg_replace("/<a href=\"text:\\/\\/(.+)\">(.+)<\\/a>/sU", "$1", $input);
		$input = preg_replace_callback("/<a(\\s+)href='chatcmd:\\/\\/(.+)'>(.+)<\\/a>/sU", array($this, 'replaceChatCmd'), $input);
		$input = preg_replace_callback("/<img(\\s+)src=(.+)>/sU", array($this, 'replaceImages'), $input);
		return $input;
	}
	
	private function replaceChatCmd($arr) {
		$botname = $this->chatBot->vars['name'];
		if (preg_match("/\\/tell $botname (.+)/i", $arr[2], $matches)) {
			return "<a href=\"#\" onclick=\"$('#commandInput').val('$matches[1]'); sendCommand();\" title=\"$matches[1]\">$arr[3]</a>";
		} else {
			return $arr[3];
		}
	}
	
	private function replaceImages($arr) {
		if (preg_match("|'rdb://(\\d+)'|", $arr[2], $matches)) {
			return "<img src='http://s2.aoitems.com/icon/{$matches[1]}' />";
		} else {
			return '';
		}
	}
}

class WebCommandReply implements CommandReply {
	private $msg;

	public function reply($msg) {
		$this->msg = $msg;
	}
	
	public function getMsg() {
		return $this->msg;
	}
}
