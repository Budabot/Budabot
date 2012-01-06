<?php

// category is one of:
//   debug
//   query
//   info
//   chat
//   error

class Logger {
	public static $TIMESTAMP_FORMAT = "Ymd H:i:s";

	public static function get_logging_directory() {
		$chatBot = Registry::getInstance('chatBot');
		
		return "./logs/{$chatBot->vars['name']}.{$chatBot->vars['dimension']}";
	}

	public static function log($category, $tag, $message) {
		global $vars;
		
		if (strtolower($category) == 'warn') {
			$category = 'ERROR';
		}
		
		if ($vars[strtolower($category) . "_console"] != 1 && $vars[strtolower($category) . "_file"] != 1) {
			return;
		}

		$timestamp = date(Logger::$TIMESTAMP_FORMAT);
		$category = strtoupper($category);
		
		if ($category == "ERROR") {
			$message .= "\n" . Util::getStackTrace();
		}

		$line = str_pad($timestamp, 17) . ' ' .  str_pad($category, 5) . ' ' . "[$tag]" . ' ' . $message;

		if ($vars[strtolower($category) . "_console"]) {
			echo "$line\n";
		}
		
		if ($vars[strtolower($category) . "_file"]) {
			Logger::append_to_log_file($category, $line);
		}
	}
	
/*===============================
** Name: log
** Record incoming info into the chatbot's log.
*/	public static function log_chat($channel, $sender, $message) {
		global $vars;
		
		$timestamp = date(Logger::$TIMESTAMP_FORMAT);
		
		if ($vars['show_aoml_markup'] == 0) {
			$message = preg_replace("/<font(.+)>/U", "", $message);
			$message = preg_replace("/<\/font>/U", "", $message);
			$message = preg_replace("/<a(\\s+)href=\"(.+)\">/sU", "[link]", $message);
			$message = preg_replace("/<a(\\s+)href='(.+)'>/sU", "[link]", $message);
			$message = preg_replace("/<\/a>/U", "[/link]", $message);
		}

		if ($channel == "Buddy") {
			$line = "$timestamp INFO  [$channel] $sender $message";
		} else if ($sender == '-1') {
			$line = "$timestamp INFO  [$channel] $message";
		} else {
			$line = "$timestamp INFO  [$channel] $sender: $message";
		}

		if ($vars["chat_console"]) {
			echo "$line\n";
		}
		
		if ($vars["chat_file"]) {
			Logger::append_to_log_file('CHAT', $line);
		}
	}

	private static function append_to_log_file($channel, $line) {
		global $vars;

		$date =  date("Y-m");

		// Open and append to log-file. Complain on failure.
        $filename = "./logs/{$vars['name']}.{$vars['dimension']}/$date.log";
		//$filename = "./logs/{$vars['name']}.{$vars['dimension']}/$today.log";
        if (($fp = fopen($filename, "a")) === false) {
            echo "    *** Failed to open log-file $filename for writing ***\n";
        } else {
            fwrite($fp, $line . PHP_EOL);
            fclose($fp);
        }
	}
}

?>
