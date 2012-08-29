<?php

class LoggerWrapper {
	private $logger;

	public function __construct($tag) {
		$this->logger = Logger::getLogger($tag);
	}

	public function log($category, $message, $throwable = null) {
		$level = LegacyLogger::getLoggerLevel($category);
		$this->logger->log($level, $message, $throwable);
	}

	public function log_chat($channel, $sender, $message) {
		LegacyLogger::log_chat($channel, $sender, $message);
	}

	public function get_logging_directory() {
		return LegacyLogger::get_logging_directory();
	}
}

?>