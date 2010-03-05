<?php
$MODULE_NAME = "BREED_MODULE";
$PLUGIN_VERSION = 0.1;

//Breedcap
bot::command("guild", "$MODULE_NAME/breed.php", "breed", "all", "Shows Breedcaps.");
bot::command("msg", "$MODULE_NAME/breed.php", "breed", "all", "Shows Breedcaps.");
bot::command("priv", "$MODULE_NAME/breed.php", "breed", "all", "Shows Breedcaps.");

?>