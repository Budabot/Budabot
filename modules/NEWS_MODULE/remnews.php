<?php

if (preg_match("/^remnews ([0-9]+)$/i", $message, $arr)) {
	$rows = $db->exec("DELETE FROM `#__news` WHERE `id` = {$arr[1]}");
	if ($rows == 0) {
		$msg = "No news entry found with the ID <highlight>{$arr[1]}<end>.";
	} else {
		$msg = "News entry with the ID <highlight>{$arr[1]}<end> was deleted successfully.";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>