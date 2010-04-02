<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the History of a raid
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.10.2006
   ** Date(last modified): 10.10.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if(eregi("^raidhistory$", $message)) {
	$db->query("SELECT * FROM raids_history_<myname> ORDER BY `time` DESC LIMIT 0, 20");
	if($db->numrows() != 0) {
		$list = "<header>::::: History about the last 20 rolls :::::<end>\n\n";
		while($row = $db->fObject()) {
			if($row->icon != 0) {
				$list .= "<img src=rdb://{$row->icon}>\n";
			}
			if($row->aoid != 0)
				$list .= "Item: ".bot::makeItem($row->aoid, $row->aoid, $row->ql, $row->item)."\n";
			else
				$list .= "Item: <highlight>$row->item<end>\n";
				
			$list .= "Date: <highlight>".gmdate("Y-F-d H:i", $row->time)."GMT<end>\n";
			if($row->points != "flat")
				$list .= "Winner: <highlight>$row->winner<end> with a bid of <highlight>$row->points<end>points\n";
			else
				$list .= "Winner: <highlight>$row->winner<end>(Flat rolled)\n";
			
			$list .= "\n";
        }
	    $msg = bot::makeLink("Raidhistory", $list);
	} else
		$msg = "No raid recorded yet.";

	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
		bot::send($msg);
} else
	$syntax_error = true;
?>