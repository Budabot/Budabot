<?php
$MODULE_NAME = "ADV_GUILD_MODULE";
$PLUGIN_VERSION = 0.1;

	//News
    bot::event("logOn", "$MODULE_NAME/news_logon.php", "none", "Show News on logon of members");  	
	bot::command("guild", "$MODULE_NAME/news.php", "news", "all", "Set News");
	bot::command("priv", "$MODULE_NAME/news.php", "news", "all", "Set News");
	bot::command("msg", "$MODULE_NAME/news.php", "news", "all", "Set News");
		
	//Citycloak
    bot::command("guild", "$MODULE_NAME/city_guild.php", "city", "all", "Shows the status of the Citycloak");
    bot::event("guild", "$MODULE_NAME/city_guild.php", "city");
    bot::event("1min", "$MODULE_NAME/city_guild_timer.php", "city");
	bot::addsetting("city_attack_spam", "Showing City Attacks in", "edit", "2", "PrivateGroup;Guild;PrivateGroup and Guild", "0;1;2", "mod");

	//Guildrelay
  	bot::event("guild", "$MODULE_NAME/relay_guild_out.php", "grc", "Send Guildrelay messages");
	bot::event("logOn", "$MODULE_NAME/relay_guild_logon.php", "grc", "Sends Logon messages");
	bot::event("logOff", "$MODULE_NAME/relay_guild_logoff.php", "grc", "Sends Logoff messages");
  	bot::command("msg", "$MODULE_NAME/relay_guild_inc.php", "grc", "all", "Relays incoming messages to guildchat");
  	bot::command("msg", "$MODULE_NAME/relay_guild_cfg.php", "guildrelay", "all", "Relay between guildchats.");
  	bot::command("priv", "$MODULE_NAME/relay_guild_cfg.php", "guildrelay", "all", "Relay between guildchats.");
	bot::regGroup("Guild_Relay", $MODULE_NAME, "Relay Chat between guilds", "grc", "guildrelay");
	bot::addsetting("relaybot", "Bot for Guildrelay", "noedit", "0", "none", '0', "mod", "$MODULE_NAME/relaybot_help.txt");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfiles
	bot::help("citycloak", "$MODULE_NAME/citycloak.txt", "guild", "Status of the citycloak", "Org Commands");
	bot::help("guildrelay", "$MODULE_NAME/guildrelay.txt", "guild", "How to relay chats between two guilds", "Org Commands");
	bot::help("news", "$MODULE_NAME/news.txt", "guild", "News", "Org Commands");
?>