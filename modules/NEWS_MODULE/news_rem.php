<?php

if (preg_match("/^news rem ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	$rows = $db->exec("DELETE FROM `#__news` WHERE `id` = ?", $id);
	if ($rows == 0) {
		$msg = "No news entry found with the ID <highlight>{$id}<end>.";
	} else {
		$msg = "News entry <highlight>{$id}<end> was deleted successfully.";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>