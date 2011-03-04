<?php

if (preg_match("/^topic$/i", $message, $arr)) {
	$date_string = Util::unixtime_to_readable(time() - Setting::get('topic_time'), false);
	if (Setting::get('topic') == '') {
		$topic = 'No topic set';
	} else {
		$topic = Setting::get('topic');
	}
	$msg = "<highlight>Topic:<end> {$topic} [set by <highlight>{$chatBot->settings["topic_setby"]}<end>][<highlight>{$date_string} ago<end>]";
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>