<?php

// Set Delay for notify on/off (prevent bot from spamming logoffs/logons when the bot first logs on)
$chatBot->vars["onlinedelay"] = time() + $chatBot->settings["CronDelay"] + 60;

?>