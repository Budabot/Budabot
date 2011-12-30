<?php
/*
   ** Author: Plugsz (RK1)
   ** Description: Guides
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.21.2006
   ** Date(last modified): 12.21.2006
   ** 
   ** Copyright (C) 2006 Donald Vanatta
   **
   ** Licence Infos: 
   ** This file is for use with Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
	require_once 'functions.php';

	$command->register($MODULE_NAME, "", "guides.php", "guides", "all", "Guides for AO");
	$command->register($MODULE_NAME, "", "aou.php", "aou", "all", "Search for or view a guide from AO-Universe");
	
	// aliases
	CommandAlias::register($MODULE_NAME, "guides breed", "breed");
	CommandAlias::register($MODULE_NAME, "guides healdelta", "healdelta");
	CommandAlias::register($MODULE_NAME, "guides lag", "lag");
	CommandAlias::register($MODULE_NAME, "guides nanodelta", "nanodelta");
	CommandAlias::register($MODULE_NAME, "guides stats", "stats");
	CommandAlias::register($MODULE_NAME, "guides buffs", "buffs");
	CommandAlias::register($MODULE_NAME, "guides title", "title");
	
	Help::register($MODULE_NAME, "guides", "guides.txt", "all", 'How to use guides');
	Help::register($MODULE_NAME, "aou", "aou.txt", "all", "How to find a guide from AO-Universe");
?>