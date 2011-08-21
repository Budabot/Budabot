<?php
	$MODULE_NAME = "ORG_HISTORY_MODULE";

	DB::add_table_replace('#__org_history', 'org_history');
	DB::loadSQLFile($MODULE_NAME, "org_history");

	Command::register($MODULE_NAME, "", "org_history.php", "orghistory", "guild", "Shows the org history (invites and kicks and leaves) for a player");
	
	Event::register($MODULE_NAME, "orgmsg", "org_action_listener.php", "none", "Capture Org Invite/Kick/Leave messages for orghistory");
	
	Help::register($MODULE_NAME, "orghistory", "org_history.txt", "guild", "How to use orghistory");
?>