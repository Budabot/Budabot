<?php

if ($type == "joinPriv") {
	$this->vars["Guest"][$sender] = true;
	$db->exec("INSERT INTO priv_chatlist_<myname> (`name`) VALUES ('$sender')");
}

?>