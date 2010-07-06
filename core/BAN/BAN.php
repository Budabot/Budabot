<?php 
$MODULE_NAME = "BAN";

	//Commands
	$this->regcommand("msg", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	$this->regcommand("msg", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	$this->regcommand("msg", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	$this->regcommand("priv", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/ban_player.php", "ban", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/unban.php", "unban", MODERATOR);
	$this->regcommand("guild", "$MODULE_NAME/banlist.php", "banlist", MODERATOR);

	//Events
	$this->regevent("1hour", "$MODULE_NAME/check_tempban.php");

	//Setup
	$this->regevent("setup", "$MODULE_NAME/upload_banlist.php");
	
	//Help Files
	$this->help("banhelp", "$MODULE_NAME/banhelp.txt", MODERATOR, "Ban a person from the bot.");
?>