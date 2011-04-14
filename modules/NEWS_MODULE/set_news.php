<?php
   
if (preg_match("/^privnews clear$/i", $message)) {
	Setting::save("news", "Not set.");
	$msg = "News has been cleared.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^privnews (.+)$/i", $message, $arr)) {
	$news = $arr[1];
 	if (strlen($news) > 300) {
		$msg = "News can't be longer than 300chars.";
	} else {
		Setting::save("news", $news);	
		$msg = "News has been set.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^adminnews clear$/i", $message)) {
 	Setting::save("adminnews", "Not set.");
	$msg = "Adminnews has been cleared.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^adminnews (.+)$/i", $message, $arr)) {
	$news = $arr[1];
 	if (strlen($news) > 300) {
		$msg = "News can't be longer than 300chars.";
	} else {
		Setting::save("adminnews", $news);	
		$msg = "Adminnews has been set.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>