<?php

if (preg_match("/^ts$/i", $message)) {
	$ts = new Teamspeak3(Setting::get('ts_username'), Setting::get('ts_password'), Setting::get('ts_server'), Setting::get('ts_port'));

	try {
		$users = $ts->exec('clientlist');
		$count = 0;
		$blob = "<header> :::::: Teamspeak 3 Info :::::: <end>\n\n";
		$blob .= "Users:\n";
		forEach ($users as $user) {
			$blob .= "{$user['client_nickname']}\n";
			$count++;
		}
		$msg = Text::make_blob("Teamspeak 3 Info ($count)", $blob);
	} catch (Exception $e) {
		$msg = "Error connecting to TS3 server.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>