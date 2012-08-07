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
	require_once 'AOUController.class.php';
	
	$chatBot->registerInstance($MODULE_NAME, 'AOUController', new AOUController());

	$command->register($MODULE_NAME, "", "guides.php", "guides", "all", "Guides for AO", "guides.txt");

	// aliases
	$commandAlias->register($MODULE_NAME, "guides breed", "breed");
	$commandAlias->register($MODULE_NAME, "guides healdelta", "healdelta");
	$commandAlias->register($MODULE_NAME, "guides lag", "lag");
	$commandAlias->register($MODULE_NAME, "guides nanodelta", "nanodelta");
	$commandAlias->register($MODULE_NAME, "guides stats", "stats");
	$commandAlias->register($MODULE_NAME, "guides buffs", "buffs");
	$commandAlias->register($MODULE_NAME, "guides title", "title");
?>
