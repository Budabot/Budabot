<?
	$MODULE_NAME = "NOTES_MODULE";

	//adds tower info to 'watch' list
	bot::command("guild", "$MODULE_NAME/note.php", "note", "all", "adds or removes a note from your list");
	bot::command("priv", "$MODULE_NAME/note.php", "note", "all", "adds or removes a note from your list");
	bot::command("msg", "$MODULE_NAME/note.php", "note", "all", "adds or removes a note from your list");
	
	// removes tower info from 'watch' list
	bot::command("guild", "$MODULE_NAME/notes.php", "notes", "all", "displays notes in your list");
	bot::command("priv", "$MODULE_NAME/notes.php", "notes", "all", "displays notes in your list");
	bot::command("msg", "$MODULE_NAME/notes.php", "notes", "all", "displays notes in your list");
	
	//Helpfiles
	bot::help("Notes", "$MODULE_NAME/notes.txt", "guild", "Notes Help", "Notes");
	
	//Setup
	bot::loadSQLFile($MODULE_NAME, "notes");
	
?>