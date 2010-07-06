<?php
	$MODULE_NAME = "NEWS_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//News
    bot::event("logOn", "$MODULE_NAME/news_logon.php", "none", "Show News on logon of members");  	
	bot::command("", "$MODULE_NAME/news.php", "news", MEMBER, "Show News");
	bot::subcommand("", "$MODULE_NAME/news.php", "news (.+)", GUILDADMIN, "news", "Add News");
	bot::subcommand("", "$MODULE_NAME/news.php", "news del (.+)", GUILDADMIN, "news", "Delete a Newsentry");

	//Help files
	bot::help("news", "$MODULE_NAME/news.txt", MEMBER, "News");
?>