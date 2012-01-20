<?php
/*
** Author: Legendadv (RK2)
** Description: Creates a highly Visible Message
** Version: 1.0
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Date(created): 18.02.2006
** Date(last modified): 18.02.2006
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

if (preg_match("/^cmd (.+)$/i", $message, $arr)) {
	$msg = "\n<yellow>---------------------\n<red>".$arr[1]."<end>\n<yellow>---------------------";
	
	if ($type == "msg") {
		$chatBot->sendGuild($msg, true);
		$chatBot->sendPrivate($msg, true);
	} else {
		$sendto->reply($msg, true);
	}
} else {
	$syntax_error = true;
}

?>