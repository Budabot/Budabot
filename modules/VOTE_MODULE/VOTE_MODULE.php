<?php
	$event->register($MODULE_NAME, "setup", "setup.php");

	$command->register($MODULE_NAME, "", "vote.php", "vote", "all", "Vote/Polling");
	
	$setting->add($MODULE_NAME, "vote_channel_spam", "Showing Vote status messages in", "edit", "options", "2", "Private Channel;Guild;Private Channel and Guild;Neither", "0;1;2;3", "mod", "votesettings");
	$setting->add($MODULE_NAME, "vote_add_new_choices", "Users can add in there own choices", "edit", "options", "1", "true;false", "1;0", "mod", "votesettings");
	$setting->add($MODULE_NAME, "vote_create_min", "Minimum org level needed to create votes.", "edit", "options", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "votesettings");
	$setting->add($MODULE_NAME, "vote_use_min", "Minimum org level needed to vote.", "edit", "options", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "votesettings");
	
	$event->register($MODULE_NAME, "2sec", "votes_check.php", "Checks timer and periodically updates chat with time left on vote");
	
	$help->register($MODULE_NAME, "vote", "vote.txt", "all", "Vote/Polling");
	$help->register($MODULE_NAME, "votesettings", "votesettings.txt", "mod", "Vote Settings");
?>