<?php
	 /*
   ** Author: Mindrila (RK1)
   ** Credits: Legendadv (RK2)
   ** BUDABOT IRC NETWORK MODULE
   ** Version = 0.1
   ** Developed for: Budabot(http://budabot.com)
   **
   */
// create the chatlist table in a similar fashion to ONLINE_MODULE with dimension addition
$db->query("CREATE TABLE IF NOT EXISTS bbin_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `faction` CHAR(10), `profession` CHAR(20), `guild` CHAR(255), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0', `guest` INT DEFAULT '0', `dimension` INT DEFAULT '0', `ircrelay` CHAR(25))");
?>