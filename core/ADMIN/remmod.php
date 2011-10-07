<?php

if (preg_match("/^remmod (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->admins[$who]["level"] != 3) {
		$chatBot->send("<red>$who is not a Moderator of this Bot.<end>", $sendto);
		return;
	}
	
	if ((int)$chatBot->admins[$sender]["level"] <= (int)$chatBot->admins[$who]["level"]){
		$chatBot->send("<red>You must have a rank higher then $who.", $sendto);
		return;
	}
	
	unset($chatBot->admins[$who]);
	$db->exec("DELETE FROM admin_<myname> WHERE `name` = '$who'");
	
	Buddylist::remove($who, 'admin');
	
	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$chatBot->send("<red>WARNING<end>: Alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect/remove {$ai->main}'s admin privileges.<end>", $sendto);
	}

	$chatBot->send("<highlight>$who<end> has been removed as a moderator.", $sendto);
	$chatBot->send("Your moderator access has been removed by <highlight>$sender<end>.", $who);
} else {
	$syntax_error = true;
}

?>