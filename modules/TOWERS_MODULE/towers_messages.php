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
	// Guess I'll copy Beaker's data.  Not sure if FC tweaked with the lvl*.8, lvl/.8 method.
	$pvp = array("0", "1-1", "2-3", "2-4", "3-5", "4-6", "5-10", "6-10", "6-10", "6-11", "6-13", 
		"9-14", "10-15", "10-16", "11-18", "12-21", "13-21", "14-22", "14-23", "15-24", "16-25", 
		"17-26", "18-28", "18-29", "19-30", "20-31", "21-33", "22-34", "22-35", "23-36", "24-38", 
		"25-39", "26-40", "26-41", "27-43", "28-44", "29-45", "30-46", "30-48", "31-49", "32-50", 
		"33-51", "34-54", "34-54", "35-55", "36-56", "37-58", "38-59", "38-60", "39-61", "40-63", 
		"41-64", "42-65", "42-66", "42-68", "44-69", "45-70", "46-71", "46-73", "47-74", "48-75", 
		"49-76", "50-78", "50-79", "51-80", "52-81", "53-83", "54-84", "54-85", "55-86", "56-88", 
		"57-89", "58-90", "58-91", "59-93", "60-94", "61-95", "62-96", "62-98", "63-99", "64-100", 
		"65-101", "66-103", "66-104", "67-105", "68-106", "69-108", "70-109", "70-110", "71-111", "72-113", 
		"73-114", "74-115", "74-116", "75-118", "76-119", "77-120", "78-121", "78-123", "79-124", "80-125", 
		"81-126", "82-128", "82-129", "83-130", "84-131", "85-133", "86-134", "86-135", "87-136", "88-138", 
		"89-139", "90-140", "90-141", "91-143", "92-144", "93-145", "94-146", "94-148", "95-149", "96-150", 
		"97-151", "98-153", "98-155", "99-155", "100-156", "101-158", "102-159", "102-160", "103-161", "104-163", 
		"105-164", "106-165", "106-166", "107-168", "108-169", "109-170", "110-171", "110-173", "111-174", "112-175", 
		"113-176", "114-178", "114-179", "115-180", "116-181", "117-183", "118-184", "118-185", "119-186", "120-188", 
		"121-189", "122-190", "122-191", "123-192", "123-194", "125-195", "126-196", "126-198", "127-199", "128-200", 
		"129-201", "130-203", "130-204", "131-205", "132-206", "133-208", "134-209", "134-210", "135-211", "136-213", 
		"137-214", "138-215", "138-218", "139-219", "140-220", "141-220", "142-220", "142-220", "143-220", "144-220", 
		"145-220", "146-220", "146-220", "147-220", "148-220", "149-220", "150-220", "150-220", "151-220", "152-220", 
		"153-220", "154-220", "154-220", "155-220", "156-220", "157-220", "157-220", "158-220", "159-220", "160-220", 
		"161-220", "161-220", "162-220", "163-220", "164-220", "165-220", "165-220", "166-220", "167-220", "168-220", 
		"169-220", "169-220", "170-220", "171-220", "172-220", "172-220", "172-220", "173-220", "174-220", "175-220");

	$whois = new whois($att_player, $this->vars["dimension"]);
	if (!$att_side) {$att_side = $whois->faction;}
	if (!$att_side) {$att_side = "Unknown";}

	$db->query("SELECT * FROM towerranges WHERE `playfield` LIKE '$zone'");
	if($db->numrows() == 0) {
		$more = "[<red>UNKNOWN AREA!<end>]";
	} else {
		while($row = $db->fObject()) {
			$dist[$row->id] = round(sqrt(pow(($coordx - $row->coordx), 2) + pow(($coordy - $row->coordy), 2)));
			$data[$row->id]["level"] = $row->low_level."-".$row->high_level;
			$data[$row->id]["playfield"] = $row->playfield;
			$data[$row->id]["location"] = $row->location;
			$data[$row->id]["hugemaploc"] = $row->hugemaploc;
		}
		asort($dist);
		reset($dist);
		$key = key($dist);

		// Beginning of the 'more' window
		$link  = "<header>:::::: Advanced Tower Infos :::::<end>\n\n";
		$link .= "Send msg to attacker: [<a href='chatcmd:///tell $att_player'>Empty</a>] ";
		$link .= "[<a href='chatcmd:///tell $att_player You just made me your new enemy by attacking those towers.'>Aggressive</a>] ";
		$link .= "[<a href='chatcmd:///tell $att_player Keep up the good work.  Let me know if you need some help with those towers.'>Supportive</a>]\n\n";
		
		$link .= "<highlight>Attacker:<end> <font color=#DEDE42>";
		if ($whois->firstname) { $link .= $whois->firstname." ";}
		$link .= "&quot;".$att_player."&quot; ";
		if ($whois->lastname)  { $link .= $whois->lastname." ";}
		$link .= "<end>\n";
		
		if ($whois->breed) {$link .= $colorlabel."Breed:<end> ".$colorvalue.$whois->breed."<end>\n";}
		if ($whois->gender) {$link .= $colorlabel."Gender:<end> ".$colorvalue.$whois->gender."<end>\n";}
		if ($whois->prof_title) {

			$lvl = $whois->level;
			if     ($lvl < 15) {$titlenum=1;}
			elseif ($lvl >= 15 && $lvl < 50) {$titlenum=2;}
			elseif ($lvl >= 50 && $lvl < 100) {$titlenum=3;}
			elseif ($lvl >= 100 && $lvl < 150) {$titlenum=4;}
			elseif ($lvl >= 150 && $lvl < 190) {$titlenum=5;}
			elseif ($lvl >= 190 && $lvl < 205) {$titlenum=6;}
			else   {$titlenum=7;}

			$link .= $colorlabel."Profession Title:<end> ".$colorvalue.$whois->prof_title." (TitleLevel ".$titlenum.")<end>\n";
		}	
		if ($whois->prof) {$link .= $colorlabel."Profession:<end> ".$colorvalue.$whois->prof."<end>\n";}
		if ($whois->level) {
			$link .= $colorlabel."Level:<end> $colorvalue";
			if ($whois->level > 200) {
				$link .= "200<end> ".$colorlabel."Shadowlevel:<end> ".$colorvalue.($whois->level-200)."<end> <red>(".$pvp[$whois->level].")<end>\n";
			} elseif ($whois->prof == "Unknown") {
				$link .= "Unknown<end>\n";
			} else {
				$link .= $whois->level."<end> <red>(".$pvp[$whois->level].")<end>\n";
			}
		}
		
		if ($whois->ai_level) {
			$link .= $colorlabel."AI Level:<end> ".$colorvalue.$whois->ai_level;
			if ($whois->ai_rank) {$link .= " - ".$whois->ai_rank;}
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


		$link .= "<highlight>Playfield:<end> ".$colorvalue.$data[$key]["playfield"]." (<highlight>#".$data[$key]["hugemaploc"]."<end> : ".$data[$key]["level"].")<end>\n";
		$link .= $colorlabel."Location:<end> ".$colorvalue.$data[$key]["location"]." (".$coordx." x ".$coordy.")<end>\n";

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
			if(!$att_guild){ $msg .= "<".strtolower($att_side).">$att_player<end>";}
			else { $msg .= "<font color=#AAAAAA>$att_player<end>";}	
			$msg .= " (level <font color=#AAAAAA>$whois->level<end>";
			if ($whois->ai_level) {$msg .= "/<green>$whois->ai_level<end>";}
			$msg .= ", $whois->breed <font color=#AAAAAA>$whois->prof<end>";
		}

		if(!$att_guild) {$msg .= ")";}
		elseif (!$whois->rank) {$msg .= "<".strtolower($att_side).">$att_guild<end>)";}
		else {$msg .= ", $whois->rank of <".strtolower($att_side).">$att_guild<end>)";}
		
	} else {
		if ($att_guild) {$msg .= "<".strtolower($att_side).">$att_guild<end>";} 
		else {$msg .= "<".strtolower($att_side).">$att_player<end>";}		
	}

	$msg .= " attacked ".$targetorg."] ";

	// tower_attack_spam >= 3 (full) includes location.
	if ($this->settings["tower_attack_spam"] >= 3) {
		if ($data[$key]["hugemaploc"]) {$hugemaploc = "<font color=#AAAAAA>#".$data[$key]["hugemaploc"]."<end>";}
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
    	bot::send($msg, NULL, true);
	}

	$sql = "INSERT INTO tower_attack_<myname> (`time`, `att_guild`, `att_side`, `att_player`, `att_level`, `att_profession`,
				`def_guild`, `def_side`, `zone`, `x`, `y`) VALUES ('".time()."', '".str_replace("'", "''", $att_guild)."', '".$att_side."',
				'".$att_player."', '".$whois->level."', '".$whois->prof."', '".str_replace("'", "''", $def_guild)."', '".$def_side."',
				'".$zone."', '".$coordx."', '".$coordy."')";

	$db -> query($sql);
}

?>
