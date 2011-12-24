<?php

if (preg_match("/^news$/i", $message, $arr)) {
	$data = $db->query("SELECT * FROM `#__news` WHERE `sticky` = 1 ORDER BY `time` DESC UNION SELECT * FROM `#__news` WHERE `sticky` = 0 ORDER BY `time` DESC LIMIT 10");
	if (count($data) != 0) {
		$link = "<header> :::::: News :::::: <end>\n\n";
		forEach ($data as $row) {
		  	if (!$updated) {
				$updated = $row->time;
			}
	
		  	$link .= "<highlight>Date:<end> ".date("dS M, H:i", $row->time)."\n";
		  	$link .= "<highlight>Author:<end> $row->name\n";
		  	$link .= "<highlight>Options:<end> ".Text::make_chatcmd("Remove", "/tell <myname> news rem $row->id")." | ";
			if ($row->sticky == 1) {
				$link .= Text::make_chatcmd("Unsticky", "/tell <myname> news unsticky $row->id")."\n";
			} else if ($row->sticky == 0) {
				$link .= Text::make_chatcmd("Sticky", "/tell <myname> news sticky $row->id")."\n";
			}
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