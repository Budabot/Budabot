<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Shows the banlist
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 05.06.2006
   ** 
   ** Copyright (C) 2005, 2006 J Gracik
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

if (preg_match("/^banlist$/i", $message)){

	$sql = "SELECT name, banned_by, time, reason, banend FROM banlist_<myname>";
	$db->query($sql);
	if ($db->numrows() == 0) {
	    bot::send("No one is banned from this bot currently.", $sendto);
	    return;
	}

	$list.= "<header>::::: Banlist :::::<end>\n\n";
	while (($row = $db->fObject()) != FALSE) {
		$list.= "<highlight>Name:<end> {$row->name}\n";
		$list.= "<highlight><tab>Date:<end> {$row->time}\n";
		$list.= "<highlight><tab>By:<end> {$row->banned_by}\n";
		if ($row->banend != null) {
			$list.= "<highlight><tab>Ban ends at:<end> ".date("m-d-y", $row->banend)."\n";
		}
		
		if ($row->reason != '') {
			$list.= "<highlight><tab>Reason:<end> {$row->reason}\n";
		}
		$list.= "\n";	
	}
	$link = bot::makeLink('Banlist', $list);
	bot::send($link, $sendto);
} else {
	$syntax_error = true;
}
?>