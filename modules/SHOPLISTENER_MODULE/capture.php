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
		
		$message = preg_replace("/<font(.+)>/U", "", $message);
		$message = preg_replace("/<\/font>/U", "", $message);
		
		// messageType: 1=WTS, 2=WTB, 3=WTT, default to WTS
		$messageType = 1;
		if (preg_match("/^(.{0,3})wtb/i", $message)) {
			$messageType = 2;
		} else if (preg_match("/^(.{0,3})wtt/i", $message)) {
			$messageType = 3;
		}
		
		$matches = array();
		$pattern = '/<a href="itemref:\/\/(\d+)\/(\d+)\/(\d+)">([^<]+)<\/a>/';
		preg_match_all($pattern, $message, $matches, PREG_SET_ORDER);
		
		$message = str_replace("'", "''", $message);
		
		
		$db->begin_transaction();
		
		$db->exec("INSERT INTO shopping_messages (dimension, message_type, channel, bot, sender, dt, message) VALUES ('<dim>', '$messageType', '$channel', '<myname>', '$sender', " . time() . ", '$message')");
		$id = $db->lastInsertId();
		
		forEach ($matches as $match) {
			$name = str_replace("'", "''", $match[4]);
			$db->exec("INSERT INTO shopping_items (message_id, lowid, highid, ql, iconid, name) VALUES ($id, $match[1], $match[2], $match[3], IFNULL((SELECT iconid FROM aodb_items WHERE aoid = '{$match[1]}' LIMIT 1), 0), '$name')");
		}
		
		$db->commit();
		
		Player::get_by_name($sender);
	}
}

?>