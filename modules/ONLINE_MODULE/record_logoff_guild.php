<?php

if (isset($this->guildmembers[$sender])) {
    $db->query("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
    if (time() >= $this->vars["onlinedelay"]) {
        $db->query("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `name` = '$sender'");
    }
}
?>
