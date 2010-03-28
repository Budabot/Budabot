<?php
$MODULE_NAME = "AUTO_WAVE_MODULE";
bot::command("guild","$MODULE_NAME/start.php", "startraid");
bot::command("guild","$MODULE_NAME/stopraid.php", "stopraid");
bot::event("setup", "$MODULE_NAME/setup.php");
bot::event("guild", "$MODULE_NAME/start.php");
bot::event("2sec", "$MODULE_NAME/counter.php");
?>