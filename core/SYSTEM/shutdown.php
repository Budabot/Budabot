<?php
   /*
   ** Author: Sebuda (RK2)
   ** Description: Shuts the Bot down.
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 28.04.2006
   ** Date(last modified): 28.04.2006
   ** 
   ** Copyright (C) 2006 J. Gracik
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

$msg = "The Bot is shutting down.";
$chatBot->send($msg, $sender);
$chatBot->send($msg, "prv", true);
$chatBot->send($msg, "guild", true);

$chatBot->disconnect();
Logger::log('INFO', 'Core', "The Bot is shutting down.");
die("The Bot is shutting down.");
?>