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
   if(preg_match("/^pack ([ib][0-9]+)$/i", $message, $arr)){
   if(substr($arr[1], 0, 1)=="i")
   $location = "inventory";
   else
   $location = "bank";
   $arr = substr($arr[1], 1);
   $item_count = 0;
   $foundpack = false;
   
   		foreach ($xml->$location->children() as $backpack) {// Loops through until it finds backpack
			if($backpack->getName() == "backpack" && $backpack['id'] == $arr){
			$foundpack = true;
				if($location == "inventory"){
				$msg = "- Inventory\n";
				}else{
				$msg = "+ Inventory\n";
				$msg .= "- Bank\n";
				}
				$msg .= "   - Backpack #".$backpack['id']."\n";
					foreach ($backpack->children() as $item) {// Loops through items
						$msg .= "<tab><tab>> ".$this->makeItem($item['lowid'], $item['highid'], $item['ql'], $item['name'])." Item ID: ".$item['id']."\n";
						$item_count++;
					}
				if($location == "inventory")
				$msg .= "+ Bank\n";
				break;
			}	
		}
	
	if(!$foundpack){
	$link = "Could not find Backpack#".$arr." in ".ucwords($location);
	}elseif($item_count == 0){
	$link = "No items found in Backpack#".$arr." in ".ucwords($location);
	}else{
	$msg = $item_count." Items in Backpack#".$arr." in ".ucwords($location)."\n\n".$msg;
    $link = $this->makeLink($item_count." items found in \"Backpack #".$arr."\" in ".ucwords($location), $msg);
	}
          if($type == "msg")
        $this->send($link, $sender);
    elseif($type == "priv")
        $this->send($link);
	elseif($type == "guild")
		$this->send($link, "guild");
   }else{
         $msg = "Incorrect syntax! For more information /tell <myname> help.";
    if($type == "msg")
        $this->send($msg, $sender);
    elseif($type == "priv")
        $this->send($msg);
	elseif($type == "guild")
		$this->send($msg, "guild");   
   }
   }else{
      $msg = "File not found! Please contact an administrator.";
    if($type == "msg")
        $this->send($msg, $sender);
    elseif($type == "priv")
        $this->send($msg);
	elseif($type == "guild")
		$this->send($msg, "guild");
   }   
   ?>