<?php

if (preg_match("/^server$/i", $message) || preg_match("/^server (.)$/i", $message, $arr)) {
	$servernum = trim($arr[1]);
	if (!$servernum) {
		$servernum = $chatBot->vars['dimension'];
	} else if ($servernum == '3') {
		$servernum = 't';
	}

	$msg = "Getting Server status. Please standby.";
	$chatBot->send($msg, $sendto);

	$server = new server($servernum);
	if ($server->errorCode != 0) {
		$msg = $server->errorInfo;
	} else {
		$link  = "<header>::::: $server->name Server Status :::::<end>\n\n";

		if ($server->servermanager == 1) {
			$link .= "<highlight>Servermanager<end> is <green>UP<end>\n";
		} else {
			$link .= "<highlight>Servermanager<end> is <red>DOWN<end>\n";
		}
			
		if ($server->clientmanager == 1) {
			$link .= "<highlight>Clientmanager<end> is <green>UP<end>\n";
		} else {
			$link .= "<highlight>Clientmanager<end> is <red>DOWN<end>\n";
		}

		if ($server->chatserver == 1) {
			$link .= "<highlight>Chatserver<end> is <green>UP<end>\n\n";
		} else {
			$link .= "<highlight>Chatserver<end> is <red>DOWN<end>\n\n";
		}

		$link .= "<highlight>Player distribution in % of total players online.<end>\n";
		ksort($server->data);
		forEach ($server->data as $zone => $proz) {
			$link .= "<highlight>$zone<end>: {$proz["players"]} \n";
		}
		
		$msg = Text::make_blob("Status of $server->name", $link);	    
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>