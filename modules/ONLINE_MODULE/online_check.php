<?php

if ($chatBot->is_ready()) {
	global $ircSocket;

	$db->begin_transaction();
	$data = $db->query("SELECT name, channel_type FROM `online`");

	$guildArray = array();
	$privArray = array();
	$ircArray = array();

	forEach ($data as $row) {
		if ($row->channel_type == 'guild') {
			$guildArray []= $row->name;
		} else if ($row->channel_type == 'priv') {
			$privArray []= $row->name;
		} else if ($row->channel_type == 'irc') {
			$ircArray []= $row->name;
		} else {
			LegacyLogger::log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild', 'priv' or 'irc'");
		}
	}

	$time = time();

	forEach ($chatBot->guildmembers as $name => $rank) {
		if ($buddylistManager->is_online($name)) {
			if (in_array($name, $guildArray)) {
				$db->exec("UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'guild'", $time, $name);
			} else {
				$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild>', 'guild', '<myname>', ?)", $name, $time);
			}
		}
	}

	forEach ($chatBot->chatlist as $name => $value) {
		if (in_array($name, $privArray)) {
			$db->exec("UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'priv'", $time, $name);
		} else {
			$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, '<myguild> Guest', 'priv', '<myname>', ?)", $name, $time);
		}
	}

	if (class_exists(IRC) && IRC::isConnectionActive($ircSocket)) forEach (IRC::getUsersInChannel($ircSocket, $setting->get('irc_channel')) as $name) {
		if (in_array($name, $ircArray)) {
			$db->exec("UPDATE `online` SET `dt` = ? WHERE `name` = ? AND added_by = '<myname>' AND channel_type = 'irc'", $time, $name);
		} else if ($name != $setting->get('irc_nickname')) {
			$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES (?, ?, 'irc', '<myname>', ?)", $name, $setting->get('irc_channel'), $time);
		}
	}

	$db->exec("DELETE FROM `online` WHERE (`dt` < ? AND added_by = '<myname>') OR (`dt` < ?)", $time, ($time - $setting->get('online_expire')));
	$db->commit();
}

?>
