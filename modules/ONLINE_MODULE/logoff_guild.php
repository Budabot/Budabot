<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows logoff from Guildmembers
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 26.11.2006
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

if (isset($this->guildmembers[$sender])) {
    $db->query("DELETE FROM guild_chatlist_<myname> WHERE `name` = '$sender'");
    if (time() >= $this->vars["onlinedelay"]) {
        $db->query("UPDATE org_members_<myname> SET `logged_off` = '".time()."' WHERE `name` = '$sender'");
		
		if ($this->settings["bot_notify"] != 0) {
			bot::send("<highlight>$sender<end> logged off", "guild", true);
			
			//Guestchannel part
			if($this->settings["guest_relay"] == 1)
				bot::send("<highlight>$sender<end> logged off", "priv", true);
		}
    }
}
?>
