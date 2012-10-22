<?php

class IRC {
	public static function isConnectionActive(&$socket) {
		if ($socket == null) {
			return false;
		}

		$array = socket_get_status($socket);
		if (empty($array) || $array['eof'] == '1') {
			return false;
		} else {
			return true;
		}
	}

	public static function connect(&$socket, $nick, $server, $port, $password, $channel) {
		LegacyLogger::log('INFO', "IRC", "Intializing IRC connection");

		$socket = fsockopen($server, $port);

		fputs($socket,"USER $nick $nick $nick $nick :$nick\n");
		fputs($socket,"NICK $nick\n");
		$logincount = 0;
		while ($logincount < 10) {
			$logincount++;
			$data = trim(fgets($socket, 128));
			LegacyLogger::log('DEBUG', "IRC", $data);

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

		while ($data = trim(fgets($socket))) {
			LegacyLogger::log('DEBUG', "IRC", $data);
			if (preg_match("/(ERROR)(.+)/", $data, $sandbox)) {
				LegacyLogger::log('ERROR', "IRC", $data);
			}
			if ($ex[0] == "PING") {
				fputs($socket, "PONG ".$ex[1]."\n");
			}
			if (preg_match("/(End of \/NAMES list)/", $data, $discard)) {
				break;
			}
		}
		stream_set_blocking($socket, 0);
		LegacyLogger::log('INFO', "IRC", "Finished connecting to IRC");
	}

	public static function getUsersInChannel(&$socket, $channel) {
		stream_set_blocking($socket, 1);
		fputs($socket, "NAMES :".$channel."\n");

		$data = trim(fgets($socket));
		stream_set_blocking($socket, 0);

		$names = array();

		if (!preg_match("/(End of \/NAMES list)/", $data)) {
			$users = substr($data, strrpos($data,":")+1);
			$names = explode(' ',trim(str_replace(array("@", "+", "%"), "", $users)));
		}

		return $names;
	}

	public static function disconnect(&$socket) {
		fclose($socket);
	}

	public static function send(&$socket, $channel, $message) {
		fputs($socket, "PRIVMSG ".$channel. " :" . $message . "\n");
	}
}

?>
