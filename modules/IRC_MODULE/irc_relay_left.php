<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if($this->settings['irc_status'] = "1") {
	if($type == "leavePriv") {
		flush();
		fputs($socket, "PRIVMSG ".$this->settings['irc_channel']." :$sender has left the private chat.\n");
		if($this->settings['irc_debug_messages'] == 1) {
			echo("[".date('H:i')."] [Out. IRC Msg.] $sender has left the channel\n");
		}
	}
	elseif($type == "logOff" && isset($this->guildmembers[$sender])) {
		flush();
		fputs($socket, "PRIVMSG ".$this->settings['irc_channel']." :$sender has logged off.\n");
		if($this->settings['irc_debug_messages'] == 1) {
			echo("[".date('H:i')."] [Out. IRC Msg.] $sender has left the channel\n");
		}	
	}
}

?>