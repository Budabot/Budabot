<?php
	$MODULE_NAME = "VOTE_MODULE";
	
	Event::register($MODULE_NAME, "setup", "setup.php");

	Command::register($MODULE_NAME, "", "vote.php", "vote", "all", "Vote/Polling");
	
	Setting::add($MODULE_NAME, "vote_channel_spam", "Showing Vote status messages in", "edit", "2", "Private Channel;Guild;Private Channel and Guild;Neither", "0;1;2;3", "mod", "$MODULE_NAME/vote_settings.txt");
	Setting::add($MODULE_NAME, "vote_add_new_choices", "Can users add in there own choices?", "edit", "1", "No;Yes", "0;1", "mod", "$MODULE_NAME/vote_settings.txt");
	Setting::add($MODULE_NAME, "vote_create_min", "Minimum org level needed to create votes.", "edit", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "$MODULE_NAME/vote_settings.txt");
	Setting::add($MODULE_NAME, "vote_use_min", "Minimum org level needed to vote.", "edit", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "$MODULE_NAME/vote_settings.txt");
	
	Event::register($MODULE_NAME, "2sec", "votes_check.php", 'none', "Checks timer and periodically updates chat with time left on vote");
	
	Help::register($MODULE_NAME, "vote", "vote.txt", "all", "Vote/Polling");
?>