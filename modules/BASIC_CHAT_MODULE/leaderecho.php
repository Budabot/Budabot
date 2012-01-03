<?php

if ($setting->get("leaderecho") == 1 && $chatBot->data["leader"] == $sender && $message[0] != $setting->get("symbol")) {
  	$msg = $setting->get("leaderecho_color") . $message . "<end>";
  	$chatBot->send($msg, 'priv');
}

?>