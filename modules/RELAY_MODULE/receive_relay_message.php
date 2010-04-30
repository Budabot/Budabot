<?php

if ($channel == $this->settings['externalrelaybot'])	{
	bot::send("[$sender] $message", "org", true);
}

?>