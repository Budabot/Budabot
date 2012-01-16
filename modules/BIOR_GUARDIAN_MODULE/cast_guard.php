<?php

if (preg_match("/^g$/i", $message)) {
	$msg = "<blue>$sender casted Guardian on tank. 40 seconds remaining.<end>";
	$chatBot->data['guard'][$sender]["g"] = time() + 340;
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>