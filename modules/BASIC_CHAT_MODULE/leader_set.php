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
		$msg = "Character <highlight>{$name}<end> does not exist.";
	} else if (!isset($chatBot->chatlist[$name])) {
		$msg = "Character <highlight>{$name}<end> is not in this channel.";
	} else {
		$chatBot->data["leader"] = $name;
		$msg = "{$name} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
	}
	$chatBot->sendPrivate($msg);
} else {
	$syntax_error = true;
}

?>
