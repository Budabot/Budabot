<?php

if ($type == "extjoinprivrequest" && $setting->get("relaytype") == 2 && strtolower($sender) == strtolower($setting->get("relaybot"))) {
	$chatBot->privategroup_join($sender);
}

?>
