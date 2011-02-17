<?php
/*
 ** Author: Derroylo (RK2)
 ** Description: Kill On Sight List
 ** Version: 0.6
 **
 ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
 **
 ** Date(created): 31.01.2006
 ** Date(last modified): 21.11.2006
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

if (preg_match("/^kos$/i", $message)) {
	$db->query("SELECT * FROM koslist");
	if ($db->numrows() == 0) {
		$msg = "No list exists yet.";
	} else {
		while ($row = $db->fObject()) {
			$list[$row->name]++;
		}

		arsort($list);
		$list = array_slice($list, 0, 25, true);
		$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
		$link .= "This list shows the top 25 of added Players\n\n";
		$i = 0;
		forEach ($list as $key => $value) {
			$i++;
			$link .= "$i. $key <highlight>(Voted {$value} times)<end>\n";
		}
			
		$msg = Text::make_link("KOS-List", $link);
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos add (.+)$/i", $message, $arr)) {
	$explodemsg = explode(' ', $arr[1], 3);
	$name = ucfirst(strtolower($explodemsg[0]));
	if ('reason' == $explodemsg[1]) {
		// compatibility for old style syntax add X reason Y
		$reason = $explodemsg[2];
	} else {
		// otherwise stitch the reason back together
		$reason = $explodemsg[1] . ' ' . $explodemsg[2];
	}

	if (strlen($reason) >= 50) {
		$msg = "The reason can't be longer than 50 characters.";
	} else {
		$db->query("SELECT * FROM koslist WHERE `sender` = '$sender' AND `name` = '".str_replace("'", "''", $name)."'");
		if($db->numrows() == 1) {
			$msg = "You have already <highlight>$name<end> on your KOS List.";
		} else {
			$db->exec("INSERT INTO koslist (`time`, `name`, `sender`, `reason`) VALUES (".time().", '".str_replace("'", "''", $name)."', '$sender', '".str_replace("'", "''", $reason)."')");
			$msg = "You have successfull added <highlight>$name<end> to the KOS List.";
		}
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos rem (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$db->query("SELECT * FROM koslist WHERE `sender` = '$sender' AND `name` = '".str_replace("'", "''", $name)."'");
	if ($db->numrows() == 1) {
		$db->exec("DELETE FROM koslist WHERE `sender` = '$sender' AND `name` = '".str_replace("'", "''", $name)."'");
		$msg = "You have successfull removed <highlight>$name<end> from the KOS List.";
	} else if ($chatBot->guildmembers[$sender] < $chatBot->vars['guild_admin_level']) {
		$db->query("SELECT * FROM koslist WHERE `name` = '".str_replace("'", "''", $name)."'");
		if($db->numrows() != 0) {
			$db->exec("DELETE FROM koslist WHERE `name` = '$".str_replace("'", "''", $name)."'");
			$msg = "You have successfull removed <highlight>$name<end> from the KOS List.";
		} else {
			$msg = "No one with this name is on the KOS List.";
		}
	} else {
		$msg = "You don't have this player on your KOS List.";
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$db->query("SELECT * FROM koslist WHERE `name` = '".str_replace("'", "''", $name)."' LIMIT 0, 40");
	if ($db->numrows() >= 1) {
		$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
		$link .= "The following Players has added <highlight>$name<end> to his list\n\n";
		while ($row = $db->fObject()) {
			$link .= "Name: <highlight>$row->sender<end>\n";
			$link .= "Date: <highlight>".gmdate("dS F Y, H:i", $row->time)."<end>\n";
			if ($row->reason != "0" && "" != $row->reason) {
				// only show the reason if there is one
				// old style would be zero as reason
				// new style is an empty string
				$link .= "Reason: <highlight>$row->reason<end>\n";
			}

			$link .= "\n";
		}
		$msg = Text::make_link("KOS-List from $name", $link);
	} else {
		$msg = "The player <highlight>$name<end> isn't on the KOS List.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>