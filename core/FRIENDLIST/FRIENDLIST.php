<?php
	$MODULE_NAME = "FRIENDLIST";

	bot::regcommand("msg", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "admin");
	bot::regcommand("priv", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "admin");
	bot::regcommand("guild", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "admin");
	
	bot::regcommand("msg", "$MODULE_NAME/rembuddy.php", "rembuddy", "admin");
	bot::regcommand("priv", "$MODULE_NAME/rembuddy.php", "rembuddy", "admin");
	bot::regcommand("guild", "$MODULE_NAME/rembuddy.php", "rembuddy", "admin");
	
	bot::regcommand("msg", "$MODULE_NAME/addbuddy.php", "addbuddy", "admin");
	bot::regcommand("priv", "$MODULE_NAME/addbuddy.php", "addbuddy", "admin");
	bot::regcommand("guild", "$MODULE_NAME/addbuddy.php", "addbuddy", "admin");
?>