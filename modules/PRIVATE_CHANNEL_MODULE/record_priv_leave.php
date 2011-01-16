<?php

if ($type == "leavePriv") {
	$db->exec("DELETE FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
	unset($this->vars["Guest"][$sender]);
}

?>