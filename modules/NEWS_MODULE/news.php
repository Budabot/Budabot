<?php

if (preg_match("/^news$/i", $message, $arr)) {
	$msg = News::getNews();
	if ($msg == '') {
		$msg = "No News recorded yet.";
	}
		
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>