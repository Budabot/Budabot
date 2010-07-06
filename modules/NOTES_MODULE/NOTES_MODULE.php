<?php
	$MODULE_NAME = "NOTES_MODULE";
	
	//Setup
	$this->loadSQLFile($MODULE_NAME, "notes");

	//adds tower info to 'watch' list
	$this->command("", "$MODULE_NAME/note.php", "note", GUILDMEMBER, "adds or removes a note from your list");
	$this->command("", "$MODULE_NAME/notes.php", "notes", GUILDMEMBER, "displays notes in your list");

	//Help files
	$this->help("Notes", "$MODULE_NAME/notes.txt", GUILDMEMBER, "Notes Help");
	
?>