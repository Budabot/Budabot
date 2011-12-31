<?php

$accessLevel = $chatBot->getInstance('accessLevel');

if (preg_match("/^remrl (.+)$/i", $message, $arr)) {
	$who = ucfirst(strtolower($arr[1]));
	
	if ($chatBot->admins[$who]["level"] != 2) {
		$chatBot->send("<highlight>$who<end> is not a raidleader.", $sendto);
		return;
	}

	$senderAccessLevel = $accessLevel->getAccessLevelForCharacter($sender);
	$whoAccessLevel = $accessLevel->getSingleAccessLevel($who);
	if ($accessLevel->compareAccessLevels($whoAccessLevel, $senderAccessLevel) >= 0) {
		$chatBot->send("You must have a higher access level than <highlight>$who<end> in order to change his access level.");
		return;
	}

	unset($chatBot->admins[$who]);
	$db->exec("DELETE FROM admin_<myname> WHERE `name` = ?", $who);

	Buddylist::remove($who, 'admin');

	$ai = Alts::get_alt_info($who);
	if (Setting::get("alts_inherit_admin") == 1 && $ai->main != $who) {
		$chatBot->send("<red>WARNING<end>: alts inheriting admin is enabled, but $who is not a main character.  {$ai->main} is $who's main.  <red>This command did NOT affect/remove {$ai->main}'s admin privileges.<end>", $sendto);
	}

	$chatBot->send("<highlight>$who<end> has been removed as a raidleader.", $sendto);
	$chatBot->send("Your raidleader access has been removed by <highlight>$sender<end>.", $who);
} else {
	$syntax_error = true;
}

?>