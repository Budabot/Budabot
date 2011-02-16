<?php

if (isset($this->guildmembers[$sender])) {
    if (time() >= $this->vars["logondelay"]) {
        $db->exec("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `name` = '$sender'");
    }
}
?>
