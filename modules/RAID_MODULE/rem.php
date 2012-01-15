<?php

global $loot;

if (preg_match("/^rem$/i", $message)) {
	if (count($loot) > 0) {
	  	forEach ($loot as $key => $item) {
			if ($loot[$key]["users"][$sender] == true) {
				unset($loot[$key]["users"][$sender]);
			}
		}
	
		$msg = "$sender has been removed from all rolls.";
	  	$chatBot->send($msg, 'priv');
	} else {
		$chatBot->send("There is nothing to remove you from.", $sender);
	}
} else {
	$syntax_error = true;
}

?>