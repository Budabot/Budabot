<?php
   /*
   ** Module: PREMADE_IMPLANT
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you search for the implants in the premade implant booths.
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): Fall 2008
   ** Date(last modified): 9-Mar-2010
   ** 
   ** Copyright (C) 2008 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** This module and all it's files and contents are licensed
   ** under the GNU General Public License.  You may distribute
   ** and modify this module and it's contents freely.
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

require_once('functions.php');

if (preg_match("/^premade (.*)$/i", $message, $arr)) {

	$msg = "";

  	$searchTerms = strtolower($arr[1]);
  	$results = null;
  	
  	if ($searchTerms == '') {
	  	
	  	$msg = "usage: <symbol>premade &lt;profession&gt;|&lt;slot&gt;|&lt;modifier&gt;";
	  	
	} else {
		
		if ($searchTerms == 'enfo' || $searchTerms == 'enforcer') {
			$searchTerms = 'enf';
		} else if ($searchTerms == 'advy' || $searchTerms == 'adventurer') {
			$searchTerms = 'adv';
		} else if ($searchTerms == 'doctor') {
			$searchTerms = 'doc';
		} else if ($searchTerms == 'engi' || $searchTerms == 'engineer') {
			$searchTerms = 'eng';
		} else if ($searchTerms == 'fix') {
			$searchTerms = 'fixer';
		} else if ($searchTerms == 'keep') {
			$searchTerms = 'keeper';	
		} else if ($searchTerms == 'sold' || $searchTerms == 'soldier') {
			$searchTerms = 'sol';	
		} else if ($searchTerms == 'trad') {
			$searchTerms = 'trader';	
		}
	  	
		if ($searchTerms == 'adv' || $searchTerms == 'agent' || $searchTerms == 'crat' || $searchTerms == 'doc' ||
	  		$searchTerms == 'enf' || $searchTerms == 'eng' || $searchTerms == 'fixer' || $searchTerms == 'keeper' ||
	  		$searchTerms == 'ma' || $searchTerms == 'mp' || $searchTerms == 'nt' || $searchTerms == 'sol' || $searchTerms == 'trader') {
		  		
		  	$results = searchByProfession($searchTerms);
		  	
		} else if ($searchTerms == 'head' || $searchTerms == 'eye' || $searchTerms == 'ear' || $searchTerms == 'rarm' ||
			$searchTerms == 'chest' || $searchTerms == 'larm' || $searchTerms == 'rwrist' || $searchTerms == 'waist' ||
			$searchTerms == 'lwrist' || $searchTerms == 'rhand' || $searchTerms == 'leg' || $searchTerm == 'lhand' ||
			$searchTerms == 'feet') {
				
			$results = searchBySlot($searchTerms);
	
	  	} else {
			
		  	$results = searchByModifier($searchTerms);
	  	}
	  	
	  	if ($results != null) {
		  	$msg = formatResults($results);
	  	} else if ($msg == '') {
			$msg = "No results found.";
	  	}
  	}
  
    bot::send($msg, $sendto);
}
?>
