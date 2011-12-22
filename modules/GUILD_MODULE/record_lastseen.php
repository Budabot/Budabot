<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	$db->exec("UPDATE org_members_<myname> SET `logged_off` = ? WHERE `name` = ?", time(), $sender);
}
?>
