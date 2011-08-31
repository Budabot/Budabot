<?php

if (Setting::get('topic') == '') {
	return;
}

if ($type == 'joinPriv' || ($type == 'logOn' && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready())) {
	$date_string = Util::unixtime_to_readable(time() - Setting::get('topic_time'), false);
	$topic = Setting::get('topic');
	$set_by = Setting::get('topic_setby');
		
	$msg = "<highlight>Topic:<end> {$topic} [set by <highlight>{$set_by}<end>][<highlight>{$date_string} ago<end>]";
    $chatBot->send($msg, $sender);
}

?>