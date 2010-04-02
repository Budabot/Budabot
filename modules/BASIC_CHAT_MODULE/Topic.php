<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set and shows the topic for Privatechannel
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.02.2006
   ** Date(last modified): 22.07.2006
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

if($this->settings["topic"] != "" && $type == "joinPriv") {
	$time = time() - $this->settings["topic_time"];
	$mins = floor($time / 60);
	$hours = floor($mins / 60);
	$mins = floor($mins - ($hours * 60));
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
  	bot::send("<highlight>Topic:<end> {$this->settings["topic"]} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$days}days, {$hours}hrs and {$mins}mins ago<end>]", $sender);
} elseif(eregi("^topic$", $message, $arr)) {
	$time = time() - $this->settings["topic_time"];
	$mins = floor($time / 60);
	$hours = floor($mins / 60);
	$mins = floor($mins - ($hours * 60));
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$msg = "<highlight>Topic:<end> {$this->settings["topic"]} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$days}days, {$hours}hrs and {$mins}mins ago<end>]";
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} elseif(eregi("^topic clear$", $message, $arr)) {
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", "No Topic set atm.");
	$msg = "Topic has been cleared.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
} elseif(eregi("^topic (.+)$", $message, $arr)) {
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", $arr[1]);
	$msg = "Topic has been updated.";

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
       	bot::send($msg);
    elseif($type == "guild")
       	bot::send($msg, "guild");
}
?>