<?php

if (preg_match("/^heal (.+)$/i", $message, $arr)) {
    $nameArray = explode(' ', $arr[1]);
	
	if (count($nameArray) == 1) {
		$name = ucfirst(strtolower($arr[1]));
		$uid = $chatBot->get_uid($name);
		
		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
			$sendto->reply($msg);
		}

		$link = "<a href='chatcmd:///macro heal /assist $name'>Click here to make a heal assist macro</a>";
		$chatBot->data['heal_assist'] = Text::make_blob("Heal Assist Macro", $link);
	} else {
		forEach ($nameArray as $key => $name) {
			$name = ucfirst(strtolower($name));
			$uid = $chatBot->get_uid($name);
			
			if (!$uid) {
				$msg = "Character <highlight>$name<end> does not exist.";
				$sendto->reply($msg);
			}
			$nameArray[$key] = "/assist $name";
		}
		
		// reverse array so that the first player will be the primary assist, and so on
		$nameArray = array_reverse($nameArray);
		$chatBot->data['heal_assist'] = '/macro heal ' . implode(" \\n ", $nameArray);
	}
	
	$sendto->reply($chatBot->data['heal_assist']);
	
	// send message 2 more times (3 total) if used in private channel
	if ($type == "priv") {
		$sendto->reply($chatBot->data['heal_assist']);
		$sendto->reply($chatBot->data['heal_assist']);
	}
} else {
	$syntax_error = true;
}

?>