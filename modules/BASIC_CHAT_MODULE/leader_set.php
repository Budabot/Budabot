<?php

if ($setting->get("leaderecho") == 1) {
	$status = "<green>Enabled<end>";
	$cmd = "off";
} else {
	$status = "<red>Disabled<end>";
	$cmd = "on";
}

if (preg_match("/^leader (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
	if (!$uid) {
		$msg = "Player <highlight>{$name}<end> does not exist.";
	} else if (!isset($chatBot->chatlist[$name])) {
		$msg = "Player <highlight>{$name}<end> isn't in this channel.";
	} else {
		$chatBot->data["leader"] = $name;
	  	$msg = "{$name} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
	}
  	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}

?>