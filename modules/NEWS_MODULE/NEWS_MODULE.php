<?php
	$MODULE_NAME = "NEWS_MODULE";

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

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfiles
	bot::help("news", "$MODULE_NAME/news.txt", "guild", "News", "Org Commands");
?>