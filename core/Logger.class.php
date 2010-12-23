<?php

global $vars;

// make sure logging directory exists
@mkdir("./logs/{$vars['name']}.{$vars['dimension']}", 0777, true);

// category is one of:
//   debug
//   query
//   info
//   chat
//   error

class Logger {
	public static function log($category, $tag, $message) {
		$timestamp = date("Ymd H:i");
		$category = strtoupper($category);

		$line = str_pad($timestamp, 14) . ' ' .  str_pad($category, 5) . ' ' . "[$tag]" . ' ' . $message;

		echo "$line\n";
		Logger::append_to_log_file($category, $line);

		/*
			00:00 DEBUG [/modules/TOWER_MODULE/towers.php] [timer check]
			00:00 INFO  [/modules/TOWER_MODULE/towers.php] [tower site added]
			00:00 WARN  [/modules/TOWER_MODULE/towers.php] [could not connect to twinknet]
			00:00 ERROR [/modules/TOWER_MODULE/towers.php] [sql error]
			
			201008.DEBUG.txt
			201008.INFO.txt
			201008.ERROR.txt
		*/
	}
	
/*===============================
** Name: log
** Record incoming info into the chatbot's log.
*/	public static function log_chat($channel, $sender, $message) {
		$timestamp = date("Ymd H:i");
		
		$message = preg_replace("/<font(.+)>/U", "", $message);
        $message = preg_replace("/<\/font>/U", "", $message);
        $message = preg_replace("/<a(\\s+)href=\"(.+)\">/sU", "[link]", $message);
        $message = preg_replace("/<a(\\s+)href='(.+)'>/sU", "[link]", $message);
        $message = preg_replace("/<\/a>/U", "[/link]", $message);

		if ($channel == "Buddy") {
			$line = "$timestamp INFO  [$channel] $sender $message";
		} else if ($sender == -1) {
			$line = "$timestamp INFO  [$channel] $message";
		} else {
			$line = "$timestamp INFO  [$channel] $sender: $message";
		}

		echo "$line\n";

		Logger::append_to_log_file('chat', $line);
	}

	private static function append_to_log_file($channel, $line) {
		global $vars;

		$today =  date("Ym");

		// Open and append to log-file. Complain on failure.
        $filename = "./logs/{$vars['name']}.{$vars['dimension']}/$today.$channel.log";
        if (($fp = fopen($filename, "a")) === FALSE) {
            echo "    *** Failed to open log-file $filename for writing ***\n";
        } else {
            fwrite($fp, $line . PHP_EOL);
            fclose($fp);
        }
	}
}

?>
