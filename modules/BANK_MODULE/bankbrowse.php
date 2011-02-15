<?php
   /*
   ** Author: Iamzipfile (RK2)
   ** Description: Searches a premade XML file with a tree of the contents of the orgs bank character
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.08.2009
   ** Date(last modified): 24.08.2009
   **
   ** The budabot bank module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 3 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** For more information please visit http://www.gnu.org/licenses/gpl-3.0.txt
   */
   
if($xml = simplexml_load_file("modules/BANK_MODULE/bank.xml")){
	if(preg_match("/^bank$/i", $message)){
		$item_count = 0;
		$backpack_count = 0;
		$msg = "";
		foreach ($xml->children() as $base_container) {// Loops through inventory and bank
			$msg .= "- ".ucwords($base_container->getName())."\n";
			$packprelude = substr($base_container->getName(), 0, 1);
			foreach ($base_container->children() as $base_slot) {// Loops through items and backpacks
				if($base_slot->getName()=='item'){
					$msg .= "<tab>> ".Text::make_item($base_slot['lowid'], $base_slot['highid'], $base_slot['ql'], $base_slot['name'])." Item ID: ".$base_slot['id']."\n";
					$item_count++;
				}elseif($base_slot->getName()=='backpack'){
					$backpack_inside_count = 0;
					$backpack_count++;
					foreach ($base_slot->children() as $item) {// Loops through items in backpacks
						$backpack_inside_count++;
						$item_count++;
					}
					if($backpack_inside_count)
					$msg .= "<tab>+ ".Text::make_link("Backpack #".$base_slot['id'], "/tell <myname> pack ".$packprelude.$base_slot['id'], "chatcmd")." Contains ".$backpack_inside_count." items\n";
					else
					$msg .= "<tab>- Backpack #".$base_slot['id']." Is empty\n";
				}
			}
		}
		$msg = $item_count." Items in total, ".$backpack_count." Backpacks in total.\n\n".$msg;
		$link = Text::make_link("Click to browse the org bank", $msg);
		$chatBot->send($link, $sendto);
	}else{
		$msg = "Incorrect syntax! For more information /tell <myname> help.";
		$chatBot->send($msg, $sendto);
	}
}else{
	$msg = "File not found! Please contact an administrator.";
	$chatBot->send($msg, $sendto);
}   

?>