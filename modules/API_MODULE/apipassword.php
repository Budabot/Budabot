<?php

if (preg_match("/^apipassword (.*)$/i", $message, $arr)) {
	$password = $arr[1];
	
	Preferences::save($sender, 'apipassword', $password);
	$chatBot->send("Your API password has been updated successfully.", $sendto);
} else {
	$syntax_error = true;
}

?>