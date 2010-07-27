<?php
	$MODULE_NAME = "WEATHER_MODULE";

	bot::command("", "$MODULE_NAME/weather.php", "weather", "all", "View Weather");

	bot::help("weather", "$MODULE_NAME/weather.txt", "guild", "Get weather info.", "Weather Module"); 
?>