<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Relays tower messages
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 02.12.2005
   ** Date(last modified): 06.02.2007
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

if(eregi("^(.+) \((.+), (clan|neutral|omni)\) attacked (.+) \((clan|neutral|omni)\) in (.+) at ([0-9]+), ([0-9]+).$", $message, $arr)) {
    $att_guild = $arr[2];

    if(strtolower($arr[3]) == "clan")
        $att_side = "<red>".$arr[3]."<end>";
    elseif(strtolower($arr[3]) == "neutral")
        $att_side = "<white>".$arr[3]."<end>";
    elseif(strtolower($arr[3]) == "omni")
        $att_side = "<blue>".$arr[3]."<end>";

    $att_player = $arr[1];
    $def_guild = $arr[4];

    if(strtolower($arr[5]) == "clan")
        $def_side = "<red>".$arr[5]."<end>";
    elseif(strtolower($arr[5]) == "neutral")
        $def_side = "<white>".$arr[5]."<end>";
    elseif(strtolower($arr[5]) == "omni")
        $def_side = "<blue>".$arr[5]."<end>";

    $zone = $arr[6];
    $coordx = $arr[7];
    $coordy = $arr[8];
    
	$whois = new whois($att_player, $this->vars["dimension"]);
    
    $db->query("SELECT * FROM towerranges WHERE `playfield` LIKE '%$zone%'");
    if($db->numrows() == 0) {
      	if(strtolower($def_guild) == strtolower($this->vars["my guild"]))
			$msg = "<highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_guild."/".$att_side.") attacked the ".$def_side." organization <highlight>".$def_guild."큦<end> tower in ".$zone."(".$coordx."x".$coordy.").";	  		    
		else 
			$msg = "<red>The organisation <highlight>$att_guild<end> have declared war against us!!<end> <highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_guild."/".$att_side.") attacked our tower in ".$zone."(".$coordx."x".$coordy.").";
		
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
		$link  = "<header>:::::: Advanced Tower Infos :::::<end>\n\n";

		$link .= "<u>Infos about the Attacker</u>: <highlight>$att_player<end>\n";
		$link .= "<highlight>Level:<end> $whois->level\n";
		$link .= "<highlight>Profession:<end> $whois->prof\n";
		$link .= "<highlight>Guild:<end> $att_guild\n";
		$link .= "<highlight>Faction:<end> $att_side\n\n";

		$link .= "<u>Infos about the Defender guild</u>\n";
		$link .= "<highlight>Guild:<end> $def_guild\n";
		$link .= "<highlight>Faction:<end> $def_side\n\n";

		$link .= "<u>Infos about the attacked Land</u>\n";
		$link .= "<highlight>QL:<end> {$data[$key]["level"]}\n";
		$link .= "<highlight>Playfield:<end> {$data[$key]["playfield"]}\n";
		$link .= "<highlight>Location:<end> {$data[$key]["location"]}\n";
		$link .= "<highlight>Hugemap Location:<end> #{$data[$key]["hugemaploc"]}\n";
		
		$adv = bot::makeLink("Advanced Info", $link);
		if(strtolower($def_guild) == strtolower($this->vars["my guild"]))
			$msg = "<red>The organisation <highlight>$att_guild<end> have declared war against us!!<end> <highlight>$att_player<end> (Lvl <highlight>$whois->level<end>/<highlight>$whois->prof<end>/$att_guild/$att_side) attacked our tower in ".$zone."(".$coordx."x".$coordy.", HUGEMAP <highlight>#{$data[$key]["hugemaploc"]}<end>) $adv";
		else
			$msg = "<highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_guild."/".$att_side.") attacked the ".$def_side." organization <highlight>".$def_guild."큦<end> tower in ".$zone."(".$coordx."x".$coordy.", HUGEMAP <highlight>#{$data[$key]["hugemaploc"]}<end>) $adv";
	}
     
    bot::send($msg, "guild", true);
    bot::send($msg, NULL, true);    
    $db -> query("INSERT INTO tower_attack_<myname> (`time`, `att_guild`, `att_side`, `att_player`, `att_level`, `att_profession`,
                  `def_guild`, `def_side`, `zone`, `x`, `y`) VALUES ('".time()."', '".$att_guild."', '".$arr[3]."',
                  '".$att_player."', '".$whois->level."', '".$whois->prof."', '".$def_guild."', '".$arr[5]."',
                  '".$zone."', '".$coordx."', '".$coordy."')");
} elseif(eregi("^(.+) just attacked the (.+) organization (.+)'s tower in (.+) at location \(([0-9]+), ([0-9]+)\).(.*)$", $message, $arr)) {
    $att_player = $arr[1];
    $def_guild = $arr[3];

    if(strtolower($arr[2]) == "clan")
        $def_side = "<red>".$arr[2]."<end>";
    elseif(strtolower($arr[2]) == "neutral")
        $def_side = "<white>".$arr[2]."<end>";
    elseif(strtolower($arr[2]) == "omni")
        $def_side = "<blue>".$arr[2]."<end>";

    $zone = $arr[4];
    $x = $arr[5];
    $y = $arr[6];
    $whois = new whois($att_player, $this->vars["dimension"]);
    if(strtolower($whois->faction) == "clan")
        $att_side = "<red>".$whois->faction."<end>";
    elseif(strtolower($whois->faction) == "neutral")
        $att_side = "<white>".$whois->faction."<end>";
    elseif(strtolower($whois->faction) == "omni")
        $att_side = "<blue>".$whois->faction."<end>";

    $db->query("SELECT * FROM towerranges WHERE `playfield` LIKE '%$zone%'");
    if($db->numrows() == 0) {
		if(strtolower($def_guild) == strtolower($this->vars["my guild"]))
		    $msg = "<red>We are under attack!!<end> <highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_side.") attacked our tower in ".$zone."(".$x."x".$y.").";
		else
		    $msg = "<highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_side.") attacked the ".$def_side." organization <highlight>".$def_guild."큦<end> tower in ".$zone."(".$x."x".$y.").";
	} else {
		while($row = $db->fObject()) {
		 	$dist[$row->id] = round(sqrt(pow(($coordx - $row->coordx), 2) + pow(($coordy - $row->coordy), 2)));
			$data[$row->id]["level"] = $row->low_level."-".$row->high_level;;
			$data[$row->id]["playfield"] = $row->playfield;
			$data[$row->id]["location"] = $row->location;	
			$data[$row->id]["hugemaploc"] = $row->hugemaploc;
		}
		asort($dist);
		reset($dist);
		$key = key($dist);
		$link  = "<header>:::::: Advanced Tower Infos :::::<end>\n\n";

		$link .= "<u>Infos about the Attacker</u>: <highlight>$att_player<end>\n";
		$link .= "<highlight>Level:<end> $whois->level\n";
		$link .= "<highlight>Profession:<end> $whois->prof\n";
		$link .= "<highlight>Faction:<end> $att_side\n\n";

		$link .= "<u>Infos about the Defender guild</u>\n";
		$link .= "<highlight>Guild:<end> $def_guild\n";
		$link .= "<highlight>Faction:<end> $def_side\n\n";

		$link .= "<u>Infos about the attacked Land</u>\n";
		$link .= "<highlight>QL:<end> {$data[$key]["level"]}\n";
		$link .= "<highlight>Playfield:<end> {$data[$key]["playfield"]}\n";
		$link .= "<highlight>Location:<end> {$data[$key]["location"]}\n";
		$link .= "<highlight>Hugemap Location:<end> #{$data[$key]["hugemaploc"]}\n";
		
		$adv = bot::makeLink("Advanced Info", $link);
		if(strtolower($def_guild) == strtolower($this->vars["my guild"]))
		    $msg = "<red>We are under attack<end> <highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_side.") attacked our tower in ".$zone."(".$x."x".$y.", HUGEMAP <highlight>#{$data[$key]["hugemaploc"]}<end>) $adv";
		else
		    $msg = "<highlight>".$att_player."<end> (Lvl <highlight>".$whois->level."<end>/<highlight>".$whois->prof."<end>/".$att_side.") attacked the ".$def_side." organization <highlight>".$def_guild."큦<end> tower in ".$zone."(".$x."x".$y.", HUGEMAP <highlight>#{$data[$key]["hugemaploc"]}<end>) $adv";
	}
    bot::send($msg, "guild", true);    
    bot::send($msg, NULL, true);
    $db -> query("INSERT INTO tower_attack_<myname> (`time`, `att_side`, `att_player`, `att_level`, `att_profession`,
                  `def_guild`, `def_side`, `zone`, `x`, `y`) VALUES ('".time()."', '".$whois->faction."',
                  '".$att_player."', '".$whois->level."', '".$whois->prof."', '".$def_guild."', '".$arr[2]."',
                  '".$zone."', '".$x."', '".$y."')");
} elseif (eregi("^The (Clan|Omni|Neutral) organization (.+) attacked the (Clan|Omni|Neutral) (.+) at their base in (.+). The attackers won!!$", $message, $arr)) {
    $db->query("INSERT INTO tower_result_<myname> (`time`, `win_guild`, `win_side`, `lose_guild`, `lose_side`) VALUES ('".time()."', '".$arr[2]."', '".$arr[1]."', '".$arr[4]."', '".$arr[3]."')");
}
?>
