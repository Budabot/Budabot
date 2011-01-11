<?php

// only delete guest list when bot is starting up, not after it's started (when someone does !newplugins)
if ($this->state != "ok") {
	$db->exec("DELETE FROM priv_chatlist_<myname>");
}

?>