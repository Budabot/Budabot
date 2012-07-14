<?php
   /*
   ** Author: Neksus (RK2)
   ** Description: Spams a doh message in Guildchat
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.07.2006
   ** Date(last modified): 15.07.2006
   **
   */

if (preg_match("/^doh/i", $message)) {
	$doh = array(
		"Doh! DOH!! Hmm... Doh-nuts",
		"Doh Doh DOH!!!!",
		"DOH!",
		"Doh ey!",
		"Doh you say..I say Doh!!!");

	$sendto->reply(Util::rand_array_value($doh));
} else {
	$syntax_error = true;
}

?>
