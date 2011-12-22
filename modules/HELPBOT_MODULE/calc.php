<?php

if (preg_match("/^calc (.+)$/i", $message, $arr)) {
    $calc = strtolower($arr[1]);

    // check if the calc string includes not allowed chars
    $calc_check = strspn($calc, "0123456789.,+-*x%()/\\ ");

    // if no wrong char found
    if ($calc_check == strlen($calc)) {
        $result = "";
        // do the calculations
   		$calc = "\$result = ".$calc.";";
        eval($calc);
        // if calculation is succesful
   		if (is_numeric($result)) {
            $result = round($result, 4);
            $msg = $arr[1]." = <highlight>".$result."<end>";
			$chatBot->send($msg, $sendto);
        } else {
        	$syntax_error = true;
		}
    } else {
    	$syntax_error = true;
	}
} else {
	$syntax_error = true;
}

?>
