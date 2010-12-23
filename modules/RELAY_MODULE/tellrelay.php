<?php

if (preg_match("/^tellrelay (.*)$/", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$uid = AoChat::get_uid($name);
	
	if (!$uid) {
		$msg = "Player <highlight>$name<end> does not exist.";
		bot::send($msg, $sendto);
		return;
	}
	
	$this->savesetting('relaytype', 1);  // 1 for 'tell'
	$this->savesetting('relaysymbol', 'Always relay');
	$this->savesetting('relaybot', $name);
	
	$msg = "Relay set up successfully with <highlight>$name<end>.  Please issue command '/tell $name tellrelay <myname>' if not done so already to complete the setup.";
	bot::send($msg, $sendto);
} else {
	$syntax_error = false;
}

?>