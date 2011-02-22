<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $bbin_socket;
stream_set_blocking($bbin_socket, 0);
if (preg_match("/^onlineirc$/i", $message, $arr)) {
	fputs($bbin_socket, "NAMES :".$chatBot->settings['irc_channel']."\n");
	sleep(1);
	while ($data = fgets($bbin_socket)) {
		if (preg_match("/(End of \/NAMES list)/", $data, $discard)) {
			break;
		} else {
			$start = strrpos($data,":")+1;
			$names = explode(' ',substr($data,$start,strlen($data)));
			$numusers = count($names);
			forEach ($names as $value) {
				$list .= "$value\n";
			}
			
			$msg = Text::make_link("$numusers online in IRC",$list);
			
			$chatBot->send($msg, $sendto);
		}
		flush();
	}
}
?>
