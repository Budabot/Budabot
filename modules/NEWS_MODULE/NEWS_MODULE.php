<?php
	$MODULE_NAME = "NEWS_MODULE";

	DB::add_table_replace('#__news', 'news');
	DB::loadSQLFile($MODULE_NAME, 'news');

	// News
    Event::register($MODULE_NAME, "logOn", "news_logon.php", "none", "Sends a tell with news to players logging in");
	Command::register($MODULE_NAME, "", "news.php", "news", "all", "Show News");
	Command::register($MODULE_NAME, "", "addnews.php", "addnews", "guildadmin", "Add a News entry", 'news');
	Command::register($MODULE_NAME, "", "remnews.php", "remnews", "guildadmin", "Remove a News entry", 'news');

	// Set admin and user news
	Command::register($MODULE_NAME, "", "set_news.php", "privnews", "rl", "Set news that is shown on privjoin");
	Command::register($MODULE_NAME, "", "set_news.php", "adminnews", "mod", "Set adminnews that is shown on privjoin", 'privnews');
	Setting::add($MODULE_NAME, "news", "Current news", "hide", "text", "Not set.");
	Setting::add($MODULE_NAME, "adminnews", "Current admin news", "hide", "text", "Not set.");

	// Help files
	Help::register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
	Help::register($MODULE_NAME, "privnews", "privnews.txt", "raidleader", "How to set private channel and admin news entries");
?>