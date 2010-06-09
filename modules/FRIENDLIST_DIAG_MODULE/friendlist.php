<?php
   /*
   ** Author: Lucier (RK1)
   ** Description: Friendlist_Diag_Module (Shows why a name is on the friendslist)
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 30.06.2007
   ** Date(last modified): 30.06.2007
   */
 
if (preg_match("/^friendlist(.+)?$/i", $message, $arg)) {
	if ($arg[1] == " clean") {$cleanup = true;}
	
	bot::send("One momment... (".count($this->buddyList)." names to check.)", $sendto);
	$nickinfo = array();

	foreach ($this->buddyList as $key => $value) {
		
		// Checking for admin
		if (isset($this->admins[$key]["level"])) {
			$nickinfo[$key] .="<red>".$this->admins[$key]["level"]."<end>, ";
		}
		
		// Checking for Org
		if (isset($this->guildmembers[$key])) {
			$nickinfo[$key] .="<green>".$this->guildmembers[$key]."<end>, ";
		}
		
		// Checking for memberlist access
		$db->query("SELECT * FROM members_<myname> WHERE `name` = '$key'");
		if($db->numrows() > 0) {$nickinfo[$key] .="<white>M<end>, ";}
		
		if ($nickinfo[$key] == ""){$nickinfo[$key] = 0;}
		
		// if theres a trailing ', ' at the end, remove it.
		if (strlen($nickinfo[$key]) > 2) {$nickinfo[$key] = substr($nickinfo[$key],0,-2);}
		
	}
	
	if (count($nickinfo) == 0) {
		bot::send("Didn't find any names in the friendlist.", $sendto);
	} else {
		$msg = "KEY: <red>Admin<end>, <yellow>Guest channel<end>, <green>Org Member<end>, <white>Member list<end>\n\n";
		ksort($nickinfo);
		foreach ($nickinfo as $key => $value) {
			if ($value != "0") {
				$msg .= "$key ($value)\n";
			} else {
				if ($cleanup) {
					$uid = $this->get_uid($key);
					$this->buddy_remove($uid);
					unset($this->buddyList[$uid]);
				} else {
					$unknown .="$key\n";
				}
				$unknowncount++;
			}
		}
		
		if ($unknowncount && $cleanup) {$msg .="\nRemoved: ($unknowncount)";}
		elseif ($unknown) {$msg .= "\nUnknown: ($unknowncount) <a href='chatcmd:///tell <myname> <symbol>friendlist clean'>Remove Unknowns?</a>\n$unknown";}
		bot::send(bot::makeLink("Friendlist Details", $msg), $sendto);
	}
}
?>