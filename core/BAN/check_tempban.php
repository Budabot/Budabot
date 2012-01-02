<?php

$banInstance = Registry::getInstance('ban');

$update = false;
forEach ($banInstance->getBanlist() as $ban){
	if ($ban->banend != null && ((time() - $ban->banend) >= 0)) {
		$update = true;
	 	$db->exec("DELETE FROM banlist_<myname> WHERE name = ?", $ban->name);
	}	
}

if ($update) {
	$banInstance->upload_banlist();
}

?>