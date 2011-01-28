<?php

// Set Delay for notify on/off (prevent bot from spamming logoffs/logons when the bot first logs on)
$this->vars["onlinedelay"] = time() + $this->settings["CronDelay"] + 60;

?>