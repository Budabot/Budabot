<?php
$MODULE_NAME = "WEATHER_MODULE";
$PLUGIN_VERSION = 0.1;

bot::command("", "$MODULE_NAME/weather.php", "weather", "all", "View Weather");

bot::help("weather", "$MODULE_NAME/weather.txt", "guild", "Get weather info.", "Weather Module"); 
?>