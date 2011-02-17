<?php

if (isset($chatBot->guildmembers[$sender])) {
    if (time() >= $chatBot->vars["logondelay"]) {
        $db->exec("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `name` = '$sender'");
    }
}
?>
