<?php
	$MODULE_NAME = "NOTES_MODULE";
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "notes");
	bot::loadSQLFile($MODULE_NAME, "links");

	bot::command("", "$MODULE_NAME/notes.php", "note", "guild", "displays, adds, or removes a note from your list");
	bot::command("", "$MODULE_NAME/notes.php", "notes", "guild", "displays, adds, or removes a note from your list");
	
	bot::command("", "$MODULE_NAME/links.php", "links", "guild", "displays, adds, or removes links from the org link list");

	//Help files
	Help::register($MODULE_NAME, "notes", "notes.txt", "guild", "Notes Help", "How to use notes");
	
?>