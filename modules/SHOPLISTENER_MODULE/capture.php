<?php

if ($packet_type == AOCP_GROUP_MESSAGE) {
	$b = unpack("C*", $args[0]);
	// check to make sure message is from a shopping channel
	// (first byte = 134; see http://aodevs.com/forums/index.php/topic,42.msg2192.html#msg2192)
	if ($b[1] == 134) {
		$channel = $chatBot->get_gname($args[0]);
		$sender	= $chatBot->lookup_user($args[1]);
		$message = $args[2];
		
		Logger::log_chat($channel, $sender, $message);
		
		$matches = array();
		$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
		preg_match_all($pattern, $message, $matches, PREG_SET_ORDER);
		
		$msg = str_replace("'", "''", $message);
		
		
		$db->begin_transaction();
		
		$db->exec("INSERT INTO shopping_messages (dimension, channel, bot, sender, dt, message) VALUES ('<dim>', '$channel', '<myname>', '$sender', " . time() . ", '$msg')");
		$id = $db->lastInsertId();
		
		forEach ($matches as $match) {
			$name = str_replace("'", "''", $match[4]);
			$db->exec("INSERT INTO shopping_items (message_id, lowid, highid, ql, iconid, name) VALUES ($id, $match[1], $match[2], $match[3], (SELECT IFNULL(icon, 0) FROM aodb WHERE highid = '{$match[2]}' LIMIT 1), '$name')");
		}
		
		$db->commit();
		
		Player::get_by_name($sender);
	}
}

?>