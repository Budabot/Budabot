<?php

$MODULE_NAME = "RAFFLE_MODULE";
//Setup
bot::event("setup", "$MODULE_NAME/setup.php");

//raffle message
bot::regcommand("msg", "$MODULE_NAME/raffle.php", "raffle", "all");
bot::regcommand("priv", "$MODULE_NAME/raffle.php", "raffle", "all");
bot::regcommand("guild", "$MODULE_NAME/raffle.php", "raffle", "all");

//status
bot::regcommand("guild", "$MODULE_NAME/status.php", "rafflestatus", "all");
bot::regcommand("msg", "$MODULE_NAME/status.php", "raffleStatus", "all");

//join raffle
bot::regcommand("msg", "$MODULE_NAME/join.php", "joinRaffle", "all");

//leave raffle
bot::regcommand("msg", "$MODULE_NAME/leave.php", "leaveRaffle", "all");

//timer
bot::event("2sec", "$MODULE_NAME/check_winner.php", "raffle", "Checks to see if raffle is over");

//Help files
bot::help("Raffle", "$MODULE_NAME/raffle.txt", "guild", "Start/Join/Leave Raffles", "Raffles");

//Settings
bot::addsetting("defaultraffletime", "Sets how long the raffle should go for in minutes.", "edit", 3, "number");

?>