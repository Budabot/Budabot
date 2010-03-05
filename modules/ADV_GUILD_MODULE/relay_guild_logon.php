<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Handles Guestrelay Member Logon
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

if($this->settings["relaybot"] != "0") {
    $msg = "";
    $db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$sender'");
	$numrows = $db->numrows();
	$row = $db->fObject();
	if($row->mode != "del" && $numrows == 1) {
        if(time() >= $this->vars["onlinedelay"] && !($this->vars["IgnoreLog"][$sender])) {
            if($row->firstname)
                $msg = $row->firstname." ";

            $msg .= "<highlight>\"".$row->name."\"<end> ";

            if($row->lastname)
                $msg .= $row->lastname." ";

            $msg .= "(Level <highlight>$row->level<end>/<green>$row->ai_level - $row->ai_rank<end>, <highlight>$row->profession<end>,";

            if($row->guild)
                $msg .= " $row->rank of <highlight>$row->guild<end>) ";
            else
                $msg .= " Not in a guild.) ";

            $msg .= "logged on. ";

            $logon_msg = $row->logon_msg;

            // Alternative Characters Part
            $main = false;
            // Check if $sender is hisself the main
            $db->query("SELECT * FROM alts WHERE `main` = '$sender'");
            if($db->numrows() == 0){
                // Check if $sender is an alt
                $db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
                if($db->numrows() != 0) {
                    $row = $db->fObject();
                    $main = $row->main;
                }
            } else
                $main = $sender;

            // If a main was found create the list
            if($main) {
                $list = "<header>::::: Alternative Character List :::::<end> \n \n";
                $list .= ":::::::: Main Character\n";
                $list .= "<tab><tab>".bot::makeLink($row->main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
                if($this->buddyList[$main] == 1)
                    $list .= "<green>Online<end>\n";
                elseif($this->buddyList[$main] == 0)
                    $list .= "<red>Offline<end>\n";
                else
                    $list .= "No status.\n";

                $list .= ":::::::: Alt Character(s)\n";
                $db->query("SELECT * FROM alts WHERE `main` = '$main'");
                while($row = $db->fObject()) {
                    $list .= "<tab><tab>".bot::makeLink($row->alt, "/tell ".$this->vars["name"]." whois $row->alt", "chatcmd")." - ";
                    if(!isset($this->buddyList[$row->alt]))
                       $list .= "No status.\n";
                    elseif($this->buddyList[$row->alt] == 1)
                       $list .= "<green>Online<end>\n";
                    else
                       $list .= "<red>Offline<end>\n";
                }
            }

			if($main != $sender && $main != false) {
				$alts = bot::makeLink("Alts", $list);
				$msg .= "Main: <highlight>$main<end> ($alts) ";
			} elseif($main != false) {
	  			$alts = bot::makeLink("Alts of $main", $list);
				$msg .= "$alts ";  
			}
		
            if($logon_msg != '0')
                $msg .= " - ".$logon_msg;

            bot::send("grc ".$msg, $this->settings["relaybot"]);
        }
    }
}
?>
