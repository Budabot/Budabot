<?php
	require_once 'News.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'News', new News);

	DB::add_table_replace('#__news', 'news');
	DB::loadSQLFile($MODULE_NAME, 'news');

	// Set admin and user news
	Command::register($MODULE_NAME, "", "set_news.php", "privnews", "rl", "Set news shown on private channel join");
	Command::register($MODULE_NAME, "", "set_news.php", "adminnews", "mod", "Set news shown to admins on private channel join", 'privnews');

	// Help files
	Help::register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
	Help::register($MODULE_NAME, "privnews", "privnews.txt", "rl", "How to set private channel and admin news entries");
?>