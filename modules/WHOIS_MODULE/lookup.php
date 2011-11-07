<?php

if (preg_match("/^lookup (.*)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($name);

	$msg = "Uid for '$name' is: '$uid'";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>