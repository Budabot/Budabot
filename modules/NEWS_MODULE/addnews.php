<?php

if (preg_match("/^addnews (.+)$/si", $message, $arr)) {
	$news = str_replace("'", "''", $arr[1]);
	$db->exec("INSERT INTO `#__news` (`time`, `name`, `news`) VALUES (".time().", '".$sender."', '$news')"); 
	$msg = "News has been added successfully.";

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>