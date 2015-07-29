<?php

// If the bot is running under Windows, use php.exe
// and the Windows-specific php-win.ini, else use
// PHP and the system default php.ini, if any, or a
// local custom php.ini if it exists (hence the new
// name for the Windows-specific ini-file)
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$php_exec = ".\win32\php.exe -c php-win.ini";
} else {
	$php_exec = "php";
}

$php_file = "main.php";
$config_file = $argv[1];

// Handle the shutdown command
while (true) {
	system("$php_exec -f $php_file -- $config_file", $returnVar);
	if ($returnVar == 10) {
		break;
	}
}

?>