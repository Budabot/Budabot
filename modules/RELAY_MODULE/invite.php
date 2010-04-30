<?php

if ($type == "extJoinPrivRequest" && strtolower($sender) == strtolower($this->settings["externalrelaybot"])) {
	AOChat::privategroup_join($sender);
}

?>