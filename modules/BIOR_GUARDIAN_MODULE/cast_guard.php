<?php

if (preg_match("/^g$/i", $message)) {
	$msg = "<blue>$sender casted Guardian on tank. 40 seconds remaining.<end>";
	$chatBot->data['guard'][$sender]["g"] = time() + 340;
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>