<?php

if (preg_match("/^uptime$/i", $message, $arr)) {
	$date_string = Util::unixtime_to_readable(time() - $this->vars['startup']);
	$msg = "The bot has been online for $date_string.";
	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>