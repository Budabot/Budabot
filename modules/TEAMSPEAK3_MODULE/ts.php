<?php

if (preg_match("/^ts$/i", $message)) {
	$ts = new Teamspeak3(Setting::get('ts_username'), Setting::get('ts_password'), Setting::get('ts_server'), Setting::get('ts_queryport'));

	try {
		$server = Setting::get('ts_server');
		$clientPort = Setting::get('ts_clientport');
		$serverLink = Text::make_chatcmd($server, "/start ts3server://$server:$clientPort");
		
		$users = $ts->exec('clientlist');
		$count = 0;
		$blob = "<header> :::::: Teamspeak 3 Info :::::: <end>\n\n";
		$blob .= "Server: $serverLink\n";
		$blob .= "Description: <highlight>" . Setting::get('ts_description') . "<end>\n\n";
		$blob .= "Users:\n";
		forEach ($users as $user) {
			if ($user['client_type'] == 0) {
				$blob .= "<highlight>{$user['client_nickname']}<end>\n";
				$count++;
			}
		}
		if ($count == 0) {
			$blob .= "<i>No users connected</i>\n";
		}
		$blob .= "\n\nTeamspeak 3 support by Tshaar (RK2)";
		$msg = Text::make_blob("Teamspeak 3 Info ($count users)", $blob);
	} catch (Exception $e) {
		$msg = "Error connecting to TS3 server.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>