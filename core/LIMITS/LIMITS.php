<?php
	require_once 'Whitelist.class.php';
	require_once 'Limits.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'Whitelist', new Whitelist);
	$chatBot->registerInstance($MODULE_NAME, 'Limits', new Limits);

	$db->loadSqlFile($MODULE_NAME, 'whitelist');

	$command->register($MODULE_NAME, "", "whitelist.php", "whitelist", "all", "Add people to whitelist to bypass limits check", "whitelist.txt", 1);

	$setting->add($MODULE_NAME, "tell_req_lvl", "Tells Min Level", "edit", "number", "0", "0;10;50;100;150;190;205;215", "", "mod", "tell_req_lvl.txt");
	$setting->add($MODULE_NAME, "tell_req_faction", "Tell Faction Limit", "edit", "options", "all", "all;Omni;Neutral;Clan;not Omni;not Neutral;not Clan", "", "mod", "tell_req_faction.txt");
	$setting->add($MODULE_NAME, "tell_req_open", "Tell General Limit", "edit", "options", "all", "all;member", "", "mod", "tell_req_open.txt");
?>
