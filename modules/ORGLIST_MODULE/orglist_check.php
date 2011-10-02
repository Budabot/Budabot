<?php

if (isset($chatBot->data["ORGLIST_MODULE"]["added"][$sender])) {
	if ($type == "logOn") {
		$chatBot->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 1;
	} else if ($type == "logOff") {
		$chatBot->data["ORGLIST_MODULE"]["result"][$sender]["online"] = 0;
	}

	Buddylist::remove($sender, 'onlineorg');
	unset($chatBot->data["ORGLIST_MODULE"]["added"][$sender]);
	
	forEach ($chatBot->data["ORGLIST_MODULE"]["check"] as $name => $value) {
		$chatBot->data["ORGLIST_MODULE"]["added"][$name] = 1;
		unset($chatBot->data["ORGLIST_MODULE"]["check"][$name]);
		Buddylist::add($name, 'onlineorg');
		break;
	}
	
	checkOrglistEnd();
}

?>