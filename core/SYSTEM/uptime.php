<?php

if (preg_match("/^uptime$/i", $message, $arr)) {
	$datediff = date_difference($this->vars['startup'], time());
	$msg = "The bot has been online for $datediff.";
	bot::send($msg, $sendto);
}

?>