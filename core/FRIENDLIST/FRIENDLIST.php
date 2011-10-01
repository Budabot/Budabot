<?php
	Command::register($MODULE_NAME, "", "friendlist_cmd.php", "friendlist", "mod", "Show buddies on bot's buddylist");
	Command::register($MODULE_NAME, "", "addbuddy.php", "addbuddy", "mod", "Add a buddy to bot's buddylist", 'friendlist');
	Command::register($MODULE_NAME, "", "rembuddy.php", "rembuddy", "mod", "Remove a buddy from bot's buddylist", 'friendlist');
	Command::register($MODULE_NAME, "", "rembuddyall.php", "rembuddyall", "mod", "Remove all buddies from bot's buddylist", 'friendlist');
	
	Help::register($MODULE_NAME, "friendlist", "friendlist.txt", "mod", "Commands for viewing and manually changing the friend list");
?>