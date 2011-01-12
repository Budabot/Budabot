<?php
/////////////////////////////////////////////////
/* ********************************************	*/
/* Config file for the bot.                     */
/* To change this settings you can use the      */
/* Ingame Commands(config/settings) for it      */
/* but change these file only when you          */
/* know what you are doing.                     */
/* ********************************************	*/

// Enter your Account info here
$vars['login']		= "";
$vars['password']	= "";
$vars['name']		= "";
$vars['my guild']	= "";

// Enter 1 for Atlantean, 2 for Rimor, 3 for Die Nueue Welt
$vars['dimension']	= 1;

// Insert the Administrator name here
$settings['Super Admin'] = "";

// Default Delay for crons after bot is connected
$settings['CronDelay'] = 0;

// List of characters the bot should ignore (multiple names should be separated by semicolons ';')
$settings['Ignore'] = "";

// Database Information
$settings['DB Type'] = "Sqlite";	// What type of Database should be used? (Sqlite or Mysql)
$settings['DB Name'] = "budabot.db";	// Database Name
$settings['DB Host'] = "./data/";	// Hostname or File location.
$settings['DB username'] = "";		// Mysql User name
$settings['DB password'] = "";		// Mysql Password

// Logging options.  1 for enabled, 2 for disabled
$vars['error_console'] = 1;
$vars['error_file'] = 1;

$vars['info_console'] = 1;
$vars['info_file'] = 0;

$vars['query_console'] = 0;
$vars['query_file'] = 0;

$vars['debug_console'] = 0;
$vars['debug_file'] = 0;

$vars['chat_console'] = 1;
$vars['chat_file'] = 0;

// Show aoml markup (formatting and blobs) in logs/console
$vars['show_aoml_markup'] = 0;

// Cache folder for storing org xml files
$vars['cachefolder'] = "./cache/";

// Default Status for modules
// 0 = Modules are disabled by default
// 1 = Modules are enabled by default
$settings['default module status'] = 0;

// Maximum chars for one window(blob) in bytes
$settings['max_blob_size'] = 7500;
////////////////////////////////////////////////
?>
