<?php

if (preg_match("/^friendlist$/i", $message) || preg_match("/^friendlist (clean)$/i", $message, $arg)) {
	if ($arg) {
		$cleanup = true;
	}

	$orphanCount = 0;
	if (count($buddylistManager->buddyList) == 0) {
		$sendto->reply("The are no characters on the friendlist.");
	} else {
		$count = 0;
		forEach ($buddylistManager->buddyList as $key => $value) {
			if (!isset($value['name'])) {
				// skip the buddies that have been added but the server hasn't sent back an update yet
				continue;
			}

			$count++;
			$removed = '';
			if (count($value['types']) == 0) {
				$orphanCount++;
				if ($cleanup) {
					$buddylistManager->remove($value['name']);
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
				$blob .= Text::make_chatcmd('Remove Orphans', '/tell <myname> <symbol>friendlist clean');
			}
		}

		if ($cleanup) {
			$sendto->reply("Removed $orphanCount friends from the friendlist.");
		}
		$msg = Text::make_blob("Friendlist ($count)", $blob);
		$sendto->reply($msg);
	}
} else if (preg_match("/^friendlist (.*)$/i", $message, $arg)) {
	$search = $arg[1];

	if (count($buddylistManager->buddyList) == 0) {
		$sendto->reply("Didn't find any names in the friendlist.");
	} else {
		$count = 0;
		$blob = "Friendlist Search: '{$search}'\n\n";
		forEach ($buddylistManager->buddyList as $key => $value) {
			$removed = '';
			if (preg_match("/$search/i", $value['name'])) {
				$count++;
				$blob .= $value['name'] . " " . implode(' ', array_keys($value['types'])) . "\n";
			}
		}

		if ($count > 0) {
			$msg = Text::make_blob("Friendlist Search ($count)", $blob);
			$sendto->reply($msg);
		} else {
			$sendto->reply("No friends on the friendlist found containing '$search'");
		}
	}
}

?>
