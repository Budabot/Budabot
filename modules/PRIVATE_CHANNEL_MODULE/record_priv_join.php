<?php

if ($type == "joinpriv") {
	$onlineController = Registry::getInstance('onlineController');
	$onlineController->addPlayerToOnlineList($sender, $chatBot->vars['guild'] . ' Guests', 'priv');
}

?>