<?php

class LegacyLogger {
	public static $TIMESTAMP_FORMAT = "Ymd H:i:s";

	public static function get_logging_directory() {
		$chatBot = Registry::getInstance('chatBot');

		return "./logs/{$chatBot->vars['name']}.{$chatBot->vars['dimension']}";
	}

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

/*===============================
** Name: log
** Record incoming info into the chatbot's log.
*/	public static function log_chat($channel, $sender, $message) {

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
		} else if ($sender == '-1') {
			$line = "[$channel] $message";
		} else {
			$line = "[$channel] $sender: $message";
		}

		LegacyLogger::log('INFO', 'CHAT', $line);
	}
}

?>
