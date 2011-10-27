<?php

if (preg_match("/^news$/i", $message, $arr)) {
	$db->query("SELECT * FROM `#__news` ORDER BY `time` DESC LIMIT 0, 10");
	$data = $db->fObject('all');
	if (count($data) != 0) {
		$link = "<header> :::::: News :::::: <end>\n\n";
		forEach ($data as $row) {
		  	if (!$updated) {
				$updated = $row->time;
			}
			
		  	$link .= "<highlight>Date:<end> ".date("dS M, H:i", $row->time)."\n";
		  	$link .= "<highlight>Author:<end> $row->name\n";
		  	$link .= "<highlight>Options:<end> ".Text::make_chatcmd("Remove", "/tell <myname> news rem $row->id")."\n";
		  	$link .= "<highlight>Message:<end> $row->news\n\n";
		}
		$msg = Text::make_blob("News", $link)." [Last updated at ".date("dS M, H:i", $updated)."]";
	} else {
		$msg = "No News recorded yet.";
	}
		
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>