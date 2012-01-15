<?php

if ($setting->get("relaybot") != "Off" && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	send_message_to_relay("grc [<myguild>] <highlight>{$sender}<end> logged off");
}

?>
