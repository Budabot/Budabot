<?php

if ($type == "leavePriv") {
	$db->query("DELETE FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
	unset($this->vars["Guest"][$sender]);
}

?>