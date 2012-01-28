<?php

if ($chatBot->is_ready()) {
	$db->begin_transaction();
	   
	$sql = "SELECT name, channel_type FROM `online`";
	$data = $db->query($sql);
	$guildArray = array();
	$privArray = array();
	forEach ($data as $row) {
		if ($row->channel_type == 'guild') {
			$guildArray []= $row->name;
		} else if ($row->channel_type == 'priv') {
			$privArray []= $row->name;
		} else {
			LegacyLogger::log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild' or 'priv'");
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

	$time_to_expire = ($time - $setting->get('online_expire'));
	$sql = "DELETE FROM `online` WHERE (`dt` < ? AND added_by = '<myname>') OR (`dt` < ?)";
	$db->exec($sql, $time, $time_to_expire);

	$db->commit();
}

?>