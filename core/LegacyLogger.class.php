<?php

namespace Budabot\Core;

use Logger;
use LoggerLevel;

class LegacyLogger {
	public static function log($category, $tag, $message) {
		$logger = Logger::getLogger($tag);
		$level = LegacyLogger::getLoggerLevel($category);
		$logger->log($level, $message);
	}

	public static function getLoggerLevel($level) {
		switch (strtolower($level)) {
			case 'trace':
				$level = LoggerLevel::getLevelTrace();
				break;
			case 'debug':
				$level = LoggerLevel::getLevelDebug();
				break;
			case 'warn':
				$level = LoggerLevel::getLevelWarn();
				break;
			case 'error':
				$level = LoggerLevel::getLevelError();
				break;
			case 'fatal':
				$level = LoggerLevel::getLevelFatal();
				break;

			case 'info':
			default:
				$level = LoggerLevel::getLevelInfo();
				break;
		}
		return $level;
	}
}
