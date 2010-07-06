<?php
	$MODULE_NAME = "NOTES_MODULE";
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "notes");

	//adds tower info to 'watch' list
	bot::command("", "$MODULE_NAME/note.php", "note", GUILDMEMBER, "adds or removes a note from your list");
	bot::command("", "$MODULE_NAME/notes.php", "notes", GUILDMEMBER, "displays notes in your list");

	//Help files
	bot::help("Notes", "$MODULE_NAME/notes.txt", GUILDMEMBER, "Notes Help");
	
?>