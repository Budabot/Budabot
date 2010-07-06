<?php

if (preg_match("/^rembuddy (.+) (.+)$/i", $message, $arr)) {
	$name = $arr[1];
	$type = $arr[2];
	
	if ($this->remove_buddy($name, $type)) {
		$msg = "$name removed from the buddy list successfully.";
	} else {
		$msg = "Could not remove $name from the buddy list.";
	}
	
	$this->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>