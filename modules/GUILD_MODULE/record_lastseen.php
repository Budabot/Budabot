<?php

if (isset($chatBot->guildmembers[$charid]) && $chatBot->is_ready()) {
	$db->exec("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `charid` = '$charid'");
}
?>
