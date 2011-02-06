<?php

if (preg_match("/^track$/i", $message)) {
	$db->query("SELECT * FROM tracked_users_<myname> ORDER BY `name`");
	$numrows = $db->numrows();
	if ($numrows != 0) {
	  	$blob .= "<header>::::: {$numrows} Users on Track List :::::<end>\n\n";
	  	while ($row = $db->fObject()) {
			$is_online = Buddylist::is_online($row->name);
	  	  	if ($is_online === 1) {
				$status = "<green>Online<end>";
			} else if ($is_online === 0) {
				$status = "<orange>Offline<end>";
			} else {
				$status = "<grey>Unknown<end>";
			}
			
			$history = $this->makeLink('History', "/tell <myname> track $row->name", 'chatcmd');

	  		$blob .= "<tab>- $row->name ($status) - $history\n";
	  	}
	  	
	    $msg = bot::makeLink("<highlight>{$numrows}<end> players on the Track List", $blob);
		bot::send($msg, $sendto);
	} else {
       	bot::send("No players are on the track list.", $sendto);
	}
} else if (preg_match("/^track rem (.+)$/i", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = '$uid'");
	  	if($db->numrows() == 0) {
	  		$msg = "<highlight>$name<end> is not on the track list.";
	  	} else {
		    $db->exec("DELETE FROM tracked_users_<myname> WHERE `uid` = '$uid'");
		    $msg = "<highlight>$name<end> has been removed from the track list.";
			Buddylist::remove($name, 'tracking');
		}
	}

	bot::send($msg, $sendto);
} else if (preg_match("/^track add (.+)$/i", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = '$uid'");
	  	if($db->numrows() != 0) {
	  		$msg = "<highlight>$name<end> is already on the track list.";
	  	} else {
		    $db->exec("INSERT INTO tracked_users_<myname> (`name`, `uid`, `added_by`, `added_dt`) VALUES ('$name', $uid, '$sender', " . time() . ")");
		    $msg = "<highlight>$name<end> has been added to the track list.";
	        Buddylist::add($name, 'tracking');
		}
	}

	bot::send($msg, $sendto);
} else if (preg_match("/^track (.+)$/i", $message, $arr)) {
	$uid = $this->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = $uid ORDER BY `dt` DESC");
	if ($db->numrows() != 0) {
		$blob .= "<header>::::: Track History for $name :::::<end>\n\n";
	  	while ($row = $db->fObject()) {
	  		$blob .= "$row->event <white>" . date(DATE_RFC850, $row->dt) ."<end>\n";
	  	}
	  	
	    $msg = bot::makeLink("Track History for $name", $blob);
	} else {
		$msg = "'$name' has never logged on or is not being tracked.";
	}
	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
