<?php
	$MODULE_NAME = "FRIENDLIST_MODULE";

	bot::command("", "$MODULE_NAME/friendlist.php", "friendlist", "admin", "shows friends list");
	bot::command("", "$MODULE_NAME/rembuddy.php", "rembuddy", "admin", "remove a friend from the friends list");
	bot::command("", "$MODULE_NAME/addbuddy.php", "addbuddy", "admin", "add a friend to the friends list");
?>