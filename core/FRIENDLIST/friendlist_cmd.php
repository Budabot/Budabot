<?php

if (preg_match("/^friendlist$/i", $message) || preg_match("/^friendlist (clean)$/i", $message, $arg)) {
	if ($arg) {
		$cleanup = true;
	}

	$chatBot->send("One moment... (".count($chatBot->buddyList)." names to check.)", $sendto);

	$orphanCount = 0;
	if (count($chatBot->buddyList) == 0) {
		$chatBot->send("Didn't find any names in the friendlist.", $sendto);
	} else {
		$blob = "<header> :::::: Friendlist :::::: <end>\n\n";
		forEach ($chatBot->buddyList as $key => $value) {
			$removed = '';
			if (count($value['types']) == 0) {
				$orphanCount++;
				if ($cleanup) {
					Buddylist::remove($value['name']);
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
				$blob .= Text::make_link('Remove Orphans', '/tell <myname> <symbol>friendlist clean', 'chatcmd');
			}
		}
		
		if ($cleanup) {
			$msg = Text::make_blob("Removed $orphanCount friends from the friendlist", $blob);
		} else {
			$msg = Text::make_blob("Friendlist Details", $blob);
		}
		$chatBot->send($msg, $sendto);
	}
} else if (preg_match("/^friendlist (.*)$/i", $message, $arg)) {
	$search = $arg[1];
	
	$chatBot->send("One momment... (".count($chatBot->buddyList)." names to check.)", $sendto);

	if (count($chatBot->buddyList) == 0) {
		$chatBot->send("Didn't find any names in the friendlist.", $sendto);
	} else {
		$count = 0;
		$blob = "Friendlist Search: '{$search}'\n\n";
		forEach ($chatBot->buddyList as $key => $value) {
			$removed = '';
			if (preg_match("/$search/i", $value['name'])) {
				$count++;
				$blob .= $value['name'] . " " . implode(' ', array_keys($value['types'])) . "\n";
			}
		}

		if ($count > 0) {
			$msg = Text::make_blob("Friendlist Search Details", $blob);
			$chatBot->send($msg, $sendto);
		} else {
			$chatBot->send("No friends on the friendlist found containing '$search'", $sendto);
		}
	}
}

?>