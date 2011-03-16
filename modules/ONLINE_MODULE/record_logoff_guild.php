<?php

if (isset($chatBot->guildmembers[$sender])) {
    $db->exec("DELETE FROM `online` WHERE `name` = '$sender' AND `channel_type` = 'guild' AND added_by = '<myname>'");
    if ($chatBot->is_ready()) {
        $db->exec("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `name` = '$sender'");
    }
}
?>
