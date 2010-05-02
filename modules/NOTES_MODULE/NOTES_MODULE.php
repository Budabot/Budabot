<?php
	$MODULE_NAME = "NOTES_MODULE";

	//adds tower info to 'watch' list
	bot::command("", "$MODULE_NAME/note.php", "note", "all", "adds or removes a note from your list");
	bot::command("", "$MODULE_NAME/notes.php", "notes", "all", "displays notes in your list");

	//Helpfiles
	bot::help("Notes", "$MODULE_NAME/notes.txt", "guild", "Notes Help", "Notes");
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "notes");
	
?>