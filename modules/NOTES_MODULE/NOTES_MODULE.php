<?php
	$MODULE_NAME = "NOTES_MODULE";
	
	//Setup
	DB::loadSQLFile($MODULE_NAME, "notes");
	DB::loadSQLFile($MODULE_NAME, "links");

	Command::register($MODULE_NAME, "", "notes.php", "notes", "guild", "displays, adds, or removes a note from your list");
	CommandAlias::register($MODULE_NAME, "notes", "note");
	
	Command::register($MODULE_NAME, "", "links.php", "links", "guild", "displays, adds, or removes links from the org link list");
	
	Setting::add($MODULE_NAME, 'showfullurls', 'Enable full urls in the link list output', 'edit', "options", 0, "true;false", "1;0", 'mod');

	//Help files
	Help::register($MODULE_NAME, "notes", "notes.txt", "guild", "Notes Help", "How to use notes");
	
?>