<?php

if (preg_match("/^track$/i", $message)) {
	$data = $db->query("SELECT * FROM tracked_users_<myname> ORDER BY `name`");
	$numrows = count($data);
	if ($numrows != 0) {
		$blob = '';
		forEach ($data as $row) {
			$row2 = $db->queryRow("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = ? ORDER BY `dt` DESC LIMIT 1", $row->uid);
			if ($row2 != null) {
				$last_action = " <white>" . date(Util::DATETIME, $row2->dt) ."<end>";
			}

			if ($row2->event == 'logon') {
				$status = "<green>logon<end>";
			} else if ($row2->event == 'logoff') {
				$status = "<orange>logoff<end>";
			} else {
				$status = "<grey>None<end>";
			}

			$remove = Text::make_chatcmd('Remove', "/tell <myname> track rem $row->name");

			$history = Text::make_chatcmd('History', "/tell <myname> track $row->name");

			$blob .= "<tab>-[{$history}] {$row->name} ({$status}{$last_action}) - {$remove}\n";
		}

	    $msg = Text::make_blob("Tracklist ({$numrows})", $blob);
		$sendto->reply($msg);
	} else {
	$sendto->reply("No players are on the track list.");
	}
} else if (preg_match("/^track rem (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));

	if (!$uid) {
        $msg = "Character <highlight>$name<end> does not exist.";
    } else {
		$data = $db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
		if (count($data) == 0) {
			$msg = "<highlight>$name<end> is not on the track list.";
		} else {
		    $db->exec("DELETE FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
		    $msg = "<highlight>$name<end> has been removed from the track list.";
			$buddylistManager->remove($name, 'tracking');
		}
	}

	$sendto->reply($msg);
} else if (preg_match("/^track add (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));

	if (!$uid) {
        $msg = "Character <highlight>$name<end> does not exist.";
    } else {
		$data = $db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
		if (count($data) != 0) {
			$msg = "<highlight>$name<end> is already on the track list.";
		} else {
		    $db->exec("INSERT INTO tracked_users_<myname> (`name`, `uid`, `added_by`, `added_dt`) VALUES (?, ?, ?, ?)", $name, $uid, $sender, time());
		    $msg = "<highlight>$name<end> has been added to the track list.";
	        $buddylistManager->add($name, 'tracking');
		}
	}

	$sendto->reply($msg);
} else if (preg_match("/^track (.+)$/i", $message, $arr)) {
	$uid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));

	$data = $db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = $uid ORDER BY `dt` DESC");
	if (count($data) == 0) {
		$blob = '';
		forEach ($data as $row) {
			$blob .= "$row->event <white>" . date(Util::DATETIME, $row->dt) ."<end>\n";
		}

	    $msg = Text::make_blob("Track History for $name", $blob);
	} else {
		$msg = "'$name' has never logged on or is not being tracked.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
