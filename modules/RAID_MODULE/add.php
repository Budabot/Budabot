<?php

global $loot;

if (preg_match("/^add ([0-9]+)$/i", $message, $arr)) {
	$slot = $arr[1];
	$found = false;
	if (count($loot) > 0) {
		$slot = $arr[1];

		//Check if the slot exists
		if (!isset($loot[$slot])) {
			$msg = "The slot you trying to add in doesn't exist.";
			$chatBot->sendTell($msg, $sender);
			return;
		}

		//Check if minlvl is set and if the player is higher then it
		if (isset($loot[$slot]["minlvl"])) {
			$whois = Player::get_by_name($sender);
			if ($whois === null || $whois->lvl < $loot[$slot]["minlvl"]) {
			    $msg = "You need to be at least lvl<highlight>{$loot[$slot]["minlvl"]}<end> to join this roll.";
				$chatBot->sendTell($msg, $sender);
				return;
			}
		}

		//Remove the player from other slots if set
		$found = false;
		forEach ($loot as $key => $item) {
			if ($loot[$key]["users"][$sender] == true) {
				unset($loot[$key]["users"][$sender]);
				$found = true;
			}
		}

		//Add the player to the choosen slot
	    $loot[$slot]["users"][$sender] = true;

	    if ($found == false) {
		    $msg = "$sender has added to <highlight>\"{$loot[$slot]["name"]}\"<end>.";
		} else {
			$msg = "$sender has moved to <highlight>\"{$loot[$slot]["name"]}\"<end>.";
		}

		$chatBot->sendPrivate($msg);
	} else {
		$chatBot->sendTell("No list available where you can add in.", $sender);
	}
} else {
	$syntax_error = true;
}

?>
