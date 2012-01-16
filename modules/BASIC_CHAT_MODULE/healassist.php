<?php

if (preg_match("/heal$/i", $message)) {
  	if (isset($chatBot->data['heal_assist'])) {
	  	$link = "<a href='chatcmd:///macro {$chatBot->data['heal_assist']} /assist {$chatBot->data['heal_assist']}'>Click here to make a heal assist macro on {$chatBot->data['heal_assist']}</a>";
		$msg = Text::make_blob("Current Healassist is {$chatBot->data['heal_assist']}", $link);
	} else {
		$msg = "No Healassist set atm.";
	}
	$chatBot->send($msg, 'priv');
} else {
	$syntax_error = true;
}
?>