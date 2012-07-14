<?php

if (preg_match("/^tellrelay (.*)$/", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($name);

	if (!$uid) {
		$msg = "Character <highlight>$name<end> does not exist.";
		$sendto->reply($msg);
		return;
	}

	$setting->save('relaytype', 1);  // 1 for 'tell'
	$setting->save('relaysymbol', 'Always relay');
	$setting->save('relaybot', $name);

	$msg = "Relay set up successfully with <highlight>$name<end>.  Please issue command '/tell $name tellrelay <myname>' if not done so already to complete the setup.";
	$sendto->reply($msg);
} else {
	$syntax_error = false;
}

?>
