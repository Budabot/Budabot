<?php

if (preg_match("/^rembuddyall$/i", $message, $arr)) {
	forEach ($buddylistManager->buddyList as $uid => $buddy) {
		$chatBot->buddy_remove($uid);
	}
	
	$msg = "All buddies have been removed from the buddy list.";
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>