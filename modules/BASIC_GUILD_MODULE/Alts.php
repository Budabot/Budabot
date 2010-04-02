<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Alt Char Handling
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

if(eregi("^alts add (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($arr[1]);
    if(!$uid)
        $msg = "Player <highlight>$name<end> does not exist.";
    else {
        $db->query("SELECT * FROM alts WHERE `alt` = '$name'");
        $row = $db->fObject();
        if($row->alt == $name)
            $msg = "Player <highlight>$name<end> is already registered as alt from <highlight>$row->main<end>.";
        else {
            $db->query("SELECT * FROM alts WHERE `main` = '$name'");
            if($db->numrows() != 0)
                $msg = "Player <highlight>$name<end> is already registered as main from someone.";
            else {
                $db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name', '$sender')");
                $msg = "<highlight>$name<end> has been registered as your alt.";
            }
        }
    }
} else if(eregi("^alts (rem|del|remove|delete) (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[2]));
    $uid = AoChat::get_uid($arr[2]);
    if(!$uid)
        $msg = "Player <highlight>".$name."<end> does not exist.";
    else {
        $db->query("SELECT * FROM alts WHERE `main` = '$sender' AND `alt` = '$name'");
        $row = $db->fObject();
        if($row->main != $sender)
            $msg = "<highlight>$name<end> is not registered as your alt.";
        else {
            $db->query("DELETE FROM alts WHERE `main` = '$sender' AND `alt` = '$name'");
            $msg = "<highlight>$name<end> has been deleted from you alt list.";
        }
    }
} else if(eregi("^alts (.+)$", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    $uid = AoChat::get_uid($arr[1]);
    if(!$uid)
        $msg = "Player <highlight>".$name."<end> does not exist.";
    else {
        $main = false;
        // Check if $sender is hisself the main
        $db->query("SELECT * FROM alts WHERE `main` = '$name'");
        if($db->numrows() == 0){
            // Check if $sender is an alt
            $db->query("SELECT * FROM alts WHERE `alt` = '$name'");
            if($db->numrows() == 0)
                $msg = "No alts are registered for <highlight>$name<end>.";
            else {
                $row = $db->fObject();
                $main = $row->main;
            }
        } else
            $main = $name;

        // If a main was found create the list
        if($main) {
            $list = "<header>::::: Alternative Character List :::::<end> \n \n";
            $list .= ":::::: Main Character\n";
            $list .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
            if(!isset($this->buddyList[$main]))
                $list .= "No status.\n";
            elseif($this->buddyList[$main] == 1)
                $list .= "<green>Online<end>\n";
            else
                $list .= "<red>Offline<end>\n";
            $list .= ":::::: Alt Character(s)\n";
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
            $msg = bot::makeLink($main."`s Alts", $list);
        }
    }
} elseif(eregi("^alts$", $message)) {
    $main = false;
    // Check if $sender is hisself the main
    $db->query("SELECT * FROM alts WHERE `main` = '$sender'");
    if($db->numrows() == 0){
        // Check if $sender is an alt
        $db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
        if($db->numrows() == 0)
            $msg = "No alts are registered for <highlight>$sender<end>.";
        else {
            $row = $db->fObject();
            $main = $row->main;
        }
    } else
        $main = $sender;


    // If a main was found create the list
    if($main) {
        $list = "<header>::::: Alternative Character List :::::<end> \n \n";
        $list .= ":::::: Main Character\n";
        $list .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
        if(!isset($this->buddyList[$main]))
            $list .= "No status.\n";
        elseif($this->buddyList[$main] == 1)
            $list .= "<green>Online<end>\n";
        else
            $list .= "<red>Offline<end>\n";
            
        $list .= ":::::: Alt Character(s)\n";
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
        $msg = bot::makeLink($sender."`s Alts", $list);
    }
}

// Send info back
if($type == "msg")
    bot::send($msg, $sender);
elseif($type == "priv")
   	bot::send($msg);
elseif($type == "guild")
   	bot::send($msg, "guild");
?>
