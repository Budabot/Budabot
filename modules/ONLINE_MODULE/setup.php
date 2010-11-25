<?php

// Set Delay for notify on/off(prevent spam from org roster module)
$this->vars["onlinedelay"] = time() + $this->settings["CronDelay"] + 60;

?>