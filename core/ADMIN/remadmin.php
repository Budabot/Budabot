<?php

if (preg_match("/^remadmin (.+)$/i", $message, $arr)){
	$who = ucfirst(strtolower($arr[1]));

	if ($chatBot->admins[$who]["level"] != 4) {
		$chatBot->send("<red>$who is not an Administrator of this Bot.<end>", $sendto);
		return;
	}
	
	if (!AccessLevel::check_access($sender, 'superadmin')){
		$chatBot->send("<red>You need to be Super-Administrator to kick a Administrator<end>", $sendto);
		return;
	}
	
	unset($chatBot->admins[$who]);
	$db->exec("DELETE FROM admin_<myname> WHERE `name` = '$who'");

	Buddylist::remove($who, 'admin');
	
	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$chatBot->send("<red>WARNING<end>: Alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect/remove {$ai->main}'s admin privileges.<end>", $sendto);
	}
	
	$chatBot->send("<highlight>$who<end> has been removed as an administrator.", $sendto);
	$chatBot->send("Your administrator access has been removed by $sender.", $who);
} else {
	$syntax_error = true;
}

?>