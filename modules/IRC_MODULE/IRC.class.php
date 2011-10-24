<?php

class IRC {
	public static function isConnectionActive() {
		global $socket;

		$array = socket_get_status($socket);
		if (empty($array) || $array['eof'] == '1') {
			return false;
		} else {
			return true;
		}
	}
	
	public static function connect() {
		global $socket;

		$nick = Setting::get('irc_nickname');
		Logger::log('INFO', "IRC", "Intializing IRC connection");
		
		$socket = fsockopen(Setting::get('irc_server'), Setting::get('irc_port'));
		
		fputs($socket,"USER $nick $nick $nick $nick :$nick\n");
		fputs($socket,"NICK $nick\n");
		while ($logincount < 10) {
			$logincount++;
			$data = fgets($socket, 128);
			if (Setting::get('irc_debug_all') == 1) {
				Logger::log('INFO', "IRC", trim($data));
			}
			// Separate all data
			$ex = explode(' ', $data);

			// Send PONG back to the server
			if ($ex[0] == "PING") {
				fputs($socket, "PONG ".$ex[1]."\n");
			}
		}

		if (Setting::get('irc_password') != 'none') {
			fputs($socket,"JOIN ".Setting::get('irc_channel'). ' ' . Setting::get('irc_password') . "\n");
		} else {
			fputs($socket,"JOIN ".Setting::get('irc_channel')."\n");
		}

		while ($data = fgets($socket)) {
			if (Setting::get('irc_debug_all') == 1) {
				Logger::log('INFO', "IRC", trim($data));
			}
			if (preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
				if (preg_match("/^startirc$/i", $message)) {
					$chatBot->send("Could not connect to IRC.", $sendto);
				}
				Logger::log('error', "IRC", trim($data));
				return;
			}
			if ($ex[0] == "PING") {
				fputs($socket, "PONG ".$ex[1]."\n");
			}
			if (preg_match("/(End of \/NAMES list)/", $data, $discard)) {
				break;
			}
		}
		stream_set_blocking($socket, 0);
		Logger::log('INFO', "IRC", "Finished connecting to IRC");
	}
	
	public static function getUsersInChannel() {
		global $socket;

		stream_set_blocking($socket, 1);
		fputs($socket, "NAMES :".Setting::get('irc_channel')."\n");
		$data = fgets($socket);
		
		$names = array();
		if (!preg_match("/(End of \/NAMES list)/", $data)) {
			$start = strrpos($data,":")+1;
			$names = explode(' ',substr($data, $start, strlen($data)));
		}
		stream_set_blocking($socket, 0);
		
		return $names;
	}
	
	public static function disconnect() {
		global $socket;
		fclose($socket);
	}
	
	public static function send($message) {
		global $socket;
		fputs($socket, "PRIVMSG ".Setting::get('irc_channel') . " :" . $message . "\n");
	}
}

?>
