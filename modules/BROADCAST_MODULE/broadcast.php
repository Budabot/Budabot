<?php

$whitelist = Registry::getInstance('whitelist');

if (preg_match("/^broadcast$/i", $message)) {
	$blob = '';

	$sql = "SELECT * FROM broadcast_<myname> ORDER BY dt DESC";
  	$data = $db->query($sql);
  	forEach ($data as $row) {
	  	$remove = Text::make_chatcmd('Remove', "/tell <myname> <symbol>broadcast rem $row->name");
		$dt = date(Util::DATETIME, $row->dt);
	  	$blob .= "<white>{$row->name}<end> [<green>added by {$row->added_by}<end>] <white>{$dt}<end> {$remove}\n";
	}
	
	if (count($data) == 0) {
		$msg = "No bots are on the broadcast list.";
	} else {
		$msg = Text::make_blob('Broadcast Bots', $blob);
	}

	$sendto->reply($msg);
} else if (preg_match("/^broadcast add (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$charid = $chatBot->get_uid($name);
	if ($charid == false) {
		$sendto->reply("'$name' is not a valid character name.");
		return;
	}
	
	if (isset($chatBot->data["broadcast_list"][$name])) {
		$sendto->reply("'$name' is already on the broadcast bot list.");
		return;
	}

	$db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $name, $sender, time());
	$msg = "Broadcast bot added successfully.";
	
	// reload broadcast bot list
	require 'setup.php';
	
	$whitelist->add($name, $sender . " (bot)");

    $sendto->reply($msg);
} else if (preg_match("/^broadcast (rem|remove) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));

	if (!isset($chatBot->data["broadcast_list"][$name])) {
		$sendto->reply("'$name' is not on the broadcast bot list.");
		return;
	}

	$db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $name);
	$msg = "Broadcast bot removed successfully.";
	
	// reload broadcast bot list
	require 'setup.php';
	
	$whitelist->remove($name);
	
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
