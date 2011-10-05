<?php

if (Setting::get("leaderecho") == 1 && $chatBot->data["leader"] == $sender && $message[0] != Setting::get("symbol")) {
  	$msg = Setting::get("leaderecho_color") . $message . "<end>";
  	$chatBot->send($msg, 'priv');
}
?>