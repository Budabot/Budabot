<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Guestchannel (invite/kick)
   ** Version: 1.1
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

if (preg_match("/^onlineguests$/i", $message)) {
	if (count($this->vars["Guest"]) > 0) {
		$db->query("SELECT * FROM priv_chatlist_<myname> WHERE `guest` = 1 ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header>::::: $numguest User(s) in Guestchannel<end>\n";
	    while ($row = $db->fObject()) {
            if ($oldprof != $row->profession) {
                $list .= "\n<tab><highlight>$row->profession<end>\n";
                $oldprof = $row->profession;
            }
            if ($row->afk != "0") {
                $afk = "(<red>Away from keyboard<end>)";
            } else {
                $afk = "";
			}
			$list .= "<tab><tab><highlight>$row->name<end> (Lvl $row->level/<green>$row->ai_level<end>) <highlight>::<end> ($row->guild) $afk\n";
	    }
		
        $msg = "<highlight>".$numguest."<end> players in Guestchannel ";
        $link = ":: ".bot::makeLink('Click here', $list);
        if($numguest != 0) {
           	bot::send($msg.$link, "guild");
        } else {
           	bot::send($msg, "guild");
		}
	} else {
		bot::send("No player is in the guestchannel.", "guild");
	}
}
?>
