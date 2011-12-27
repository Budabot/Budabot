<?php
	require_once 'News.class.php';
	
	$chatBot->registerInstance('News', new News);

	DB::add_table_replace('#__news', 'news');
	DB::loadSQLFile($MODULE_NAME, 'news');

	// News
    Event::register($MODULE_NAME, "logOn", "News.logon", "Sends a tell with news to players logging in");
	Command::register($MODULE_NAME, "", "News.newsCommand", "news", "all", "Show News");
	Subcommand::register($MODULE_NAME, "", "News.newsAddCommand", "news add (.+)", "rl", "news", "Add a news entry");
	Subcommand::register($MODULE_NAME, "", "News.newsRemCommand", "news rem (.+)", "rl", "news", "Remove a news entry");
	Subcommand::register($MODULE_NAME, "", "News.stickyCommand", "news sticky (.+)", "leader", "news", "Stickies a news entry");
	Subcommand::register($MODULE_NAME, "", "News.unstickyCommand", "news unsticky (.+)", "leader", "news", "Unstickies a news entry");
	
	// Set admin and user news
	Command::register($MODULE_NAME, "", "set_news.php", "privnews", "rl", "Set news shown on private channel join");
	Command::register($MODULE_NAME, "", "set_news.php", "adminnews", "mod", "Set news shown to admins on private channel join", 'privnews');
	Setting::add($MODULE_NAME, "news", "Current news", "hide", "text", "Not set.");
	Setting::add($MODULE_NAME, "adminnews", "Current admin news", "hide", "text", "Not set.");

	// Help files
	Help::register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
	Help::register($MODULE_NAME, "privnews", "privnews.txt", "rl", "How to set private channel and admin news entries");
?>