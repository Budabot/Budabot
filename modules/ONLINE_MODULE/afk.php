<?php

if (preg_match("/^.?afk$/i", $message)) {
    $db->exec("UPDATE online SET `afk` = 1 WHERE `name` = '$sender' AND added_by = '<myname>' AND channel_type = '$type'");
    $msg = "<highlight>$sender<end> is now AFK";
	$chatBot->send($msg, $type);
	$message = "afk";
} else if (preg_match("/^.?brb(.*)$/i", $message, $arr)) {
    $db->exec("UPDATE online SET `afk` = 'brb' WHERE `name` = '$sender' AND added_by = '<myname>' AND channel_type = '$type'");
    $msg = "<highlight>$sender<end> is now AFK";
	$chatBot->send($msg, $type);
	$message = "brb" . $arr[1];
} else if (preg_match("/^.?afk (.*)$/i", $message, $arr)) {
	$reason = str_replace("'", "''", $arr[1]);
    $db->exec("UPDATE online SET `afk` = '$reason' WHERE `name` = '$sender' AND added_by = '<myname>' AND channel_type = '$type'");
    $msg = "<highlight>$sender<end> is now AFK";
	$chatBot->send($msg, $type);
	$message = "afk " . $arr[1];
} else if (preg_match("/^.?kiting$/i", $message, $arr) && $numrows != 0) {
	$db->exec("UPDATE online SET `afk` = 'kiting' WHERE `name` = '$sender' AND added_by = '<myname>' AND channel_type = '$type'");
	$msg = "<highlight>$sender<end> is now kiting";
	$chatBot->send($msg, $type);
	$message = "kiting";
}

?>
