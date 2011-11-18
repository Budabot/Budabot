<?php

if ($chatBot->is_ready()) {
	$db->begin_transaction();
	   
	$sql = "SELECT name, channel_type FROM `online`";
	$db->query($sql);
	$data = $db->fObject('all');
	$guildArray = array();
	$privArray = array();
	forEach ($data as $row) {
		if ($row->channel_type == 'guild') {
			$guildArray []= $row->name;
		} else if ($row->channel_type == 'priv') {
			$privArray []= $row->name;
		} else {
			Logger::log("WARN", "ONLINE_MODULE", "Unknown channel type: '$row->channel_type'. Expected: 'guild' or 'priv'");
		}
	}

	$time = time();
	forEach ($chatBot->guildmembers as $name => $rank) {
		if (Buddylist::is_online($name)) {
			if (in_array($name, $guildArray)) {
				$db->exec("UPDATE `online` SET `dt` = " . $time . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'guild'");
			} else {
				$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild>', 'guild', '<myname>', " . $time . ")");
			}
		}
	}

	forEach ($chatBot->chatlist as $name => $value) {
		if (in_array($name, $privArray)) {
			$db->exec("UPDATE `online` SET `dt` = " . $time . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'priv'");
		} else {
			$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild> Guest', 'priv', '<myname>', " . $time . ")");
		}
	}

	$time_to_expire = ($time - Setting::get('online_expire'));
	$sql = "DELETE FROM `online` WHERE (`dt` < {$time} AND added_by = '<myname>') OR (`dt` < {$time_to_expire})";
	$db->exec($sql);

	$db->commit();
}

?>