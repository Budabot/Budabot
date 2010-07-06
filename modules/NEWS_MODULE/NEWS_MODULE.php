<?php
	$MODULE_NAME = "NEWS_MODULE";
	$PLUGIN_VERSION = 1.0;
	
	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");

	//News
    $this->event("logOn", "$MODULE_NAME/news_logon.php", "none", "Show News on logon of members");  	
	$this->command("", "$MODULE_NAME/news.php", "news", MEMBER, "Show News");
	$this->subcommand("", "$MODULE_NAME/news.php", "news (.+)", GUILDADMIN, "news", "Add News");
	$this->subcommand("", "$MODULE_NAME/news.php", "news del (.+)", GUILDADMIN, "news", "Delete a Newsentry");

	//Help files
	$this->help("news", "$MODULE_NAME/news.txt", MEMBER, "News");
?>