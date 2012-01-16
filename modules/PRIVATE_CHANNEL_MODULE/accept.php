<?php

if (preg_match("/^accept (.+)/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	if (!$chatBot->get_uid($name)) {
		$msg = "Character <highlight>$name<end> does not exist.";
	} else {
		$chatBot->privategroup_join($name);
		$msg = "Accepted private channel invitation from <highlight>$name<end>.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
