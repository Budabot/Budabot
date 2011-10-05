<?php
	Command::activate("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	Command::activate("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	Command::register($MODULE_NAME, "", "addalias.php", "addalias", "mod", "Add a command alias", 'alias');
	Command::register($MODULE_NAME, "", "remalias.php", "remalias", "mod", "Remove a command alias", 'alias');
	Command::register($MODULE_NAME, "", "aliaslist.php", "aliaslist", "guild", "List all aliases", 'alias');
	Command::register($MODULE_NAME, "", "cmdlist.php", "cmdlist", "guild", "Shows a list of all commands on the bot");
	Command::register($MODULE_NAME, "", "eventlist.php", "eventlist", "guild", "Shows a list of all events on the bot");
	Command::register($MODULE_NAME, "", "searchcmd.php", "searchcmd", "mod", "Find which module has specified command");

	Help::register($MODULE_NAME, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
	Help::register($MODULE_NAME, "alias", "alias.txt", "mod", "How to add and remove aliases");
	Help::register($MODULE_NAME, "searchcmd", "searchcmd.txt", "mod", "How to find which module a command belongs to");
	Help::register($MODULE_NAME, "eventlist", "boteventlist.txt", "guild", "How to see list of all events");
	Help::register($MODULE_NAME, "cmdlist", "cmdlist.txt", "guild", "How to see list of all commands");
?>