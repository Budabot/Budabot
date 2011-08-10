<?php

if (preg_match("/^track$/i", $message)) {
	$db->query("SELECT * FROM tracked_users_<myname> ORDER BY `name`");
	$numrows = $db->numrows();
	if ($numrows != 0) {
	  	$blob .= "<header> :::::: {$numrows} Users on Track List :::::: <end>\n\n";
		$data = $db->fObject('all');
	  	forEach ($data as $row) {
			$db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = $row->uid ORDER BY `dt` DESC LIMIT 1");
			if ($db->numrows() > 0) {
				$row2 = $db->fObject();
				$last_action = " <white>" . date(DATE_RFC850, $row2->dt) ."<end>";
			}
			
			if ($row2->event == 'logon') {
				$status = "<green>logon<end>";
			} else if ($row2->event == 'logoff') {
				$status = "<orange>logoff<end>";
			} else {
				$status = "<grey>None<end>";
			}
			
			$remove = Text::make_link('Remove', "/tell <myname> track rem $row->name", 'chatcmd');
			
			$history = Text::make_link('History', "/tell <myname> track $row->name", 'chatcmd');

	  		$blob .= "<tab>-[{$history}] {$row->name} ({$status}{$last_action}) - {$remove}\n";
	  	}
	  	
	    $msg = Text::make_blob("<highlight>{$numrows}<end> players on the Track List", $blob);
		$chatBot->send($msg, $sendto);
	} else {
       	$chatBot->send("No players are on the track list.", $sendto);
	}
} else if (preg_match("/^track rem (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
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

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^track add (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
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

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^track (.+)$/i", $message, $arr)) {
	$uid = $chatBot->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = $uid ORDER BY `dt` DESC");
	if ($db->numrows() != 0) {
		$blob .= "<header> :::::: Track History for $name :::::: <end>\n\n";
	  	$data = $db->fObject('all');
	  	forEach ($data as $row) {
	  		$blob .= "$row->event <white>" . date(DATE_RFC850, $row->dt) ."<end>\n";
	  	}
	  	
	    $msg = Text::make_blob("Track History for $name", $blob);
	} else {
		$msg = "'$name' has never logged on or is not being tracked.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
