<?php

if (!isset($chatBot->data["Raffles"])) {
    $chatBot->data["Raffles"] = array(
        "running" => false,
        "owner" => NULL,
        "item" => NULL,
        "count" => NULL,
        "time" => NULL,
        "rafflees" => NULL,
        "lastresult" => NULL,
		"nextmsgtime" => NULL,
        "sendto" => NULL
    );
}

?>