<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set logon messages from Guildmembers
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */

$db->query("SELECT name, logon_msg FROM org_members_<myname> WHERE `name` = '$sender'");
$row = $db->fObject();
if (preg_match("/^logon$/i", $message)) {
	if ($row !== null) {
		if ($row->logon_msg == '') {
			$msg = "Your logon message has not been set.";
		} else {
			$msg = "{$sender} logon: {$row->logon_msg}";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logon clear$/i", $message)) {
    if ($row !== null) {
        $db->exec("UPDATE org_members_<myname> SET `logon_msg` = '' WHERE `name` = '$sender'");
        $msg = "Logon message cleared.";
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^logon (.+)$/i", $message, $arr)) {
    if ($row !== null) {
        $arr[1] = str_replace("'", "''", $arr[1]);
        if (strlen($arr[1]) <= Setting::get('max_logon_msg_size')) {
            $db->exec("UPDATE org_members_<myname> SET `logon_msg` = '$arr[1]' WHERE `name` = '$sender'");
            $msg = "Thank you $sender. Your logon message has been set.";
        } else {
            $msg = "Your logon message is too large. Your logon message may contain a maximum of " . Setting::get('max_logon_msg_size') . " characters.";
		}
    } else {
        $msg = "You are not on the notify list.";
	}
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
