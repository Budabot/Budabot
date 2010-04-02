<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Create/Changes/Shows Team setup
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.02.2006
   ** Date(last modified): 07.10.2006
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

global $lfg_teams;
global $teams;
global $team_names;
if(eregi("^team 0 ([a-z0-9]+)$", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$uid = AoChat::get_uid($name);
    if(!$uid) {
		$msg = "Player <highlight>".$name."<end> does not exist.";
	    bot::send($msg);
	} else {
	 	$found = false;
	  	foreach($teams as $key => $value) {
			if($teams[$key][$name] == true) {
			 	$found = true;
			  	unset($teams[$key][$name]);
			  	break;
			}
	  	}
	  	if($found) {
	  	 	if(isset($this->chatlist[$name]))
			  	$lfg_teams[$name]["team"] = "none";
		  	$msg = "<highlight>$name<end> has been removed from his Team.";
		} else
			$msg = "<highlight>$name<end> hasn´t been assigned to a Team.";
			
	  	bot::send($msg);	  	
	} 
} elseif(eregi("^team ([0-9]+) ([a-z0-9]+)$", $message, $arr)){
	$name = ucfirst(strtolower($arr[2]));
	$uid = AoChat::get_uid($name);
  	$team_num = $arr[1];	
	$team_members = count($teams[$team_num]);
    if(!$uid) {
		$msg = "Player <highlight>".$name."<end> does not exist.";
	    bot::send($msg);
	} elseif(!isset($this->chatlist[$name])) {
	  	$msg = "Player <highlight>".$name."<end> isn´t on this bot.";
		bot::send($msg);
	} elseif($team_members == 6) {
	  	$msg = "Team <highlight>#$team_num<end> has already <highlight>6<end> Members.";
	  	bot::send($msg);
	} elseif($team_num > 10) {
	  	$msg = "Sorry but there can´t be more teams then 10!.";
	  	bot::send($msg); 	
	} else {
	  	foreach($teams as $key => $value) {
			if($teams[$key][$name] == true) {
			  	unset($teams[$key][$name]);
			  	break;
			}
	  	}
	  	$teams[$team_num][$name] = true;
	  	$lfg_teams[$name]["team"] = $team_num;
	  	$msg = "<highlight>$name<end> has been assigned to Team <highlight>#$team_num<end>.";
	  	bot::send($msg);
	}
} elseif(eregi("^team ([0-9a-z ]+) ([1-9])$", $message, $arr)) {
  	$team_names[$arr[2]] = $arr[1];
  	$msg = "Team <highlight>#{$arr[2]}<end> has been renamed to <highlight>$arr[1]<end>.";
  	bot::send($msg);
} elseif(eregi("^team ([0-9a-z ]+)$", $message, $arr)) {
  	$new_team = count($teams) + 1;
  	if($new_team == 11)
  		$msg = "Sry but you can´t create more then 10teams.";
  	else {
  	  	if(!is_array($teams))
  	  		$new_team = 1;
		$team_names[$new_team] = $arr[1];
		$teams[$new_team] = array();
			
	  	$msg = "A new Team with the name <highlight>{$arr[1]}<end> has been created.";	    	
	}
  	bot::send($msg);
} elseif(eregi("^teams clear$", $message)) {
  	$teams = "";
  	$team_names = "";
	foreach($lfg_teams as $key => $value)
		$lfg_teams[$key]["team"] = "none";
  	$msg = "Teams has been cleared by <highlight>$sender<end>";
  	bot::send($msg);
} elseif(eregi("^teams$", $message)) {
	if(!is_array($teams))
		$msg = "No Teams build yet.";
	else {
	  	//Show current Team setup
		$msg = "Teams: ";
		foreach($teams as $key => $value) {
		  	if(isset($team_names[$key]))
			  	$msg .= "\nTeam <highlight>{$team_names[$key]}<end> (".count($value)."):";
			else
			  	$msg .= "\nTeam <highlight>$key<end> (".count($value)."):";			
		  	foreach($value as $key => $val) {
		  	  	switch ($lfg_teams[$key]["prof"]) {
			        case "Adventurer":
			            $prof = "Adv";
			            break;
			        case "Bureaucrat":
			            $prof = "Crat";
			            break;
			        case "Doctor":
			            $prof = "Doc";
			            break;
			        case "Enforcer":
			            $prof = "Enf";
			            break;
			        case "Engineer":
			            $prof = "Eng";
			            break;
			        case "Fixer":
			            $prof = "Fix";
			            break;
			        case "Martial Artist":
			            $prof = "MA";
			            break;
			        case "Meta-Physicist":
			            $prof = "MP";
			            break;
			        case "Nano-Technician":
			            $prof = "NT";
			            break;
			        case "Soldier":
			            $prof = "Sol";
			            break;
			        default:
			        	$prof = $lfg_teams[$key]["prof"];
			        	break;
				}
			 $msg .= " [<highlight>$key<end> {$lfg_teams[$key]["level"]}/$prof]";	   
			}	
		}
	}
	bot::send($msg);
	
	//Send Tell with Administration part
	$list .= "<header>::::: Team Administration :::::<end>\n\n";
	$list .= "<a href='chatcmd:///g <myname> <symbol>teams clear'>Clear Teams</a>\n\n";
	if($teams == "")
		$list .= "No Teams build yet.\n\n";
	else {
	  	$list .= "<u>Current Team Setup:</u>";
		foreach($teams as $key => $value) {
		  	$num_teams = count($value);
		  	$change_teamname = "(<a href='chatcmd:///g <myname> <symbol>team TTeam $key'>TTeam</a> <a href='chatcmd:///g <myname> <symbol>team Backup TTeam $key'>Backup TTeam</a> <a href='chatcmd:///g <myname> <symbol>team DTeam $key'>DTeam</a>)";
			if(isset($team_names[$key]))
			  	$list .= "\nTeam <highlight>{$team_names[$key]}<end> ($num_teams) $change_teamname:";
			else
			  	$list .= "\nTeam <highlight>$key<end> ($num_teams) $change_teamname:";	

			if($num_teams == 0)
				$list .= "\n<tab><highlight>None<end>.";
			else {
			  	foreach($value as $key => $val) {
			  	  	$rem = "<a href='chatcmd:///g <myname> <symbol>team 0 $key'>Remove</a>";
			  	  	$list .= "\n<tab><highlight>$key<end> ({$lfg_teams[$key]["level"]}, {$lfg_teams[$key]["prof"]}) $rem";
			  	}
			}
			$list .= "\n";
		}
	}
	$list .= "\n<u>Unteamed Players</u>";
	if($num_teams == count($lfg_teams))
		$list .= "\n<highlight>None<end>.";
	else {
		unset($prof);
	  	foreach($lfg_teams as $player => $info) {
		    if($info["team"] == "none")
				$prof[$info["prof"]][] = $player;
		}
		ksort($prof);
		reset($prof);
		foreach($prof as $key => $value) {
            $list .= "\n<highlight>$key<end>\n";
			foreach($value as $player) {
				$t = "";
			  	for($i = 1;$i <= 5; $i++)
					$t .= " <a href='chatcmd:///g <myname> <symbol>team $i $player'>$i</a>";
		        $list .= " - Lvl {$lfg_teams[$player]["level"]} <highlight>$player<end> $t\n";
		    }
		}
	}
	$msg = bot::makeLink("Team Administration", $list);
	bot::send($msg, $sender);	
} else
	$syntax_error = true;
?>