<?php

if ($type == "extJoinPrivRequest" && $this->settings["relaytype"] == 2 && strtolower($sender) == strtolower($this->settings["relaybot"])) {
	$this->privategroup_join($sender);
}

?>