<?php

if (!Util::isValidSender($sender) && preg_match("/^(.+) turned the cloaking device in your city (on|off).$/i", $message, $arr)) {
	$db->exec("INSERT INTO org_city_<myname> (`time`, `action`, `player`) VALUES ('".time()."', '".$arr[2]."', '".$arr[1]."')");
}

?>
