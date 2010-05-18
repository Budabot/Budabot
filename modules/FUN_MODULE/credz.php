<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows a random credits message(Ported over from a bebot plugin written by Xenixa (RK1))
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
   
$credz = array(
	"*name*, get lost you begging fr00b!",
	"Credz! What do I look like a money tree *name*!!?",
	"Cyber with me and I might think about giving you some credits *name*.",
	"Sure *name* but there's a 100% Interest Rate compounded Hourly. Still interested?",
	"/me sneaks up and thumps *name* in the head with a roll of coins doing *dmg* points of melee damage!",
	"Sure, I would love some credits. How much you wanna give me?",
	"1 or 2 Credz?",
	"No credz for j00!!",
	"Ok, I'm feeling generous, here's *creds* for you *name*.",
	"Sorry, I'm flat ass broke hun. I'm just a Bot I don't get paid.",
	"/me does /ignore *name* ... damn beggers.",
	"No I don't think so. You still owe me *creds* credits from last time *name*!",
	"Sorry, I don't have any credits left. Would you like a RBP instead?");
	
if(preg_match("/^credz/i", $message)) {
	$dmg = rand(100,999);
    $cred = rand(10000,9999999);
	$randval = rand(1, sizeof($credz) - 1);
	$msg = $credz[$randval];
    $msg = str_replace("*name*", $sender, $msg);
    $msg = str_replace("*dmg*", $dmg, $msg);
    $msg = str_replace("*creds*", $cred, $msg);
	bot::send($msg, "guild");
}	
?>