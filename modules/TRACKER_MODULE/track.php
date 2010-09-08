<?php

if (preg_match("/^track$/i", $message)) {
	$db->query("SELECT * FROM tracked_users_<myname> ORDER BY `name`");
	$numrows = $db->numrows();
	if ($numrows != 0) {
	  	$blob .= "<header>::::: {$numrows} Users on Track List :::::<end>\n\n";
	  	while ($row = $db->fObject()) {
			$is_online = $this->buddy_online($row->name)
	  	  	if ($is_online === true) {
				$status = "<green>Online<end>";
			} else if ($is_online === false) {
				$status = "<orange>Offline<end>";
			} else {
				$status = "<grey>Unknown<end>";
			}

	  		$blob .= "<tab>- $row->name ($status)\n";
	  	}
	  	
	    $msg = bot::makeLink("<highlight>{$numrows}<end> players on the Track List", $blob);
		bot::send($msg, $sendto);
	} else {
       	bot::send("No players are on the track list.", $sendto);
	}
} else if (preg_match("/^track (.+)$/i", $message)) {
	$uid = $this->get_uid($arr[1]);
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT event, dt FROM tracking_<myname> WHERE `uid` = $uid");
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
