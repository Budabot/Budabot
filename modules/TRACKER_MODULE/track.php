<?php

if (preg_match("/^track$/i", $message)) {
	$db->query("SELECT t.*, p.name FROM tracked_users_<myname> t LEFT JOIN players p ON t.charid = p.charid ORDER BY `name`");
	$data = $db->fObject('all');
	$numrows = $db->numrows();
	if ($numrows != 0) {
	  	$blob .= "<header>::::: {$numrows} Users on Track List :::::<end>\n\n";
	  	forEach ($data as $row) {
			$is_online = Buddylist::is_online($row->name);
	  	  	if ($is_online === 1) {
				$status = "<green>Online<end>";
			} else if ($is_online === 0) {
				$status = "<orange>Offline<end>";
			} else {
				$status = "<grey>Unknown<end>";
			}
			
			$history = Text::make_link('History', "/tell <myname> track $row->name", 'chatcmd');

	  		$blob .= "<tab>- $row->name ($status) - $history\n";
	  	}
	  	
	    $msg = Text::make_link("<highlight>{$numrows}<end> players on the Track List", $blob);
		$chatBot->send($msg, $sendto);
	} else {
       	$chatBot->send("No players are on the track list.", $sendto);
	}
} else if (preg_match("/^track rem (.+)$/i", $message, $arr)) {
    $charid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$charid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$numrows = $db->exec("DELETE FROM tracked_users_<myname> WHERE `charid` = '$charid'");
	  	if ($numrows == 0) {
	  		$msg = "<highlight>$name<end> is not on the track list.";
	  	} else {
		    $msg = "<highlight>$name<end> has been removed from the track list.";
			Buddylist::remove($name, 'tracking');
		}
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^track add (.+)$/i", $message, $arr)) {
    $charid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$charid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$db->query("SELECT * FROM tracked_users_<myname> WHERE `charid` = '$charid'");
	  	if ($db->numrows() != 0) {
	  		$msg = "<highlight>$name<end> is already on the track list.";
	  	} else {
		    $db->exec("INSERT INTO tracked_users_<myname> (`charid`, `added_by`, `added_dt`) VALUES ($charid, '$sender', " . time() . ")");
			Player::add_info($name);
		    $msg = "<highlight>$name<end> has been added to the track list.";
	        Buddylist::add($name, 'tracking');
		}
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^track (.+)$/i", $message, $arr)) {
	$charid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	if (!$charid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
		$db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `charid` = $charid ORDER BY `dt` DESC");
		if ($db->numrows() != 0) {
			$blob .= "<header>::::: Track History for $name :::::<end>\n\n";
			while ($row = $db->fObject()) {
				$blob .= "$row->event <white>" . date(DATE_RFC850, $row->dt) ."<end>\n";
			}
			
			$msg = Text::make_link("Track History for $name", $blob);
		} else {
			$msg = "'$name' has never logged on or is not being tracked.";
		}
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
