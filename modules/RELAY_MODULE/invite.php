<?php

if ($type == "extJoinPrivRequest" && $chatBot->settings["relaytype"] == 2 && strtolower($sender) == strtolower($chatBot->settings["relaybot"])) {
	$chatBot->privategroup_join($sender);
}

?>