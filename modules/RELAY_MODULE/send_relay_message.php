<?php

if (substr($message, 0, 1) == $this->settings['externalrelaysymbol']) {
	AOChat::send_privgroup($this->settings['externalrelaybot'], "$sender: " . substr($message, 1));
}

?>