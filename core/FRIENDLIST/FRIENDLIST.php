<?php
	$MODULE_NAME = "FRIENDLIST";

	Command::activate("msg", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "mod");
	Command::activate("priv", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "mod");
	Command::activate("guild", "$MODULE_NAME/friendlist_cmd.php", "friendlist", "mod");
	
	Command::activate("msg", "$MODULE_NAME/rembuddy.php", "rembuddy", "mod");
	Command::activate("priv", "$MODULE_NAME/rembuddy.php", "rembuddy", "mod");
	Command::activate("guild", "$MODULE_NAME/rembuddy.php", "rembuddy", "mod");
	
	Command::activate("msg", "$MODULE_NAME/rembuddyall.php", "rembuddyall", "mod");
	Command::activate("priv", "$MODULE_NAME/rembuddyall.php", "rembuddyall", "mod");
	Command::activate("guild", "$MODULE_NAME/rembuddyall.php", "rembuddyall", "mod");
	
	Command::activate("msg", "$MODULE_NAME/addbuddy.php", "addbuddy", "mod");
	Command::activate("priv", "$MODULE_NAME/addbuddy.php", "addbuddy", "mod");
	Command::activate("guild", "$MODULE_NAME/addbuddy.php", "addbuddy", "mod");
	
	// Help files
	Help::register($MODULE_NAME, "friendlist", "friendlist.txt", "mod", "Commands for viewing and manually changing the friend list");
	// TODO add help for rembuddy/addbuddy/rembuddyall
?>