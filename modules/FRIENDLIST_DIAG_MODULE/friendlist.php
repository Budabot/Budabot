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
	if ($arg[1] == " clean") {
		$cleanup = true;
	}
	
	bot::send("One momment... (".count($this->buddyList)." names to check.)", $sendto);
	
	$orphanCount = 0;
	if (count($this->buddyList) == 0) {
		bot::send("Didn't find any names in the friendlist.", $sendto);
	} else {
		$blob = "Buddy List\n\n";
		forEach ($this->buddyList as $key => $value) {
			if (count($value['types']) == 0) {
				$orphanCount++;
				if ($cleanup) {
					$this->remove_buddy($value['name']);
					$removed = "<red>REMOVED<end>";
				}
			}

			$blob .= $value['name'] . " $removed " . implode(' ', array_keys($value['types'])) . "\n";
		}

		if ($cleanup) {
			$blob .="\n\nRemoved: ($orphanCount)";
		} else {
			$blob .= "\n\nUnknown: ($orphanCount) ";
			if ($orphanCount > 0) {
				$blob .= $this->makeLink('Remove Orphans', '/tell <myname> <symbol>friendlist clean', 'chatcmd');
			}
		}
		bot::send(bot::makeLink("Friendlist Details", $blob), $sendto);
	}
}
?>