<?php
   /*
   ** Module: IMPLANT
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you lookup information on a specific ql of implant.
   ** Version: 2.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13-October-2007
   ** Date(last modified): 9-Mar-2010
   **
   ** Copyright (C) 2009 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos:
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

// premade implant functions
function searchByProfession($profession) {
	$db = DB::get_instance();

	$sql = "SELECT * FROM premade_implant WHERE profession = '$profession' ORDER BY slot";
	$db->query($sql);
	return $db->fObject("all");
}

function searchBySlot($slot) {
	$db = DB::get_instance();

	$sql = "SELECT * FROM premade_implant WHERE slot = '$slot' ORDER BY shiny, bright, faded";
	$db->query($sql);
	return $db->fObject("all");
}

function searchByModifier($modifier) {
	$db = DB::get_instance();

	$sql = "SELECT * FROM premade_implant WHERE shiny LIKE '%$modifier%' OR bright LIKE '%$modifier%' OR faded LIKE '%$modifier%'";
	$db->query($sql);
	return $db->fObject("all");
}

function formatResults($implants) {
	$count = 0;
	forEach ($implants as $implant) {
		$blob .= getFormattedLine($implant);
		$count++;
	}
		
	$blob .= "\n\nWritten by Tyrence(RK2)";
	
	return $blob;
}

function getFormattedLine($implant) {
	return "<green>$implant->profession<end> $implant->slot <white>$implant->ability<end> <font color='#FFFF00'>$implant->shiny</font> <font color='#FFA020'>$implant->bright</font> <font color='#FF8040'>$implant->faded</font>\n";
}

// implant functions
function getRequirements($ql) {
	$db = DB::get_instance();

	$sql = "SELECT * FROM implant_requirements WHERE ql = $ql";

	$db->query($sql);

	$row = $db->fObject();
	add_info($row);

	return $row;
}

function findMaxImplantQlByReqs($ability, $treatment) {
	$db = DB::get_instance();

	$sql = "SELECT * FROM implant_requirements WHERE ability <= $ability AND treatment <= $treatment ORDER BY ql DESC LIMIT 1";

	$db->query($sql);
	
	$row = $db->fObject();
	add_info($row);

	return $row;
}

function formatClusterBonuses(&$obj) {
	$msg = "\n For ql $obj->ql clusters,\n\n";

	$msg .= "You will gain for most skills:\n" .
		"<tab>Shiny    $obj->skillShiny ($obj->lowestSkillShiny - $obj->highestSkillShiny)\n" .
		"<tab>Bright    $obj->skillBright ($obj->lowestSkillBright - $obj->highestSkillBright)\n" .
		"<tab>Faded   $obj->skillFaded ($obj->lowestSkillFaded - $obj->highestSkillFaded)\n" .
		"-----------------------\n" .
		"<tab>Total   $obj->skillTotal\n";

	$msg .= "\n\n";

	$msg .= "You will gain for abilities:\n" .
		"<tab>Shiny    $obj->abilityShiny ($obj->lowestAbilityShiny - $obj->highestAbilityShiny)\n" .
		"<tab>Bright    $obj->abilityBright ($obj->lowestAbilityBright - $obj->highestAbilityBright)\n" .
		"<tab>Faded   $obj->abilityFaded ($obj->lowestAbilityFaded - $obj->highestAbilityFaded)\n" .
		"-----------------------\n" .
		"<tab>Total   $obj->abilityTotal\n";


	if ($obj->ql > 250) {

		$msg .= "\n\nRequires Title Level 6";

	} else if ($obj->ql > 200) {

		$msg .= "\n\nRequires Title Level 5";
	}

	$msg .= "\n\nMinimum ql for clusters:\n\n" .
		"<tab>Shiny: $obj->minShinyClusterQl\n" .
		"<tab>Bright: $obj->minBrightClusterQl\n" .
		"<tab>Faded: $obj->minFadedClusterQl";

	return $msg;
}

function add_info(&$obj) {
	$db = DB::get_instance();
	
	if ($obj === null) {
		return;
	}
	
	_setHighestAndLowestQls($obj, 'abilityShiny');
	_setHighestAndLowestQls($obj, 'abilityBright');
	_setHighestAndLowestQls($obj, 'abilityFaded');
	_setHighestAndLowestQls($obj, 'skillShiny');
	_setHighestAndLowestQls($obj, 'skillBright');
	_setHighestAndLowestQls($obj, 'skillFaded');
	
	$obj->abilityTotal = $obj->abilityShiny + $obj->abilityBright + $obj->abilityFaded;
	$obj->skillTotal = $obj->skillShiny + $obj->skillBright + $obj->skillFaded;

	$obj->minShinyClusterQl = round($obj->ql * 0.86);
	$obj->minBrightClusterQl = round($obj->ql * 0.84);
	$obj->minFadedClusterQl = round($obj->ql * 0.82);

	// if implant ql is 201+, then clusters must be refined and must be ql 201+ also
	if ($obj->ql >= 201) {

		if ($obj->minShinyClusterQl < 201) {
			$obj->minShinyClusterQl = 201;
		}
		if ($obj->minBrightClusterQl < 201) {
			$obj->minBrightClusterQl = 201;
		}
		if ($obj->minFadedClusterQl < 201) {
			$obj->minFadedClusterQl = 201;
		}
	}
}

function _setHighestAndLowestQls(&$obj, $var) {
	$db = DB::get_instance();

	$varValue = $obj->$var;

	$sql = "SELECT MAX(ql) as max, MIN(ql) as min FROM implant_requirements WHERE $var = $varValue";
	$db->query($sql);
	$row = $db->fObject();

	// camel case var name
	$tempNameVar = ucfirst($var);
	$tempHighestName = "highest$tempNameVar";
	$tempLowestName = "lowest$tempNameVar";

	$obj->$tempLowestName = $row->min;
	$obj->$tempHighestName = $row->max;
}

?>
