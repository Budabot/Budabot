<?php

if (isset($chatBot->guildmembers[$charid])) {
    $db->exec("DELETE FROM online WHERE `charid` = '$charid' AND `channel_type` = 'guild' AND added_by = '<myname>'");
    if ($chatBot->is_ready()) {
        $db->exec("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `charid` = '$charid'");
    }
}
?>
