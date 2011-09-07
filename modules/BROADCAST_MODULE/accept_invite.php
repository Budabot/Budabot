<?php

if (isset($chatBot->data["broadcast_list"][$sender])) {
	$chatBot->privategroup_join($sender);
}

?>