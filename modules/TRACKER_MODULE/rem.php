<?php

if (preg_match("/^track rem (.+)$/i", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = '$uid'");
	  	if($db->numrows() == 0) {
	  		$msg = "<highlight>$name<end> is not on the track list.";
	  	} else {
		    $db->query("DELETE FROM tracked_users_<myname> WHERE `uid` = '$uid'");
		    $msg = "<highlight>$name<end> has been removed from the track list.";
			$this->remove_buddy($name, 'tracking');
		}
	}

	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>