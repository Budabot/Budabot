<?php

if ($type == "leavepriv") {
	$onlineController = Registry::getInstance('onlineController');
	$onlineController->removePlayerFromOnlineList($sender, 'priv');
}

?>