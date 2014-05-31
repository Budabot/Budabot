<?php

namespace Budabot\Core;

use Logger;

class LoggerWrapper {
	private $logger;
	private $chatLogger;

	public function __construct($tag) {
		$this->logger = Logger::getLogger($tag);
		$this->chatLogger = Logger::getLogger('CHAT');
	}

	public function log($category, $message, $throwable = null) {
		$level = LegacyLogger::getLoggerLevel($category);
		$this->logger->log($level, $message, $throwable);
	}

	public function log_chat($channel, $sender, $message) {
		global $vars;
		if ($vars['show_aoml_markup'] == 0) {
			$message = preg_replace("/<font(.+)>/U", "", $message);
			$message = preg_replace("/<\/font>/U", "", $message);
			$message = preg_replace("/<a(\\s+)href=\"(.+)\">/sU", "[link]", $message);
			$message = preg_replace("/<a(\\s+)href='(.+)'>/sU", "[link]", $message);
			$message = preg_replace("/<\/a>/U", "[/link]", $message);
		}

		if ($channel == "Buddy") {
			$line = "[$channel] $sender $message";
		} else if ($sender == '-1' || $sender == '4294967295') {
			$line = "[$channel] $message";
		} else {
			$line = "[$channel] $sender: $message";
		}

		$level = LegacyLogger::getLoggerLevel('INFO');
		$this->chatLogger->log($level, $line);
	}

	public function getLoggingDirectory() {
		global $vars;
		return "./logs/{$vars['name']}.{$vars['dimension']}";
	}
	
	public function isEnabledFor($category) {
		$level = LegacyLogger::getLoggerLevel($category);
		return $this->logger->isEnabledFor($level);
	}
}

?>
