<?php

if (preg_match("/^news add (.+)$/i", $message, $arr) || preg_match("/^news (.+)$/i", $message, $arr)) {
	$news = str_replace("'", "''", $arr[1]);
	$db->exec("INSERT INTO news (`time`, `name`, `news`) VALUES (".time().", '".$sender."', '$news')"); 
	$msg = "News has been added successfully.";

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>