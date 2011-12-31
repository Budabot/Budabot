<?php
	require_once 'Worldnet.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'Worldnet', new Worldnet);
	$chatBot->getInstance('Worldnet')->init($MODULE_NAME);
?>