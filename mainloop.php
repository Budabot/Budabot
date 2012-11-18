<?php

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

	// If the bot is running under Windows, use php.exe
	// and the Windows-specific php-win.ini, else use
	// PHP and the system default php.ini, if any, or a
	// local custom php.ini if it exists (hence the new
	// name for the Windows-specific ini-file).
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$php_exec = ".\win32\php.exe -c php-win.ini";
	} else {
		$php_exec = "php";
	}

	$php_file = "main.php";
	$config_file = $argv[1];

	// Handle the shutdown command.
	while (true) {
		$last_line = system("$php_exec -f $php_file -- $config_file");
		if (preg_match("/^The bot is shutting down.$/i", $last_line)) {
			die();
		}
	}

?>
