<?php
	$MODULE_NAME = "WEATHER_MODULE";
	$PLUGIN_VERSION = 0.1;

	bot::command("", "$MODULE_NAME/weather.php", "weather", ALL, "View Weather");

	bot::help("weather", "$MODULE_NAME/weather.txt", ALL, "Get weather info.");
?>