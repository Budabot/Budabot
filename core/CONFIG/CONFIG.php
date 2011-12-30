<?php
	$command->activate("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	$command->activate("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	$command->activate("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	$command->register($MODULE_NAME, "", "addalias.php", "addalias", "mod", "Add a command alias", 'alias');
	$command->register($MODULE_NAME, "", "remalias.php", "remalias", "mod", "Remove a command alias", 'alias');
	$command->register($MODULE_NAME, "", "aliaslist.php", "aliaslist", "guild", "List all aliases", 'alias');
	$command->register($MODULE_NAME, "", "cmdlist.php", "cmdlist", "guild", "Shows a list of all commands on the bot");
	$command->register($MODULE_NAME, "", "eventlist.php", "eventlist", "guild", "Shows a list of all events on the bot");
	$command->register($MODULE_NAME, "", "cmdsearch.php", "cmdsearch", "all", "Find commands based on key words");
	CommandAlias::register($MODULE_NAME, "cmdsearch", "searchcmd");

	$help->register($MODULE_NAME, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
	$help->register($MODULE_NAME, "alias", "alias.txt", "mod", "How to add and remove aliases");
	$help->register($MODULE_NAME, "cmdsearch", "cmdsearch.txt", "all", "How to find a command base on key words");
	$help->register($MODULE_NAME, "eventlist", "eventlist.txt", "guild", "How to see list of all events");
	$help->register($MODULE_NAME, "cmdlist", "cmdlist.txt", "guild", "How to see list of all commands");
?>