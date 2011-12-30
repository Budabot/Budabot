<?php
	require_once 'Whitelist.class.php';
	require_once 'Limits.class.php';
	
	$db->loadSqlFile($MODULE_NAME, 'whitelist');
	
	$command->register($MODULE_NAME, "", "whitelist.php", "whitelist", "all", "Add people to whitelist to bypass limits check");

	$setting->add($MODULE_NAME, "tell_req_lvl", "Tells Min Level", "edit", "number", "0", "0;10;50;100;150;190;205;215", "", "mod");
	$setting->add($MODULE_NAME, "tell_req_faction", "Tell Faction Limit", "edit", "options", "all", "all;Omni;Neutral;Clan;not Omni;not Neutral;not Clan", "", "mod");
	$setting->add($MODULE_NAME, "tell_req_open", "Tell General Limit", "edit", "options", "all", "all;member", "", "mod");

	$help->register($MODULE_NAME, "whitelist", "whitelist.txt", "mod", "How to add exceptions to limits rules");
	$help->register($MODULE_NAME, "tell_req_lvl", "tell_req_lvl.txt", "mod", "Set level requirements to send tells to the bot");
	$help->register($MODULE_NAME, "tell_req_faction", "tell_req_faction.txt", "mod", "Set faction requirements to send tells to the bot");
	$help->register($MODULE_NAME, "tell_req_open", "tell_req_open.txt", "mod", "Set general requirements to send tells to the bot");
?>