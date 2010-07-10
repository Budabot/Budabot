<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the topic on Members logon
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.07.2006
   ** Date(last modified): 24.11.2006
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

if (isset($this->guildmembers[$sender]) && (time() >= $this->vars["topicdelay"])) {
	$time = time() - $this->settings["topic_time"];
	$mins = floor($time / 60);
	$hours = floor($mins / 60);
	$mins = floor($mins - ($hours * 60));
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$msg = "<highlight>Topic:<end> {$this->settings["topic"]} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$days}days, {$hours}hrs and {$mins}mins ago<end>]";
    bot::send($msg, $sender);
}
?>