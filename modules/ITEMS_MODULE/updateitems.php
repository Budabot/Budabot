<?php

if (preg_match("/^updateitems$/i", $message)) {
	$msg = download_newest_itemsdb();
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>