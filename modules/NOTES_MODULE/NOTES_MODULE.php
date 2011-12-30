<?php
	$db->loadSQLFile($MODULE_NAME, "notes");
	$db->loadSQLFile($MODULE_NAME, "links");

	$command->register($MODULE_NAME, "", "notes.php", "notes", "guild", "displays, adds, or removes a note from your list");
	
	$command->register($MODULE_NAME, "", "links.php", "links", "guild", "displays, adds, or removes links from the org link list");
	
	Setting::add($MODULE_NAME, 'showfullurls', 'Enable full urls in the link list output', 'edit', "options", 0, "true;false", "1;0");

	Help::register($MODULE_NAME, "notes", "notes.txt", "guild", "How to use notes");
	Help::register($MODULE_NAME, "links", "links.txt", "guild", "How to use links");
	
?>