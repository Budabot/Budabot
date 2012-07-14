<?php
    require_once 'CommandSearchView.class.php';
    require_once 'CommandSearchController.class.php';

    $chatBot->registerInstance($MODULE_NAME, 'CommandSearchView', new CommandSearchView);
    $chatBot->registerInstance($MODULE_NAME, 'CommandSearchController', new CommandSearchController);

	$command->activate("msg", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	$command->activate("guild", "$MODULE_NAME/cmdcfg.php", "config", "mod");
	$command->activate("priv", "$MODULE_NAME/cmdcfg.php", "config", "mod");

	$command->register($MODULE_NAME, "", "addalias.php", "addalias", "mod", "Add a command alias", 'alias.txt', 1);
	$command->register($MODULE_NAME, "", "remalias.php", "remalias", "mod", "Remove a command alias", 'alias.txt', 1);
	$command->register($MODULE_NAME, "", "aliaslist.php", "aliaslist", "guild", "List all aliases", 'alias.txt', 1);
	$command->register($MODULE_NAME, "", "cmdlist.php", "cmdlist", "guild", "Shows a list of all commands on the bot", "cmdlist.txt", 1);
	$command->register($MODULE_NAME, "", "eventlist.php", "eventlist", "guild", "Shows a list of all events on the bot", "eventlist.txt", 1);
	$commandAlias->register($MODULE_NAME, "cmdsearch", "searchcmd");

	$help->register($MODULE_NAME, "config", "config.txt", "mod", "Configure Commands/Events of the Bot");
?>
