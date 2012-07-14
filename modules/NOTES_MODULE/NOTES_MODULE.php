<?php
	$db->loadSQLFile($MODULE_NAME, "notes");
	$db->loadSQLFile($MODULE_NAME, "links");

	$command->register($MODULE_NAME, "", "notes.php", "notes", "guild", "displays, adds, or removes a note from your list", "notes.txt");

	$command->register($MODULE_NAME, "", "links.php", "links", "guild", "displays, adds, or removes links from the org link list", "links.txt");

	$setting->add($MODULE_NAME, 'showfullurls', 'Enable full urls in the link list output', 'edit', "options", 0, "true;false", "1;0");
?>
