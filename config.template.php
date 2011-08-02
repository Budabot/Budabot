<?php
/* ********************************************	*/
/* Config file for Budabot.                     */
/* ********************************************	*/

// Account info
$vars['login']      = "";
$vars['password']   = "";
$vars['name']       = "";
$vars['my_guild']   = "";

// 1 for Atlantean, 2 for Rimor, 3 for Die Nueue Welt, 4 for Test
$vars['dimension']  = 1;

// Administrator/Owner name
$vars['SuperAdmin'] = "";

// Database Information
$vars['DB Type'] = "Sqlite";	// What type of Database should be used? (Sqlite or Mysql)
$vars['DB Name'] = "budabot.db";	// Database Name
$vars['DB Host'] = "./data/";	// Hostname or File location.
$vars['DB username'] = "";		// Mysql User name
$vars['DB password'] = "";		// Mysql Password

// Logging options. 1 for enabled, 0 for disabled
$vars['error_console'] = 1;
$vars['error_file'] = 1;

$vars['info_console'] = 1;
$vars['info_file'] = 1;

$vars['query_console'] = 0;
$vars['query_file'] = 0;

$vars['debug_console'] = 0;
$vars['debug_file'] = 0;

$vars['chat_console'] = 1;
$vars['chat_file'] = 1;

// Show aoml markup in logs/console. 1 for enabled, 0 for disabled
$vars['show_aoml_markup'] = 0;

// Cache folder for storing org xml files
$vars['cachefolder'] = "./cache/";

// Default Status for modules. 1 for enabled, 0 for disabled
$settings['default_module_status'] = 0;

// AO Chat Proxy. 1 for enabled, 0 for disabled
$vars['use_proxy'] = 0;
$vars['proxy_server'] = "127.0.0.1";
$vars['proxy_port'] = 9993;

?>
