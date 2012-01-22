<?php
   
if (preg_match("/^(xitems|litems|items) ([0-9]+) (.+)$/i", $message, $arr)) {
    $ql = $arr[2];
    if (!($ql >= 1 && $ql <= 500)) {
        $msg = "Invalid Ql specified (1-500)";
        $sendto->reply($msg);
        return;
    }
    $search = $arr[3];
} else if (preg_match("/^(xitems|litems|items) (.+)$/i", $message, $arr)) {
    $search = $arr[2];
    $ql = false;
} else {
  	$syntax_error = true;
	return;
}

// ao automatically converts '&' to '&amp;', so we convert it back
$search = htmlspecialchars_decode($search);
$msg = find_items_from_local($search, $ql);
$sendto->reply($msg);

?>