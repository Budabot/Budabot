<?php

if (preg_match("/^broadcast$/i", $message)) {
	$blob = "<header> :::::: Broadcast Bots :::::: <end>\n\n";

	$sql = "SELECT * FROM broadcast_<myname> ORDER BY dt DESC";
  	$db->query($sql);
	$data = $db->fObject('all');
  	forEach ($data as $row) {
	  	$remove = Text::make_link('Remove', "/tell <myname> <symbol>broadcast rem $row->name" , 'chatcmd');
		$dt = gmdate("M j, Y, G:i", $row->dt);
	  	$blob .= "<white>{$row->name}<end> [<green>added by {$row->added_by}<end>] <white>{$dt}<end> {$remove}\n";
	}
	
	if (count($data) == 0) {
		$msg = "No bots are on the broadcast list.";
	} else {
		$msg = Text::make_link('Broadcast', $blob, 'blob');
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^broadcast add (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$charid = $chatBot->get_uid($name);
	if ($charid == false) {
		$chatBot->send("'$name' is not a valid character name.", $sendto);
		return;
	}
	
	if (isset($chatBot->data["broadcast_list"][$name])) {
		$chatBot->send("'$name' is already on the broadcast bot list.", $sendto);
		return;
	}

	$db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES('$name', '$sender', '" . time() . "')");
	$msg = "Broadcast bot added successfully.";
	
	// reload broadcast bot list
	global $chatBot;
	require 'setup.php';
	
	Whitelist::add($name, $sender . " (broadcast bot)");

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^broadcast (rem|remove) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));

	if (!isset($chatBot->data["broadcast_list"][$name])) {
		$chatBot->send("'$name' is not on the broadcast bot list.", $sendto);
		return;
	}

	$db->exec("DELETE FROM broadcast_<myname> WHERE name = '$name'");
	$msg = "Broadcast bot removed successfully.";
	
	// reload broadcast bot list
	global $chatBot;
	require 'setup.php';
	
	Whitelist::remove($name);
	
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
