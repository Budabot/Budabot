<?php

if (preg_match("/^topic$/i", $message, $arr)) {
	if ($setting->get('topic') == '') {
		$msg = 'No topic set.';
	} else {
		$date_string = Util::unixtime_to_readable(time() - $setting->get('topic_time'), false);
		$topic = $setting->get('topic');
		$set_by = $setting->get('topic_setby');

		$msg = "<highlight>Topic:<end> {$topic} [set by <highlight>{$set_by}<end>][<highlight>{$date_string} ago<end>]";
	}

    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
