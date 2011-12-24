<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	$msg = News::getNews();
	if ($msg != '') {
        $chatBot->send($msg, $sender);
	}	
}

?>