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
  	if (count($this->banlist) == 0) {
	    bot::send("No one is currently banned from this bot.", $sendto);
	    return;
	}
	
	$list.= "<header>::::: Banlist :::::<end>\n\n";
	forEach ($this->banlist as $key => $data) {
		$list.= "<highlight>Name:<end> {$this->banlist["$key"]["name"]}\n";
		$list.= "<highlight><tab>Date:<end> {$this->banlist["$key"]["when"]}\n";
		$list.= "<highlight><tab>By:<end> {$this->banlist["$key"]["admin"]}\n";
		if (isset($this->banlist[$key]["banend"])) {
			$list.= "<highlight><tab>Ban ends at:<end> ".date("m-d-y", $this->banlist["$key"]["banend"])."\n";
		}
		
		if (isset($this->banlist["$key"]["reason"])) {
			$list.= "<highlight><tab>Reason:<end> {$this->banlist["$key"]["reason"]}\n";
		}
		$list.= "\n";	
	}
	$link = bot::makeLink('Banlist', $list);
	bot::send($link, $sendto);
} else {
	$syntax_error = true;
}
?>