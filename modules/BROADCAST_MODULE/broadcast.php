<?php

if (preg_match("/^broadcast$/i", $message)) {
	$blob = "<header> :::::: Broadcast Bots :::::: <end>\n\n";

	$sql = "SELECT * FROM broadcast_<myname> ORDER BY dt DESC";
  	$data = $db->query($sql);
  	forEach ($data as $row) {
	  	$remove = Text::make_chatcmd('Remove', "/tell <myname> <symbol>broadcast rem $row->name");
		$dt = date("M j, Y, G:i", $row->dt);
	  	$blob .= "<white>{$row->name}<end> [<green>added by {$row->added_by}<end>] <white>{$dt}<end> {$remove}\n";
	}
	
	if (count($data) == 0) {
		$msg = "No bots are on the broadcast list.";
	} else {
		$msg = Text::make_blob('Broadcast', $blob);
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

	$db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $name, $sender, time());
	$msg = "Broadcast bot added successfully.";
	
	// reload broadcast bot list
	require 'setup.php';
	
	Whitelist::add($name, $sender . " (bot)");

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^broadcast (rem|remove) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));

	if (!isset($chatBot->data["broadcast_list"][$name])) {
		$chatBot->send("'$name' is not on the broadcast bot list.", $sendto);
		return;
	}

	$db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $name);
	$msg = "Broadcast bot removed successfully.";
	
	// reload broadcast bot list
	require 'setup.php';
	
	Whitelist::remove($name);
	
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
