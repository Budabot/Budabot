<?php

if (!isset($this->vars["Raffles"])) {
    $this->vars["Raffles"] = array(
        "running" => false,
        "owner" => NULL,
        "item" => NULL,
        "count" => NULL,
        "time" => NULL,
        "rafflees" => NULL,
        "lastresult" => NULL,
        "lastmsgtime" => NULL,
        "sendto" => NULL
        );
}

?>