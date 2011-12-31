<?php
  
function getTrickleResults($abilities) {
	$chatBot = Registry::getInstance('chatBot');
	$db = Registry::getInstance('db');

	$sql = "
		SELECT 
			groupName,
			name,
			amountAgi,
			amountInt,
			amountPsy,
			amountSta,
			amountStr,
			amountSen,
			(amountAgi * {$abilities['agi']}
				+ amountInt * {$abilities['int']}
				+ amountPsy * {$abilities['psy']}
				+ amountSta * {$abilities['sta']}
				+ amountStr * {$abilities['str']}
				+ amountSen * {$abilities['sen']}) AS amount
		FROM
			trickle
		GROUP BY
			groupName,
			name,
			amountAgi,
			amountInt,
			amountPsy,
			amountSta,
			amountStr,
			amountSen
		HAVING
			amount > 0
		ORDER BY
			groupName,
			name";

	return $db->query($sql);
}

function formatOutput($results, $amount, &$abilities) {
	$msg = "";
	$groupName = "";
	forEach($results as $result) {

		if ($result->groupName != $groupName) {
			$groupName = $result->groupName;
			$msg .= "\n<tab><green>::: $groupName :::<end>\n";
		}

		$amount = $result->amount / 4;
		$msg .= "<yellow>$result->name<end> <orange>$amount<end>";
		
		/*
		forEach ($abilities as $ability => $value) {
			$ability = ucfirst($ability);
			$abilityField = "amount" . $ability;
			$abilityAmount = $result->$abilityField * 100;
			if ($abilityAmount != 0) {
				$msg .= " (" . $ability . " " . $abilityAmount . "%)";
			}
		}
		*/
		
		$msg .= "\n";
	}

	return $msg;
}

?>