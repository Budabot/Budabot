<?php

	/* ********************************************	*/
	/* Configuration file for Budabot.              */
	/* ********************************************	*/

	/*
	 ** This file is part of Budabot.
	 **
	 ** Budabot is free software: you can redistribute it and/org modify
	 ** it under the terms of the GNU General Public License as published by
	 ** the Free Software Foundation, either version 3 of the License, or
	 ** (at your option) any later version.
	 **
	 ** Budabot is distributed in the hope that it will be useful,
	 ** but WITHOUT ANY WARRANTY; without even the implied warranty of
	 ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 ** GNU General Public License for more details.
	 **
	 ** You should have received a copy of the GNU General Public License
	 ** along with Budabot. If not, see <http://www.gnu.org/licenses/>.
	*/

	// Account information.
	$vars['login']      = "";
	$vars['password']   = "";
	$vars['name']       = "";
	$vars['my_guild']   = "";

	// 1 for Atlantean, 2 for Rimor, 4 for Test Live.
	$vars['dimension']  = 1;

	// Character name of the Super Administrator.
	$vars['SuperAdmin'] = "";

	// Database information.
	$vars['DB Type'] = "sqlite";		// What type of database should be used? ('sqlite' or 'mysql')
	$vars['DB Name'] = "budabot.db";	// Database name
	$vars['DB Host'] = "./data/";		// Hostname or file location
	$vars['DB username'] = "";			// MySQL username
	$vars['DB password'] = "";			// MySQL password

	// Show AOML markup in logs/console? 1 for enabled, 0 for disabled.
	$vars['show_aoml_markup'] = 0;

	// Cache folder for storing organization XML files.
	$vars['cachefolder'] = "./cache/";

	// Default status for new modules? 1 for enabled, 0 for disabled.
	$vars['default_module_status'] = 0;

	// Use AO Chat Proxy? 1 for enabled, 0 for disabled.
	$vars['use_proxy'] = 0;
	$vars['proxy_server'] = "127.0.0.1";
	$vars['proxy_port'] = 9993;

	// API module configuration
	$vars['API Port'] = 5250;

	// Define additional paths from where Budabot should load modules at startup
	$vars['module_load_paths'] = array(
		'./modules'
	);

	// This should only be enabled during development and only if you understand the implications
	// seen here: <http://code.google.com/p/budabot2/wiki/CompilingRunkit>
	define("USE_RUNKIT_CLASS_LOADING", false);

?>
