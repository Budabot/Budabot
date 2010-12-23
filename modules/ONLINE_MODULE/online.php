<?php
/*
 ** Author: Derroylo (RK2)
 ** Description: Who is online(online design)
 ** Version: 1.1
 **
 ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
 **
 ** Date(created): 23.11.2005
 ** Date(last modified): 03.02.2007
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

if (preg_match("/^online$/i", $message)){
	$msg = "";
	list($numonline, $msg, $list) = online($type, $sender, $sendto, $this);
	if ($numonline != 0) {
		$blob = bot::makeLink($msg, $list);
		bot::send($blob, $sendto);
	} else {
		bot::send($msg, $sendto);
	}
} else if (preg_match("/^online (.*)$/i", $message, $arr)) {
	$msg = "";
	switch (strtolower($arr[1])) {
		case "all":
			$prof = "all";
			break;
		case "adv":
			$prof = "Adventurer";
			break;
		case "agent":
			$prof = "Agent";
			break;
		case "crat":
			$prof = "Bureaucrat";
			break;
		case "doc":
			$prof = "Doctor";
			break;
		case "enf":
			$prof = "Enforcer";
			break;
		case "eng":
			$prof = "Engineer";
			break;
		case "fix":
			$prof = "Fixer";
			break;
		case "keep":
			$prof = "Keeper";
			break;
		case "ma":
			$prof = "Martial Artist";
			break;
		case "mp":
			$prof = "Meta-Physicist";
			break;
		case "nt":
			$prof = "Nano-Technician";
			break;
		case "sol":
			$prof = "Soldier";
			break;
		case "trad":
			$prof = "Trader";
			break;
		case "shade":
			$prof = "Shade";
			break;
	}

	if (!$prof) {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, trad or all";
		bot::send($msg, $sendto);
		return;
	}

	list($numonline, $msg, $list) = online($type, $sender, $sendto, $this);
	if ($numonline != 0) {
		$blob = bot::makeLink($msg, $list);
		bot::send($blob, $sendto);
	} else {
		bot::send($msg, $sendto);
	}
}
?>
