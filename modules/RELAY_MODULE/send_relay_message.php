<?php

if (($this->settings["relaybot"] != "Off") && ($args[2][0] != $this->settings["symbol"])) {
	$relayMessage = '';
	if ($this->settings['relaysymbol'] == 'Always relay') {
		$relayMessage = $message;
	} else if ($args[2][0] == $this->settings['relaysymbol']) {
		$relayMessage = substr($args[2], 1);
	}

	if ($relayMessage != '') {
		$msg = "grc <grey>[".$this->vars["my guild"]."] ".bot::makeLink($sender,$sender,"user").": ".$relayMessage."</font>";
        send_message_to_relay($msg);
	}
}

?>