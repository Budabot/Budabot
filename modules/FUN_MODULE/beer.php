<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows a random beer message(Ported over from a bebot plugin written by Xenixa (RK1))
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.07.2006
   ** Date(last modified): 24.07.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
$beer[0] = "*name* are you buying?";
$beer[1] = "Beer! lets get this party started *name*!!";
$beer[2] = "WTF!? ... why you trying to steel my beer!";
$beer[3] = "A round of beer coming up courtesy of *name*.";
$beer[4] = "/me sneaks up and smashes a bottle over *name*'s head doing *dmg* points of melee damage! w00t drunken PvP!";
$beer[5] = "Have one on the house *name* and tell me all your problems";
$beer[6] = "Sure, I would love to drink a few. Your place or mine?";
$beer[7] = "Import or Domestic hun?";
$beer[8] = "Sorry, I just ran out hun. Would you like some Rising Sun Sake instead?";
$beer[9] = "Umm, I dont think you are of age, lets see some ID.";
$beer[10] = "Well, by the looks of it. I think you have reached your limit for the night *name*.";
$beer[11] = "NO!! First pay off your bar tab. You still owe me *creds* credits *name*!";
$beer[12] = "YEAH! Let's start gathering for a Pub raid!!";
$beer[13] = "Only Leet's are drinking beer! We need harder stuff like Wodka!";
	
if(preg_match("/^beer/i", $message)) {
	$dmg = rand(100,999);
    $cred = rand(10000,9999999);
	$randval = rand(1, sizeof($beer) - 1);
	$msg = $beer[$randval];
    $msg = str_replace("*name*", $sender, $msg);
    $msg = str_replace("*dmg*", $dmg, $msg);
    $msg = str_replace("*creds*", $cred, $msg);
	bot::send($msg, $sendto);
}
?>