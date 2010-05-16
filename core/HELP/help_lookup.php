<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Shows a specific Helpfile
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 16.06.2006
   ** 
   ** Copyright (C) 2005, 2006 J. Gracik
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

$helpcmd = strtolower($arr[1]);
$found = false;
foreach($this->helpfiles as $key1 => $value1) {
	foreach($value1 as $key2 => $value2){  
	  	if($key2 == $helpcmd) {
			$filename = $this->helpfiles[$key1][$key2]["filename"];
			$admin = $this->helpfiles[$key1][$key2]["admin level"];
			$found = true;
			break;	    
		}
	}
	
	if($found == true)
		break;
}

if($found == false) {
	bot::send("No help found on this topic.", $sendto);
	return;
}

$restricted = true;
switch($admin) {
	case "guild":
		if(isset($this->guildmembers[$sender]))
			$restricted = false;
	break;
	case "guildadmin":
		if($this->guildmembers[$sender] <= $this->settings['guild admin level'])
			$restricted = false;	
	break;
	case "1":
	case "2":
	case "3":
		if($this->admins[$sender]["level"] >= $admin)
			$restricted = false;
	break;
	default:
	case "all":
		$restricted = false;
	break;
}

if(($help = fopen($filename, "r")) && ($restricted == false)){
	while (!feof ($help))
		$data .= fgets ($help, 4096);
	fclose($help);
	$arr[1] = ucfirst($arr[1]);	
	$msg = bot::makeLink("Help($arr[1])", $data);
} else {
	$msg = "No help found on this topic.";
}

bot::send($msg, $sendto);
?>