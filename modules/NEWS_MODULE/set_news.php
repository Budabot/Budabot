<?php
   
if (preg_match("/^privnews clear$/i", $message)) {
	$setting->save("news", "Not set.");
	$msg = "News has been cleared.";
	$sendto->reply($msg);
} else if (preg_match("/^privnews (.+)$/i", $message, $arr)) {
	$news = $arr[1];
 	if (strlen($news) > 300) {
		$msg = "News can't be longer than 300chars.";
	} else {
		$setting->save("news", $news);	
		$msg = "News has been set.";
	}
	$sendto->reply($msg);
} else if (preg_match("/^adminnews clear$/i", $message)) {
 	$setting->save("adminnews", "Not set.");
	$msg = "Adminnews has been cleared.";
	$sendto->reply($msg);
} else if (preg_match("/^adminnews (.+)$/i", $message, $arr)) {
	$news = $arr[1];
 	if (strlen($news) > 300) {
		$msg = "News can't be longer than 300chars.";
	} else {
		$setting->save("adminnews", $news);	
		$msg = "Adminnews has been set.";
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>