<?php

if (isset($chatBot->guildmembers[$sender])) {
    $db->exec("DELETE FROM `online` WHERE `name` = ? AND `channel_type` = 'guild' AND added_by = '<myname>'", $sender);
    if ($chatBot->is_ready()) {
        $db->exec("UPDATE org_members_<myname> SET `logged_off` = ? WHERE `name` = ?", time(), $sender);
    }
}
?>
