<?php

$name = "Dnetorg";

$whitelist = Registry::getInstance('whitelist');

if (preg_match("/^dnet (enable|on|add)/i", $message)) {
	if (!isset($chatBot->data["broadcast_list"][$name])) {
		Setting::save('dnet_status', 1);
		$db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $name, $sender, time());
		$whitelist->add($name, $sender . " (broadcast bot)");

		// reload broadcast bot list
		require 'setup.php';

		$msg = "!join";
		Logger::log_chat("Out. Msg.", $name, $msg);
		$chatBot->send_tell($name, $msg);
	}

	$msg = "Dnet support has been <green>enabled<end>.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^dnet (disable|off|rem|remove)$/i", $message)) {
	Setting::save('dnet_status', 0);
	$db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $name);
	$whitelist->remove($name);

	// reload broadcast bot list
	require 'setup.php';

	$chatBot->privategroup_leave($name);

	$msg = "Dnet support has been <orange>disabled<end>.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
