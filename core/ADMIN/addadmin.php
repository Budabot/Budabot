<?php

if (preg_match("/^addadmin (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));

	if ($chatBot->get_uid($who) == NULL){
		$chatBot->send("The character <highlight>$who<end> does not exist.", $sendto);
		return;
	}
	
	if ($who == $sender) {
		$chatBot->send("You cannot change your own access level.", $sendto);
		return;
	}
	
	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
		if ($chatBot->admins[$ai->main]["level"] == 4) {
			$msg .= "<highlight>{$ai->main}<end> is already an administrator.";
		} else {
			$msg .= "Try again with <highlight>$who<end>'s main, <highlight>{$ai->main}<end>.";
		}
		$chatBot->send($msg, $sendto);
		return;
	}

	if ($chatBot->admins[$who]["level"] == 4) {
		$chatBot->send("<highlight>$who<end> is already an administrator.", $sendto);
		return;
	}

	if (isset($chatBot->admins[$who]["level"]) && $chatBot->admins[$who]["level"] >= 2) {
		if ($chatBot->admins[$who]["level"] > 4) {
			$chatBot->send("<highlight>$who<end> has been demoted to administrator.", $sendto);
			$chatBot->send("You have been demoted to administrator by <highlight>$sender<end>.", $who);
		} else {
			$chatBot->send("<highlight>$who<end> has been promoted to administrator.", $sendto);
			$chatBot->send("You have been promoted to administrator by <highlight>$sender<end>.", $who);
		}
		$db->exec("UPDATE admin_<myname> SET `adminlevel` = 4 WHERE `name` = '$who'");
	} else {
		$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (4, '$who')");
		$chatBot->send("<highlight>$who<end> has been promoted to administrator.", $sendto);
		$chatBot->send("You have been promoted to administrator by <highlight>$sender<end>.", $who);
	}

	$chatBot->admins[$who]["level"] = 4;
	Buddylist::add($who, 'admin');
} else {
	$syntax_error = true;
}
?>