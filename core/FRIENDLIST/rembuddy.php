<?php

if (preg_match("/^rembuddy (.+) (.+)$/i", $message, $arr)) {
	$name = $arr[1];
	$type = $arr[2];

	if ($buddylistManager->remove($name, $type)) {
		$msg = "$name removed from the buddy list successfully.";
	} else {
		$msg = "Could not remove $name from the buddy list.";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
