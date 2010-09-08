<?php

if (preg_match("/^track add (.+)$/i", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    
	if (!$uid) {
        $msg = "Player <highlight>$name<end> does not exist.";
    } else {
	  	$db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = '$uid'");
	  	if($db->numrows() != 0) {
	  		$msg = "<highlight>$name<end> is already on the track list.";
	  	} else {
		    $db->query("INSERT INTO tracked_users_<myname> (`name`, `uid`, `added_by`, `added_dt`) VALUES ('$name', $uid, '$sender', NOW())");
		    $msg = "<highlight>$name<end> has been added to the track list.";
	        $this->add_buddy($name, 'tracking');
		}
	}

	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>