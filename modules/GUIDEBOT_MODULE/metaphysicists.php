<?php
	 /*
   ** Author: Plugsz (RK1)
   ** Description: Guides
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.21.2006
   ** Date(last modified): 12.21.2006
   ** 
   ** Copyright (C) 2006 Donald Vanatta
   **
   ** Licence Infos: 
   ** This file is for use with Budabot.
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

$blob = "<header>::::: Guide to Meta Physicists :::::<end>\n\n
The Meta-Physicist for Beginners Guide
Guide is too large to load into the bot, so I have included the link on AOForums for your convienance.

<a href='chatcmd:///start http://forums.anarchy-online.com/showthread.php?t=466609/ '><font color = yellow>MP Guides on the AO Forums</font></a>

"
;

$msg = bot::makeLink("Guide to Meta Physicists", $Metaphysicists_txt); 
bot::send($msg, $sendto);
?>