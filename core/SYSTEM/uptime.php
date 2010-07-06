<?php

if (preg_match("/^uptime$/i", $message, $arr)) {
	$datediff = date_difference($this->vars['startup'], time());
	$msg = "The bot has been online for $datediff.";
	$this->send($msg, $sendto);
}

?>