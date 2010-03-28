<?
$MODULE_NAME = "INFO_MODULE";

	//Private
	bot::command("priv", "$MODULE_NAME/info.php", "info", "all", "Shows basic pvp title info");

	//Guild
	bot::command("guild", "$MODULE_NAME/info.php", "info", "all", "Shows basic pvp title info");

	//Tells
	bot::command("msg", "$MODULE_NAME/info.php", "info", "all", "Shows basic pvp title info");
?>