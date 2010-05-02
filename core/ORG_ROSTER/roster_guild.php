<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Refresh/Create org memberlist
   ** Version: 1.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 16.01.2007
   ** 
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann
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

if($this->vars["my guild"] != "" && $this->vars["my guild id"] != "") {
	// Set Delay for notify on/off(prevent spam from org roster module)
	$this->vars["onlinedelay"] = time() + 60;
	
	echo "\n \nStarting Roster Update \n";
	//Get the org infos
	$org = new org($this->vars["my guild id"], $this->vars["dimension"], $force_update);
	
	//Check if Orgxml file is correct if not abort
	if($org->errorCode != 0) {
	  	echo "Error in getting the org roster xmlfile.\nPlease try again later.\n";
	} else {
		// Get Buddylist
		$buddies = $this->buddyList;
		
		// Remove the Data about the bot itself
		$copy = array_flip($org->member);
		unset($copy[ucfirst(strtolower($this->vars["name"]))]);
		unset($buddies[ucfirst(strtolower($this->vars["name"]))]);
		$org->member = array_flip($copy);
		
		//Delete old Memberslist
		unset($this->guildmembers);
		
		//Save the current org_members table in a var
		$db->query("SELECT * FROM org_members_<myname>");
		if($db->numrows() == 0 && (count($org->member) > 0))
			$restart = true;
		else {
			$restart = false;
			while($row = $db->fObject()) {
				$dbentrys[$row->name]["name"] = $row->name;
				$dbentrys[$row->name]["mode"] = $row->mode;
			}
		}
		
		//Get Guestlistbuddys
		$db->query("SELECT * FROM guests_<myname>");
		while($row = $db->fObject())
			$guests[$row->name] = true;
		
		//Start the transaction
		$db->beginTransaction();
		
		// Going through each member of the org and add his data�s
		foreach($org->member as $amember) {
			//If the orgmembers isn�t on buddylist add him
		    if(!isset($this->buddyList[$amember]))
		        bot::send("addbuddy", $amember);
		    
		    //If there exists already data about the player just update hum
			if($dbentrys[$amember]["mode"] != "") {
			  	if($dbentrys[$amember]["mode"] == "man" || $dbentrys[$amember]["mode"] == "org") {
			        $mode = "org";
		            $this->guildmembers[$amember] = $org->members[$amember]["rank_id"];
			  	} else 
		            $mode = "del";
		
		        $db->query("UPDATE org_members_<myname> SET `mode` = '".$mode."',
		                    `firstname` = '".str_replace("'", "''", $org->members[$amember]["firstname"])."',
		                    `lastname` = '".str_replace("'", "''", $org->members[$amember]["lastname"])."',
		                    `guild` = '".$org->orgname."',
		                    `profession` = '".$org->members[$amember]["profession"]."', 
		                    `rank_id`  = '".$org->members[$amember]["rank_id"]."',
		                    `rank` = '".$org->members[$amember]["rank"]."',
		                    `level` = '".$org->members[$amember]["level"]."',
		                    `ai_level` = '".$org->members[$amember]["ai_level"]."',
		                    `ai_rank` = '".$org->members[$amember]["ai_rank"]."',
		                    `gender` = '".$org->members[$amember]["gender"]."',
		                    `breed` = '".$org->members[$amember]["breed"]."'
		                    WHERE `name` = '".$org->members[$amember]["name"]."'");	  		
			//Else insert his data
			} else {
			    $db->query("INSERT INTO org_members_<myname> (`name`, `mode`, `firstname`, `lastname`, `guild`, `rank_id`, `rank`, `level`, `profession`, `gender`, `breed`, `ai_level`, `ai_rank`)
		                        VALUES ('".$org -> members[$amember]["name"]."', 'org',
		                        '".str_replace("'", "''", $org->members[$amember]["firstname"])."',
		                        '".str_replace("'", "''", $org->members[$amember]["lastname"])."', '".$org->orgname."',
		                        '".$org -> members[$amember]["rank_id"]."', '".$org->members[$amember]["rank"]."',
		                        '".$org -> members[$amember]["level"]."', '".$org->members[$amember]["profession"]."',
		                        '".$org -> members[$amember]["gender"]."', '".$org->members[$amember]["breed"]."',
		                        '".$org -> members[$amember]["ai_level"]."',
		                        '".$org -> members[$amember]["ai_rank"]."')");
				$this->guildmembers[$amember] = $org->members[$amember]["rank_id"];                        
		    }
		    unset($buddies[$amember]);    
		}
		
		//End the transaction
		$db->Commit();
		
		//Removing old data�s in the db and remove buddies if they exist
		$db->query("SELECT name FROM org_members_<myname> WHERE `mode` != 'man'");
		while($row = $db->fObject()) {
		    if(!$org->members[$row->name]) {
		        $db->query("DELETE FROM org_members_<myname> WHERE `name` = '".$row->name."'");
		        bot::send("rembuddy", $row->name);
		        unset($buddies[$row->name]);
		    }
		}

		// Removing buddies that are still left and not otherwise used
		foreach($buddies as $key => $value) {
		    if((!isset($guests[$key])) && (!isset($this->admins[$key])) && (!isset($this->members)) && ($dbentrys[$key]["mode"] != "man"))
				bot::send("rembuddy", $key);
		}
			
		echo "Org Roster Update is done. \n";
		
		if($restart == true) {
		  	echo "The bot needs to be restarted so be able to see who is online in your org.\n";
		  	echo "Automatically restarting in 10seconds.\n";
		  	sleep(10);
		  	die("The bot is restarting");
		}
	}
}
?>
