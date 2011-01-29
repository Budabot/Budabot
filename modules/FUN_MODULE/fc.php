<?php

$fc = array();
$fc[] = "[in the same thread that topics got deleted for not agreeing with how moderation was happening] This prevalant attitude that we restrict or otherwise take issue with people having differing opinions from us. --Kintaii";
$fc[] = "We're more open and honest than probably pretty much any other game development team out there. --Kintaii";
$fc[] = "[talking about the new engine] It's done when it's done. --Vhab";

if (preg_match("/^fc/i", $message)) {
	$dmg = rand(100,999);
    $cred = rand(10000,9999999);
	$randval = rand(1, sizeof($fc) - 1);
	$msg = $fc[$randval];
    $msg = str_replace("*name*", $sender, $msg);
    $msg = str_replace("*dmg*", $dmg, $msg);
    $msg = str_replace("*creds*", $cred, $msg);
	bot::send($msg, $sendto);
}

?>
