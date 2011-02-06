<?php
	$MODULE_NAME = "WEATHER_MODULE";

	bot::command("", "$MODULE_NAME/weather.php", "weather", "all", "View Weather");

	Help::register($MODULE_NAME, "weather", "weather.txt", "guild", "Get weather info");
?>