<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Who is online(chatlist design)
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 13.01.2007
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

if(eregi("^chatlist$", $message) || eregi("^sm$", $message)){
	if($type == "priv" || ($this->settings["online_tell"] == 1 && $type == "msg")) {
		$db->query("SELECT * FROM priv_chatlist_<myname> ORDER BY `level` DESC");
	} elseif($type == "guild" || ($this->settings["online_tell"] == 0 && $type == "msg")) {
	  	if($this->settings["relaybot"] != "0")
			$db->query("SELECT * FROM guild_chatlist_<myname> UNION ALL SELECT * FROM guild_chatlist_".strtolower($this->settings["relaybot"])." ORDER BY `level` DESC");
		else
			$db->query("SELECT * FROM guild_chatlist_<myname> ORDER BY `level` DESC");
	}
	while($row = $db->fObject()){
		$list = $list."$row->name ".
			  "<br>   $row->level/$row->ai_level $row->profession";
		if($row->org)
			$list = $list." ($row->guild)";
		$list = $list."<br>";
		$total++;
		if($row->level >= 220)
			$at220++;
		if($row->level >= 210 && $row->level <= 219)
			$above210++;
		if($row->level <= 209)
			$below++;
	}
	$list = "Total: $total<br><br>".$list;
	$list = "Players(220): $at220<br>".$list;
	$list = "Players(210-219): $above210<br>".$list;
	$list = "Players(1-209): $below<br>".$list;
	if($this->vars["topic"] != "")
		$topic = "Topic: {$this->settings["topic"]}<br><br>";
	$list = "<yellow>Chatlist<br><br><green>$topic<lgreen>".$list;
	$link = bot::makeLink('Chatlist', $list);

    if($type == "msg")
        bot::send($link, $sender);
    elseif($type == "priv")
       	bot::send($link);
    elseif($type == "guild")
       	bot::send($link, "guild");
}
?>
