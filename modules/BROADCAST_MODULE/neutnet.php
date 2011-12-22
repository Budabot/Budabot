<?php

$num_neutnet_slaves = 16;

if (preg_match("/^neutnet (enable|on|add)$/i", $message)) {
	for ($i = 1; $i <= $num_neutnet_slaves; $i++) {
		$name = "Neutnet" . $i;
		
		$charid = $chatBot->get_uid($name);
		if ($charid == false) {
			continue;
		}
		
		if (isset($chatBot->data["broadcast_list"][$name])) {
			continue;
		}

		$db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $name, $sender, time());
		
		Whitelist::add($name, $sender . " (broadcast bot)");
	}
	
	// reload broadcast bot list
	require 'setup.php';

	$msg = "Neutnet bots have been added to the broadcast list.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^neutnet (disable|off|rem|remove)$/i", $message)) {
	for ($i = 1; $i <= $num_neutnet_slaves; $i++) {
		$name = "Neutnet" . $i;

		$db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $name);
		
		Whitelist::remove($name);
	}
	
	// reload broadcast bot list
	require 'setup.php';
	
	$msg = "Neutnet bots have been removed from the broadcast list.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
