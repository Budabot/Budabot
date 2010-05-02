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

$doh[0]="Doh! DOH!! Hmm... Doh-nuts";
$doh[1]="Doh Doh DOH!!!!";
$doh[2]="DOH!";
$doh[3]="Doh ey!";
$doh[4]="Doh you say..I say Doh!!!";
	
if(eregi("^doh$", $message)) {
	$randval = rand(1, sizeof($beer) - 1);
	$msg = $doh[$randval];
	bot::send($msg, "guild");
}
?>