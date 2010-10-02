<?php
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

if ($this->settings["topic"] != "" && $type == "joinPriv") {
	$date_string = unixtime_to_readable(time() - $this->settings["topic_time"], false);
	$msg = "<highlight>Topic:<end> {$this->settings["topic"]} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$date_string} ago<end>]";
  	bot::send($msg, $sender);
} else if (preg_match("/^topic$/i", $message, $arr)) {
	$date_string = unixtime_to_readable(time() - $this->settings["topic_time"], false);
	if ($this->settings["topic"] == '') {
		$topic = 'No topic set at the moment';
	} else {
		$topic = $this->settings["topic"];
	}
	$msg = "<highlight>Topic:<end> {$topic} [set by <highlight>{$this->settings["topic_setby"]}<end>][<highlight>{$date_string} ago<end>]";
    bot::send($msg, $sendto);
} else if (preg_match("/^topic clear$/i", $message, $arr)) {
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", "");
	$msg = "Topic has been cleared.";
    bot::send($msg, $sendto);
} else if (preg_match("/^topic (.+)$/i", $message, $arr)) {
  	bot::savesetting("topic_time", time());
  	bot::savesetting("topic_setby", $sender);
  	bot::savesetting("topic", $arr[1]);
	$msg = "Topic has been updated.";
    bot::send($msg, $sendto);
}
?>