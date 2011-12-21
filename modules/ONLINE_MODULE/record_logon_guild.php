<?php

if (isset($chatBot->guildmembers[$sender])) {
  	$data = $db->query("SELECT name FROM `online` WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $sender);
	if (count($data) == 0) {
		$sql = "INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)";
	    $db->exec($sql, $sender, time());
	}

	// update info for player
	Player::get_by_name($sender);
}

?>
