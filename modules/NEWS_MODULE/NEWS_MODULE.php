<?php
	require_once 'News.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'News', new News);

	$db->add_table_replace('#__news', 'news');
	$db->loadSQLFile($MODULE_NAME, 'news');

	// Set admin and user news
	$command->register($MODULE_NAME, "", "set_news.php", "privnews", "mod", "Set news shown on private channel join");
	$command->register($MODULE_NAME, "", "set_news.php", "adminnews", "mod", "Set news shown to admins on private channel join", 'privnews');

	// Help files
	$help->register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
	$help->register($MODULE_NAME, "privnews", "privnews.txt", "mod", "How to set private channel and admin news entries");
?>