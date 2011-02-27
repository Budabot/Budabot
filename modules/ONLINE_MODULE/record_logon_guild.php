<?php

if (isset($chatBot->guildmembers[$charid])) {
  	$db->query("SELECT charid FROM online WHERE `charid` = '$charid' AND `channel_type` = 'guild' AND added_by = '<myname>'");
	if ($db->numrows() == 0) {
	    $db->exec("INSERT INTO online (`charid`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$charid', '{$chatBot->vars['my guild']}', 'guild', '<myname>', " . time() . ")");
	}

	// update info for player
	Player::add_info($sender);
}

?>
