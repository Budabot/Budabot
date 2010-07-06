<?php
	$MODULE_NAME = "WEATHER_MODULE";
	$PLUGIN_VERSION = 0.1;

	$this->command("", "$MODULE_NAME/weather.php", "weather", ALL, "View Weather");

	$this->help("weather", "$MODULE_NAME/weather.txt", ALL, "Get weather info.");
?>