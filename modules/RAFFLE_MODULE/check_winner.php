<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Checks the running timers
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 26.12.2005
   ** Date(last modified): 21.11.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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
//Generously hijacked and modified from Buddhabot's timers_check.php
//Check if at least one timer is running

	if ($this->vars["Raffles"]["inprog"] == 0) {
		return;
	}

	$tleft = $this->vars["Raffles"]["time"] - time();
	$owner = $this->vars["Raffles"]["owner"];
	$item = $this->vars["Raffles"]["item"];
	$timesincelastmsg = time() - $this->vars["Raffles"]["lastmsgtime"];

	$linkMsg="<white>Current Members:<end>";
    forEach ($this->vars["Raffles"]["rafflees"] as $tempName) {
       $linkMsg .= "\n$tempName";
    }
    if (count($this->vars["Raffles"]["rafflees"]) == 0) {
       $linkMsg .= "No entrants yet.";
    }
    
    $linkMsg .= "

Click <a href='chatcmd:///tell <myname> joinRaffle'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> leaveRaffle'>here</a> if you wish to leave the raffle.";

	$linkMsg .= "\n\n Time left: $tleft seconds.";

    $link = bot::makeLink("here", $linkMsg);
        
	if ($tleft < 0 && $timesincelastmsg > 15) {
		$memNum = count($this->vars["Raffles"]["rafflees"]);
		if($memNum > 0) {
		    $winningnum = rand(0, $memNum-1);
		    $winner = $this->vars["Raffles"]["rafflees"][$winningnum];
		    $msg = "Winner of the raffle for $item is $winner. See $owner to collect your prize.";
		} else {
		    $msg = "No raffle entrants, no winner for $item.";
		}
		$this->vars["Raffles"]["inprog"] = 0;
	} elseif ($tleft < 240 && $tleft > 60 && $timesincelastmsg > 60) {
		$msg = "<yellow>Reminder:<end> Raffle for $item <highlight>JOIN NOW<end>. Click $link to join.";
	} elseif ($tleft < 60 && $tleft > 30 && $timesincelastmsg > 30) {
		$msg = "<yellow>Reminder:<end> Raffle for $item <highlight>1 minute<end> left. Click $link to join.";
	} elseif ($tleft < 30 && $tleft > 15 && $timesincelastmsg > 15) {
		$msg = "<yellow>Reminder:<end> Raffle for $item <highlight>30-Seconds<end> left. Click $link to join.";
	} elseif ($tleft < 15 && $tleft > 0 && $timesincelastmsg > 15) {
		$msg = "<yellow>Reminder:<end> Raffle for $item has <highlight>15-Seconds<end> left. Click $link to join.";
	}

	if ($msg != "") {
	    $this->vars["Raffles"]["lastmsgtime"] = time();
	    bot::send($msg, "org");
		bot::send($msg, "prv");
	}
?>