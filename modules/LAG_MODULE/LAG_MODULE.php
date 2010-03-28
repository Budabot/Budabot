<?php
/**
 * Based off the Client Lag Tweaks module for VhaBot by Jurosik (RK1) - Modded by Naturalistic
 * Ported to Budabot by Tyrence(RK2)
 * Can be downloaded from: http://www.box.net/shared/mkbu070y7f
 */

$MODULE_NAME = "LAG_MODULE";
$PLUGIN_VERSION = 1.0;

//Breedcap
bot::command("guild", "$MODULE_NAME/lag.php", "lag", "all", "Shows options you can use to reduce lag");
bot::command("msg", "$MODULE_NAME/lag.php", "lag", "all", "Shows options you can use to reduce lag");
bot::command("priv", "$MODULE_NAME/lag.php", "lag", "all", "Shows options you can use to reduce lag");

?>