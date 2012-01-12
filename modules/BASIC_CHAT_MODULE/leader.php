<?php

if ($setting->get("leaderecho") == 1) {
	$status = "<green>Enabled<end>";
	$cmd = "off";
} else {
	$status = "<red>Disabled<end>";
	$cmd = "on";
}

$accessLevel = Registry::getInstance('accessLevel');

if (preg_match("/^leader$/i", $message)) {
  	if ($chatBot->data["leader"] == $sender) {
		unset($chatBot->data["leader"]);
	  	$msg = "Leader cleared.";
	} else if ($chatBot->data["leader"] != "") {
		if ($accessLevel->compareCharacterAccessLevels($sender, $chatBot->data["leader"])) {
  			$chatBot->data["leader"] = $sender;
		  	$msg = "{$sender} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
		} else {
			$msg = "You can't take leader from <highlight>{$chatBot->data["leader"]}<end>.";
		}
	} else {
		$chatBot->data["leader"] = $sender;
	  	$msg = "{$sender} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
	}
  	$chatBot->send($msg, 'priv');

} else {
	$syntax_error = true;
}

?>