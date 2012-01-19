<?php
	require_once 'News.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'News', new News);

	$db->add_table_replace('#__news', 'news');
	$db->loadSQLFile($MODULE_NAME, 'news');

	// Help files
	$help->register($MODULE_NAME, "news", "news.txt", "guild", "How to use news");
?>