<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Relays tower messages
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 02.12.2005
   ** Date(last modified): 23.08.2007
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

$colorlabel = "<font color=#00DE42>";
$colorvalue = "<font color=#63AD63>";

if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) just entered a state of war! (.+) attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \\((\\d+),(\\d+)\\)\\.$/i", $message, $arr)) {
	$att_side = $arr[1];
	$att_guild = $arr[2];
	$att_player = $arr[3];
	$def_side = $arr[4];
	$def_guild = $arr[5];
	$zone = $arr[6];
	$coordx = $arr[7];
	$coordy = $arr[8];
} else if (preg_match("/^(.+) just attacked the (Clan|Neutral|Omni) organization (.+)'s tower in (.+) at location \(([0-9]+), ([0-9]+)\).(.*)$/i", $message, $arr)) {
	$att_player = $arr[1];
	$def_side = $arr[2];
	$def_guild = $arr[3];
	$zone = $arr[4];
	$coordx = $arr[5];
	$coordy = $arr[6];
} else if (preg_match("/^The (Clan|Neutral|Omni) organization (.+) attacked the (Clan|Neutral|Omni) (.+) at their base in (.+). The attackers won!!$/i", $message, $arr)) {
	$db->query("INSERT INTO tower_result_<myname> (`time`, `win_guild`, `win_side`, `lose_guild`, `lose_side`) VALUES ('".time()."', '".str_replace("'", "''", $arr[2])."', '".$arr[1]."', '".str_replace("'", "''", $arr[4])."', '".$arr[3]."')");
}

if ($def_guild) {

	$whois = new whois($att_player, $this->vars["dimension"]);
	if (!$att_side) {
		$att_side = $whois->faction;
	}
	if (!$att_side) {
		$att_side = "Unknown";
	}

	$site_info = Towers::get_closest_site($zone, $coordx, $coordy);
	if ($site_info == false) {
		$more = "[<red>UNKNOWN AREA!<end>]";
	} else {

		// Beginning of the 'more' window
		$link  = "<header>:::::: Advanced Tower Infos :::::<end>\n\n";
		$link .= "Send msg to attacker: [<a href='chatcmd:///tell $att_player'>Empty</a>] ";
		$link .= "[<a href='chatcmd:///tell $att_player You just made me your new enemy by attacking those towers.'>Aggressive</a>] ";
		$link .= "[<a href='chatcmd:///tell $att_player Keep up the good work.  Let me know if you need some help with those towers.'>Supportive</a>]\n\n";
		
		$link .= "<highlight>Attacker:<end> <font color=#DEDE42>";
		if ($whois->firstname) {
			$link .= $whois->firstname." ";
		}
		$link .= "&quot;".$att_player."&quot; ";
		if ($whois->lastname)  {
			$link .= $whois->lastname." ";
		}
		$link .= "<end>\n";
		
		if ($whois->breed) {
			$link .= $colorlabel."Breed:<end> ".$colorvalue.$whois->breed."<end>\n";
		}
		if ($whois->gender) {
			$link .= $colorlabel."Gender:<end> ".$colorvalue.$whois->gender."<end>\n";
		}

		if ($whois->prof) {
			$link .= $colorlabel."Profession:<end> ".$colorvalue.$whois->prof."<end>\n";
		}
		if ($whois->level) {
			$link .= $colorlabel."Level:<end> $colorvalue";
			if ($whois->prof == "Unknown") {
				$link .= "Unknown<end>\n";
			} else {
				$level_info = Level::get_level_info($whois->level);
				$link .= $whois->level."<end> <red>({$level_info->pvpMin}-{$level_info->pvpMax})<end>\n";
			}
		}
		
		if ($whois->ai_level) {
			$link .= $colorlabel."AI Level:<end> ".$colorvalue.$whois->ai_level;
			if ($whois->ai_rank) {
				$link .= " - ".$whois->ai_rank;
			}
			$link .= "<end>\n";
		}
			
		$link .= $colorlabel."Alignment:<end> ".$colorvalue.$att_side."<end>\n";
		
		if ($att_guild) {
			if ($att_side == "Omni") {
				$link .= $colorlabel."Detachment:<end> ".$colorvalue.$att_guild."<end>\n";
			} else {
				$link .= $colorlabel."Clan:<end> ".$colorvalue.$att_guild."<end>\n";
			}
			if ($whois->rank) {$link .= $colorlabel."Organization Rank:<end> <white>".$whois->rank."<end>\n";}
		}


		$link .= "\n";

		$link .= "<highlight>Defender:<end> ".$colorvalue.$def_guild."<end>\n";
		$link .= $colorlabel."Alignment:<end> ".$colorvalue.$def_side."<end>\n\n";


		$link .= "<highlight>Playfield:<end> ".$colorvalue.$site_info->playfield." (<highlight>#".$site_info->hugemaploc."<end> : {$site_info->low_level}-{$site_info->high_level})<end>\n";
		$link .= $colorlabel."Location:<end> ".$colorvalue.$site_info->location." (".$coordx." x ".$coordy.")<end>\n";

		$more = "[".bot::makeLink("more", $link)."]";
	}
	
	// Prep for if our org is being attacked.
	if(strtolower($def_guild) == strtolower($this->vars["my guild"])) {
		$wedefend = true;
		$msg = "<red>We are under attack!<end> ";
	} else {
		$wedefend = false;
	}
	$targetorg = "<".strtolower($def_side).">".$def_guild."<end>";

	// Starting tower message to org/private chat
	$msg .= "<font color=#FF67FF>[";



	// tower_attack_spam >= 2 (normal) includes attacker stats
	if ($this->settings["tower_attack_spam"] >= 2) {

		if ($whois->prof == "Unknown") {$msg .= "<".strtolower($att_side).">$att_player<end> (Unknown";}
		else {
			if(!$att_guild){
				$msg .= "<".strtolower($att_side).">$att_player<end>";
			} else {
				$msg .= "<font color=#AAAAAA>$att_player<end>";
			}
			$msg .= " (level <font color=#AAAAAA>$whois->level<end>";
			if ($whois->ai_level) {
				$msg .= "/<green>$whois->ai_level<end>";
			}
			$msg .= ", $whois->breed <font color=#AAAAAA>$whois->prof<end>";
		}

		if(!$att_guild) {
			$msg .= ")";
		} else if (!$whois->rank) {
			$msg .= "<".strtolower($att_side).">$att_guild<end>)";
		} else {
			$msg .= ", $whois->rank of <".strtolower($att_side).">$att_guild<end>)";
		}
		
	} else if ($att_guild) {
		$msg .= "<".strtolower($att_side).">$att_guild<end>";
	} else {
		$msg .= "<".strtolower($att_side).">$att_player<end>";
	}

	$msg .= " attacked ".$targetorg."] ";

	// tower_attack_spam >= 3 (full) includes location.
	if ($this->settings["tower_attack_spam"] >= 3) {
		if ($site_info->hugemaploc) {
			$hugemaploc = "<font color=#AAAAAA>#".$site_info->hugemaploc."<end>";
		}
		$msg .= "[".$zone." $hugemaploc (".$coordx." x ".$coordy.")] ";
	}

	$msg .= "$more<end>";

	$d = $this->settings["tower_faction_def"];
	$a = $this->settings["tower_faction_atk"];
	$s = $this->settings["tower_attack_spam"];

	if ($wedefend || ($s > 0 && (
        (strtolower($def_side) == "clan"    && ($d & 1)) ||
	    (strtolower($def_side) == "neutral" && ($d & 2)) ||
        (strtolower($def_side) == "omni"    && ($d & 4)) ||
        (strtolower($att_side) == "clan"    && ($a & 1)) ||
	    (strtolower($att_side) == "neutral" && ($a & 2)) ||
        (strtolower($att_side) == "omni"    && ($a & 4)) ))) {

		// Won't need these 4 lines for 0.7.0
		$msg = str_replace("<neutral>", "<font color='#EEEEEE'>", $msg);
		$msg = str_replace("<omni>", "<font color='#00FFFF'>", $msg);
		$msg = str_replace("<clan>", "<font color='#F79410'>", $msg);
		$msg = str_replace("<unknown>", "<font color='#FF0000'>", $msg);

    	bot::send($msg, "guild", true);
	}

	$sql = "INSERT INTO tower_attack_<myname> (`time`, `att_guild`, `att_side`, `att_player`, `att_level`, `att_profession`,
				`def_guild`, `def_side`, `zone`, `x`, `y`) VALUES ('".time()."', '".str_replace("'", "''", $att_guild)."', '".$att_side."',
				'".$att_player."', '".$whois->level."', '".$whois->prof."', '".str_replace("'", "''", $def_guild)."', '".$def_side."',
				'".$zone."', '".$coordx."', '".$coordy."')";

	$db -> query($sql);
}

?>
