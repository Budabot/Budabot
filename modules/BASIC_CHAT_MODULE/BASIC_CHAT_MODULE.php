<?php
	require_once 'ChatRallyController.class.php';
	require_once 'ChatSayController.class.php';
	require_once 'ChatAssistController.class.php';
	require_once 'ChatTopicController.class.php';
	require_once 'ChatLeaderController.class.php';
	require_once 'ChatCheckController.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'ChatRallyController', new ChatRallyController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatSayController', new ChatSayController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatAssistController', new ChatAssistController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatTopicController', new ChatTopicController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatLeaderController', new ChatLeaderController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatCheckController', new ChatCheckController);
?>
