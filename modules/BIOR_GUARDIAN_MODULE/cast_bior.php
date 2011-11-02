<?php

if (preg_match("/^b$/i", $message)) {
	$msg = "<blue>$sender casted Bio Regrowth on tank. 30 seconds remaining.<end>";
	$chatBot->data['bior'][$sender]["b"] = time() + 330;
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>