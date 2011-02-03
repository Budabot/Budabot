<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
Setting::save("irc_status", 0);
if ($this->settings['irc_autoconnect'] == 1) {
	include 'irc_connect.php';
}
?>
