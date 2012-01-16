<?php

if (preg_match("/^topic clear$/i", $message, $arr)) {
  	$setting->save("topic_time", time());
  	$setting->save("topic_setby", $sender);
  	$setting->save("topic", "");
	$msg = "Topic has been cleared.";
    $sendto->reply($msg);
} else if (preg_match("/^topic (.+)$/i", $message, $arr)) {
  	$setting->save("topic_time", time());
  	$setting->save("topic_setby", $sender);
  	$setting->save("topic", $arr[1]);
	$msg = "Topic has been updated.";
    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>