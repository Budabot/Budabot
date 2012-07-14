<?php

if (preg_match("/^assist (.+)$/i", $message, $arr)) {
    $nameArray = explode(' ', $arr[1]);

	if (count($nameArray) == 1) {
		$name = ucfirst(strtolower($arr[1]));
		$uid = $chatBot->get_uid($name);
		if ($type == "priv" && !isset($chatBot->chatlist[$name])) {
			$msg = "Character <highlight>$name<end> is not in this bot.";
			$sendto->reply($msg);
		}

		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
			$sendto->reply($msg);
		}

		$link = "<a href='chatcmd:///macro $name /assist $name'>Click here to make an assist $name macro</a>";
		$chatBot->data['assist'] = Text::make_blob("Assist $name Macro", $link);
	} else {
		forEach ($nameArray as $key => $name) {
			$name = ucfirst(strtolower($name));
			$uid = $chatBot->get_uid($name);
			if ($type == "priv" && !isset($chatBot->chatlist[$name])) {
				$msg = "Character <highlight>$name<end> is not in this bot.";
				$sendto->reply($msg);
			}

			if (!$uid) {
				$msg = "Character <highlight>$name<end> does not exist.";
				$sendto->reply($msg);
			}
			$nameArray[$key] = "/assist $name";
		}

		// reverse array so that the first player will be the primary assist, and so on
		$nameArray = array_reverse($nameArray);
		$chatBot->data['assist'] = '/macro assist ' . implode(" \\n ", $nameArray);
	}

	$sendto->reply($chatBot->data['assist']);

	// send message 2 more times (3 total) if used in private channel
	if ($type == "priv") {
		$sendto->reply($chatBot->data['assist']);
		$sendto->reply($chatBot->data['assist']);
	}
} else {
	$syntax_error = true;
}

?>
