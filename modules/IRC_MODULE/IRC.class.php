<?php

class IRC {
	public static function isConnectionActive($socket) {
		$array = socket_get_status($socket);
		if (empty($array) || $array['eof'] == '1') {
			return false;
		} else {
			return true;
		}
	}
	
	public static function connect($socket, $nick, $server, $port, $password, $channel) {
		Logger::log('INFO', "IRC", "Intializing IRC connection");
		
		$socket = fsockopen($server, $port);
		
		fputs($socket,"USER $nick $nick $nick $nick :$nick\n");
		fputs($socket,"NICK $nick\n");
		while ($logincount < 10) {
			$logincount++;
			$data = fgets($socket, 128);
			Logger::log('DEBUG', "IRC", trim($data));

			$ex = explode(' ', $data);

			// Send PONG back to the server
			if ($ex[0] == "PING") {
				fputs($socket, "PONG ".$ex[1]."\n");
			}
		}

		if ($password != 'none') {
			fputs($socket,"JOIN ".$channel. ' ' . $password . "\n");
		} else {
			fputs($socket,"JOIN ".$channel."\n");
		}

		while ($data = fgets($socket)) {
			Logger::log('DEBUG', "IRC", trim($data));
			if (preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
				Logger::log('ERROR', "IRC", trim($data));
				return false;
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
		return true;
	}
	
	public static function getUsersInChannel($socket, $channel) {
		stream_set_blocking($socket, 1);
		fputs($socket, "NAMES :".$channel."\n");
		$data = fgets($socket);
		
		$names = array();
		if (!preg_match("/(End of \/NAMES list)/", $data)) {
			$start = strrpos($data,":")+1;
			$names = explode(' ',substr($data, $start, strlen($data)));
		}
		stream_set_blocking($socket, 0);
		
		return $names;
	}
	
	public static function disconnect($socket) {
		fclose($socket);
	}
	
	public static function send($socket, $channel, $message) {
		fputs($socket, "PRIVMSG ".$channel. " :" . $message . "\n");
	}
}

?>
