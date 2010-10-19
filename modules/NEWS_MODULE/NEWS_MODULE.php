<?php
	$MODULE_NAME = "NEWS_MODULE";

	//Setup
	Event::register("setup", $MODULE_NAME, "setup.php");

	//News
    bot::event("logOn", "$MODULE_NAME/news_logon.php", "none", "Sends a tell with news to players logging in");
	bot::command("", "$MODULE_NAME/news.php", "news", "all", "Show News");
	bot::subcommand("", "$MODULE_NAME/news.php", "news (.+)", "guildadmin", "news", "Add News");
	bot::subcommand("", "$MODULE_NAME/news.php", "news del (.+)", "guildadmin", "news", "Delete a Newsentry");
	
	//Set admin and user news
	bot::command("", "$MODULE_NAME/set_news.php", "privnews", "rl", "Set news that are shown on privjoin");
	bot::command("", "$MODULE_NAME/set_news.php", "adminnews", "mod", "Set adminnews that are shown on privjoin");
	bot::addsetting("news", "no", "hide", "Not set.");
	bot::addsetting("adminnews", "no", "hide", "Not set.");

	//Help files
	bot::help("news", "$MODULE_NAME/news.txt", "guild", "How to use news");
	bot::help("priv_news", "$MODULE_NAME/priv_news.txt", "raidleader", "Set Privategroup News");
?>