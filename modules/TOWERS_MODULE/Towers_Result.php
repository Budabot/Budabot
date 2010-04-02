<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the attack and results on tower attacks
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.12.2005
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

if(eregi("^battle$", $message)) {
    $db->query("SELECT * FROM tower_attack_<myname> ORDER BY `time` DESC LIMIT 0, 20");
    if($db->numrows() == 0)
        $msg = "No Tower messages recorded yet.";
    else {
        $list = "<header>::::: The last 20 Tower Attacks :::::<end>\n\n";
        while($row = $db->fObject()) {
            $list .= "Attacktime: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
            if(strtolower($row->att_side) == "clan")
                $att_side = "<red>".$row->att_side."<end>";
            elseif(strtolower($row->att_side) == "neutral")
                $att_side = "<white>".$row->att_side."<end>";
            elseif(strtolower($row->att_side) == "omni")
                $att_side = "<blue>".$row->att_side."<end>";
			else 
				$att_side = "<red>UNKNOWN SIDE<end>";
				
            if(strtolower($row->def_side) == "clan")
                $def_side = "<red>".$row->def_side."<end>";
            elseif(strtolower($row->def_side) == "neutral")
                $def_side = "<white>".$row->def_side."<end>";
            elseif(strtolower($row->def_side) == "omni")
                $def_side = "<blue>".$row->def_side."<end>";
			else 
				$def_side = "<red>UNKNOWN SIDE<end>";


			if($row->att_guild == "")
	            $list .= "Attacker: <highlight>".$row->att_player."(Lvl ".$row->att_level."/".$row->att_profession."/".$att_side.") <end>\n";
	        else
				$list .= "Attacker: <highlight>".$row->att_player."(Lvl ".$row->att_level."/".$row->att_profession."/".$row->att_guild."/".$att_side.") <end>\n";

            $list .= "Defender: <highlight>".$row->def_guild." (".$def_side.") <end>\n";
            $list .= "Zone: <highlight>".$row->zone." (".$row->x."x".$row->y.")<end>\n\n";
        }
        $msg = "Tower Battle: ".bot::makeLink("click to view", $list);
    }
    
    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
}else if(eregi("^victory$", $message)) {
    $db->query("SELECT * FROM tower_result_<myname> ORDER BY `time` DESC LIMIT 0, 20");
    if($db->numrows() == 0)
        $msg = "No Tower messages recorded yet.";
    else {
        $list = "<header>::::: The last 20 Tower Results :::::<end>\n\n";
        while($row = $db->fObject()) {
            $list .= "Time: <highlight>".gmdate("M j, Y, G:i", $row->time)." (GMT)<end>\n";
            if(strtolower($row->win_side) == "clan")
                $win_side = "<red>".$row->win_side."<end>";
            elseif(strtolower($row->win_side) == "neutral")
                $win_side = "<white>".$row->win_side."<end>";
            elseif(strtolower($row->win_side) == "omni")
                $win_side = "<blue>".$row->win_side."<end>";

            if(strtolower($row->lose_side) == "clan")
                $lose_side = "<red>".$row->lose_side."<end>";
            elseif(strtolower($row->lose_side) == "neutral")
                $lose_side = "<white>".$row->lose_side."<end>";
            elseif(strtolower($row->lose_side) == "omni")
                $lose_side = "<blue>".$row->lose_side."<end>";

            $list .= "Winner: <highlight>".$row->win_guild." (".$win_side.") <end>\n";
            $list .= "Loser: <highlight>".$row->lose_guild." (".$lose_side.") <end>\n\n";
        }
        $msg = "Tower Battle Results: ".bot::makeLink("click to view", $list);
    }
    
    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>
