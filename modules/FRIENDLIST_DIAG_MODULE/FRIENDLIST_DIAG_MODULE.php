<?php
	$MODULE_NAME = "FRIENDLIST_DIAG_MODULE";

	// View backpacks or general searches.
	bot::command("", "$MODULE_NAME/friendlist.php", "friendlist", MODERATOR, "friendlist management");
	bot::command("", "$MODULE_NAME/rembuddy.php", "rembuddy", MODERATOR, "friendlist management");

?>