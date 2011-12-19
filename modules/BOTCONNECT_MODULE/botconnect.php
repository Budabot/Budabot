<?php

if (preg_match("/^botconnect$/i", $message)) {
	$data = Botconnect::getAll();
	
	if (count($data) > 0) {
		$blob = "<header> :::::: Connect List :::::: <end>\n\n";
		forEach ($data as $row) {
			$onlineStatus = Buddylist::is_online($row->name);
			$online = '';
			if ($onlineStatus == 1) {
				$online .= "<green>Online<end>";
			} else if ($onlineStatus == 0) {
				$online .= "<orange>Offline<end>";
			} else {
				$online .= "<orange>Unknown<end>";
			}
			$removeLink = Text::make_chatcmd("Remove", "/tell <myname> botconnect remove $row->name");
		
			$blob .= "{$row->name} {$online} {$removeLink}\n";
		}
		$msg = Text::make_blob("Connect List", $blob);
	} else {
		$msg = "There are no bots on the connect list.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^botconnect add (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$uid = $chatBot->get_uid($name);
    if (!$uid) {
		$msg = "Character <highlight>{$name}<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if (Botconnect::onConnectList($name)) {
		$msg = "<highlight>{$name}<end> is already on the connect list.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	Botconnect::add($name);

	$msg = "<highlight>{$name}<end> has been added to the connect list.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^botconnect (rem|remove) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));
	
	$uid = $chatBot->get_uid($name);
    if (!$uid) {
		$msg = "Character <highlight>{$name}<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if (!Botconnect::onConnectList($name)) {
		$msg = "<highlight>{$name}<end> is not on the connect list.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	Botconnect::remove($name);

	$msg = "<highlight>{$name}<end> has been removed from the connect list.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>