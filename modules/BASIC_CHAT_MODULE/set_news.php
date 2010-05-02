<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Set admin and user news
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.11.2005
   ** Date(last modified): 04.11.2006
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
   
if(eregi("^privnews clear$", $message)) {
	bot::savesetting("news", "Not set.");
	$msg = "News has been cleared.";
	if($type == "priv")
		bot::send($msg);
	elseif($type == "msg")
		bot::send($msg, $sender);
} elseif(eregi("^privnews (.+)$", $message, $arr)) {
	$news = $arr[1];
 	if(strlen($news) > 300) {
		$msg = "News can�t be longer than 300chars.";
	} else {
		bot::savesetting("news", $news);	
		$msg = "News has been set.";
	}
	if($type == "priv")
		bot::send($msg);
	elseif($type == "msg")
		bot::send($msg, $sender);	
} elseif(eregi("^adminnews clear$", $message)) {
 	bot::savesetting("adminnews", "Not set.");
	$msg = "Adminnews has been cleared.";
	if($type == "priv")
		bot::send($msg);
	elseif($type == "msg")
		bot::send($msg, $sender);
} elseif(eregi("^adminnews (.+)$", $message, $arr)) {
	$news = $arr[1];
 	if(strlen($news) > 300) {
		$msg = "News can�t be longer than 300chars.";
	} else {
		bot::savesetting("adminnews", $news);	
		$msg = "Adminnews has been set.";
	}
	if($type == "priv")
		bot::send($msg);
	elseif($type == "msg")
		bot::send($msg, $sender);	
}
?>