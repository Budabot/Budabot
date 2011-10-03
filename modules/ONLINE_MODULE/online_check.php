<?php

if ($chatBot->is_ready()) {
	$db->begin_transaction();
	   
	$sql = "SELECT name FROM `online`";
	$db->query($sql);
	$data = $db->fObject('all');
	$array = array();
	forEach ($data as $row) {
		$array []= $row->name;
	}

	forEach ($chatBot->guildmembers as $name => $rank) {
		if (Buddylist::is_online($name)) {
			if (in_array($name, $array)) {
				$db->exec("UPDATE `online` SET `dt` = " . time() . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'guild'");
			} else {
				$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild>', 'guild', '<myname>', " . time() . ")");
			}
		}
	}

	forEach ($chatBot->chatlist as $name => $value) {
		if (in_array($name, $array)) {
			$db->exec("UPDATE `online` SET `dt` = " . time() . " WHERE `name` = '$name' AND added_by = '<myname>' AND channel_type = 'priv'");
		} else {
			$db->exec("INSERT INTO `online` (`name`, `channel`,  `channel_type`, `added_by`, `dt`) VALUES ('$name', '<myguild> Guest', 'priv', '<myname>', " . time() . ")");
		}
	}

	$time_to_expire = (time() - Setting::get('online_expire'));
	$sql = "DELETE FROM `online` WHERE `dt` < {$time_to_expire}";
	$db->exec($sql);

	$db->commit();
}

?>