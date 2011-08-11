<?php

if (preg_match("/^rembuddyall$/i", $message, $arr)) {
	forEach ($chatBot->buddyList as $uid => $buddy) {
		$chatBot->buddy_remove($uid);
	}
	$chatBot->buddyList = array();
	
	$msg = "All buddies have been removed from the buddy list.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>