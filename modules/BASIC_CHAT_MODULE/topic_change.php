<?php

if (preg_match("/^topic clear$/i", $message, $arr)) {
  	Setting::save("topic_time", time());
  	Setting::save("topic_setby", $sender);
  	Setting::save("topic", "");
	$msg = "Topic has been cleared.";
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^topic (.+)$/i", $message, $arr)) {
  	Setting::save("topic_time", time());
  	Setting::save("topic_setby", $sender);
  	Setting::save("topic", $arr[1]);
	$msg = "Topic has been updated.";
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>