<?php

if ($setting->get('topic') == '') {
	return;
}

if ($type == 'joinpriv' || ($type == 'logon' && isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready())) {
	$date_string = Util::unixtime_to_readable(time() - $setting->get('topic_time'), false);
	$topic = $setting->get('topic');
	$set_by = $setting->get('topic_setby');
		
	$msg = "<highlight>Topic:<end> {$topic} [set by <highlight>{$set_by}<end>][<highlight>{$date_string} ago<end>]";
    $chatBot->send($msg, $sender);
}

?>