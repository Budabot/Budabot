<?php

if ($type == "joinPriv") {
	$this->vars["Guest"][$sender] = true;
	$db->query("INSERT INTO priv_chatlist_<myname> (`name`) VALUES ('$sender')");
}

?>