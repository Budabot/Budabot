<?php
/*
 ** Author: Mindrila (RK1)
 ** Description: Tells players logging on the actual online status
 ** Version: 0.1
 **
 ** Developed for: Budabot(http://budabot.com)
 **
 ** Date(created): 23.03.2010
 ** Date(last modified): 23.03.2010
 **
 ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann
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

$msg = "";
list($numonline, $msg, $list) = online($sender, $sendto, $this);
if ($numonline != 0) {
	$blob = Text::make_link($msg, $list);
	$chatBot->send($blob, $sender);
} else {
	$chatBot->send($msg, $sender);
}

?>