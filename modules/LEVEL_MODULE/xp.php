<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows needed XP/SK for a level
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.12.2005
   ** Date(last modified): 15.12.2005
   ** 
   ** Copyright (C) 2005 Carsten Lohmann
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

if (preg_match("/^xp ([0-9]+)$/i", $message, $arr)) {
	$level = $arr[1];
	if (($row = Level::get_level_info($level)) != false) {
		if ($level >= 200) {
			$msg = "With lvl <highlight>{$row->level}<end> you need <highlight>".number_format($row->xpsk)."<end> SK to level up.";
		} else {
			$msg = "With lvl <highlight>{$row->level}<end> you need <highlight>".number_format($row->xpsk)."<end> XP to level up.";
		}
    } else {
        $msg = "You need to specify a lvl between 1 and 219.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^xp ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$minLevel = $arr[1];
	$maxLevel = $arr[2];
    if ($minLevel >= 1 && $minLevel <= 220 && $maxLevel >= 1 && $maxLevel <= 220) {
        if ($minLevel < $maxLevel) {
			$db->query("SELECT * FROM levels WHERE level >= $minLevel AND level < $maxLevel");
			$xp = 0;
			$sk = 0;
			while (($row = $db->fObject()) != false) {
                if ($row->level < 200) {
                    $xp += $row->xpsk;
                } else {
                    $sk += $row->xpsk;
				}
            }
            if ($sk > 0 && $xp > 0) {
                $msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>".number_format($xp)."<end> XP and <highlight>".number_format($sk)."<end> SK.";
            } else if ($sk > 0) {
                $msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>" .number_format($sk)."<end> SK.";
            } else if ($xp > 0) {
                $msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>".number_format($xp)."<end> XP.";
			}
        } else {
            $msg = "The start level can't be higher then the end level.";
		}
    } else {
        $msg = "You need to specify a lvl between 1 and 220.";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
