<?php

if (ucfirst(strtolower(Setting::get('worldnet_bot'))) == $sender) {
	$chatBot->privategroup_join($sender);
}

?>