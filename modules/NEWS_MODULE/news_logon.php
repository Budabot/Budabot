<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	$data = $db->query("SELECT * FROM `#__news` ORDER BY `time` DESC LIMIT 0, 10");
	if (count($data) != 0) {
		$link = "<header> :::::: News :::::: <end>\n\n";
		forEach ($data as $row) {
		  	if (!$updated) {
				$updated = $row->time;
			}
		  	$link .= "<highlight>Date:<end> ".date("dS M, H:i", $row->time)."\n";
		  	$link .= "<highlight>Author:<end> $row->name\n";
		  	$link .= "<highlight>Message:<end> $row->news\n\n";
		}
		$msg = Text::make_blob("News", $link)." [Last updated at ".date("dS M, H:i", $updated)."]";
        $chatBot->send($msg, $sender);
	}	
}

?>