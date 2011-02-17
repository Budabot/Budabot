<?php
	$MODULE_NAME = "NEWS_MODULE";

	//Setup
	Event::register($MODULE_NAME, "setup", "setup.php");

	//News
    Event::register($MODULE_NAME, "logOn", "news_logon.php", "none", "Sends a tell with news to players logging in");
	Command::register($MODULE_NAME, "", "news.php", "news", "all", "Show News");
	Subcommand::register($MODULE_NAME, "", "news.php", "news (.+)", "guildadmin", "news", "Add News");
	Subcommand::register($MODULE_NAME, "", "news.php", "news del (.+)", "guildadmin", "news", "Delete a News entry");

	//Set admin and user news
	Command::register($MODULE_NAME, "", "set_news.php", "privnews", "rl", "Set news that are shown on privjoin");
	Command::register($MODULE_NAME, "", "set_news.php", "adminnews", "mod", "Set adminnews that are shown on privjoin");
	Setting::add($MODULE_NAME, "news", "no", "hide", "text", "Not set.");
	Setting::add($MODULE_NAME, "adminnews", "no", "hide", "text", "Not set.");

	//Help files
	Help::register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
	Help::register($MODULE_NAME, "priv_news", "priv_news.txt", "raidleader", "Set Private channel News");
?>