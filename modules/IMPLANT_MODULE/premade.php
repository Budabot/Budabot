<?php

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
		$blob = formatResults($results);
		$blob .= "\n\nWritten by Tyrence (RK2)";
		$blob .= "\nOriginal Premade Implant Database provided by Demoder (RK2)";
		$msg = Text::make_blob("Implant Search Results for '$searchTerms'", $blob);
	} else {
		$msg = "No results found.";
	}

    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
