<?php
	$MODULE_NAME = "VOTE_MODULE";
	
	bot::event("setup", "$MODULE_NAME/setup.php");

	bot::command("", "$MODULE_NAME/vote.php", "vote", ALL, "Vote/Polling");
	
	bot::addsetting("vote_channel_spam", "Showing Vote status messages in", "edit", "2", "PrivateGroup;Guild;PrivateGroup and Guild;Neither", "0;1;2;3", MODERATOR, "$MODULE_NAME/vote_settings.txt");
	bot::addsetting("vote_add_new_choices", "Can users add in there own choices?", "edit", "1", "No;Yes", "0;1", MODERATOR, "$MODULE_NAME/vote_settings.txt");
	bot::addsetting("vote_create_min", "Minimum org level needed to create votes.", "edit", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", MODERATOR, "$MODULE_NAME/vote_settings.txt");
	bot::addsetting("vote_use_min", "Minimum org level needed to vote.", "edit", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", MODERATOR, "$MODULE_NAME/vote_settings.txt");
	
	bot::event("2sec", "$MODULE_NAME/votes_check.php");
	
	bot::help("vote", "$MODULE_NAME/vote.txt", ALL, "Vote/Polling");
?>