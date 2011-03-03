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

if (preg_match("/^premade (.*)$/i", $message, $arr)) {

	$searchTerms = strtolower($arr[1]);
  	$results = null;
	
	$profession = Util::get_profession_name($searchTerms);
	if ($profession != '') {
		$searchTerms = $profession;
		$results = searchByProfession($profession);
	} else if ($searchTerms == 'head' || $searchTerms == 'eye' || $searchTerms == 'ear' || $searchTerms == 'rarm' ||
		$searchTerms == 'chest' || $searchTerms == 'larm' || $searchTerms == 'rwrist' || $searchTerms == 'waist' ||
		$searchTerms == 'lwrist' || $searchTerms == 'rhand' || $searchTerms == 'legs' || $searchTerms == 'lhand' ||
		$searchTerms == 'feet') {

		$results = searchBySlot($searchTerms);
	} else {
		$results = searchByModifier($searchTerms);
	}
	
	if ($results != null) {
		$blob = "<header> :::::: Implant Search Results for '$searchTerms' :::::: <end>\n\n";
		$blob .= formatResults($results);
		$msg = Text::make_link("Implant Search Results for '$searchTerms'", $blob, 'blob');
	} else {
		$msg = "No results found.";
	}
  
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
