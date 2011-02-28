<?php

if ($type == "extJoinPrivRequest" && Setting::get("relaytype") == 2 && strtolower($sender) == strtolower(Setting::get("relaybot"))) {
	$chatBot->privategroup_join($sender);
}

?>