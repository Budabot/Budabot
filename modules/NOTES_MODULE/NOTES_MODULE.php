<?php
	$MODULE_NAME = "NOTES_MODULE";
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "notes");

	bot::command("", "$MODULE_NAME/notes.php", "note", "all", "displays, adds, or removes a note from your list");
	bot::command("", "$MODULE_NAME/notes.php", "notes", "all", "displays, adds, or removes a note from your list");

	//Help files
	bot::help($MODULE_NAME, "notes", "notes.txt", "guild", "Notes Help", "How to use notes");
	
?>