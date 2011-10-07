<?php

if (preg_match("/^addrl (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->get_uid($who) == NULL){
		$chatBot->send("<red>Sorry player you wish to add doesn't exist.<end>", $sendto);
		return;
	}
	
	if ($who == $sender) {
		$chatBot->send("<red>You can't add yourself to another group.<end>", $sendto);
		return;
	}
	
	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$msg = "<red>Alts inheriting admin is enabled, and $who is not a main character.<end>";
		if ($chatBot->admins[$ai->main]["level"] == 2) {
			$msg .= " {$ai->main} is already a raidleader.";
		} else {
			$msg .= " Try again with $who's main, <highlight>{$ai->main}<end>.";
		}
		$chatBot->send($msg, $sendto);
		return;
	}

	if ($chatBot->admins[$who]["level"] == 2) {
		$chatBot->send("<red>Sorry but $who is already a raidleader.<end>", $sendto);
		return;
	}
	
	if ((int)$chatBot->admins[$sender]["level"] <= (int)$chatBot->admins[$who]["level"]){
		$chatBot->send("<red>You must have a rank higher then $who.<end>", $sendto);
		return;
	}

	if (isset($chatBot->admins[$who]["level"]) && $chatBot->admins[$who]["level"] > 2) {
		$chatBot->send("<highlight>$who<end> has been demoted to a raidleader.", $sendto);
		$chatBot->send("You have been demoted to raidleader by <highlight>$sender<end>.", $who);
		$db->exec("UPDATE admin_<myname> SET `adminlevel` = 2 WHERE `name` = '$who'");
	} else {
		$db->exec("INSERT INTO admin_<myname> (`adminlevel`, `name`) VALUES (2, '$who')");
		$chatBot->send("<highlight>$who<end> has been added as a raidleader.", $sendto);
		$chatBot->send("You have been promoted to raidleader by <highlight>$sender<end>.", $who);
	}

	$chatBot->admins[$who]["level"] = 2;
	Buddylist::add($who, 'admin');
} else {
	$syntax_error = true;
}

?>