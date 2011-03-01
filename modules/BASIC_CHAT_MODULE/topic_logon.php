<?php
   
if (Setting::get('topic') == '') {
	return;
}

if ($type == 'joinPriv' || ($type == 'logon' && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready())) {
	$date_string = Util::unixtime_to_readable(time() - $chatBot->settings["topic_time"], false);
	$msg = "<highlight>Topic:<end> {$chatBot->settings["topic"]} [set by <highlight>{$chatBot->settings["topic_setby"]}<end>][<highlight>{$date_string} ago<end>]";
    $chatBot->send($msg, $sender);
}

?>