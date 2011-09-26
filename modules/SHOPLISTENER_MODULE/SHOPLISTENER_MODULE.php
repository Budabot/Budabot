<?php
	$MODULE_NAME = "SHOPLISTENER_MODULE";

	Event::register($MODULE_NAME, "allpackets", "capture.php", "none", "Capture messages from shopping channel");
?>