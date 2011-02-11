<?php

if ($type == "extJoinPrivRequest" && $this->settings["relaytype"] == 2 && strtolower($sender) == strtolower($this->settings["relaybot"])) {
	$chatBot->privategroup_join($sender);
}

?>