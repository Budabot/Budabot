<?php

if ($type == "joinPriv") {
	$db->exec("INSERT INTO priv_chatlist_<myname> (`name`) VALUES ('$sender')");
}

?>