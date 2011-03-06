<?php

if (preg_match("/^updateitems$/i", $message)) {
	$msg = download_newest_itemsdb();
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>