<?php

if (preg_match("/^calc (.+)$/i", $message, $arr)) {
    $calc = strtolower($arr[1]);

	if (preg_match("/\\/0/", $arr[1])) {
		$msg = "You cannot divide by 0!";
		$chatBot->send($msg, $sendto);
		return;
	}
	
    //check if the calc string includes not allowed chars
    $calc_check = strspn($calc, "0123456789.,+-*x%()/\\ ");

    //If no wrong char found
    if ($calc_check == strlen($calc)) {
        $result = "";
        //Do the calculations
   		$calc = "\$result = ".$calc.";";
        eval($calc);
        //If calculation is succesfull
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
