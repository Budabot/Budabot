<?php
	require_once 'ChatRallyController.class.php';
	require_once 'chatsay.class.php';
	require_once 'ChatAssistController.class.php';
	require_once 'ChatTopicController.class.php';
	require_once 'ChatLeaderController.class.php';

	$chatBot->registerInstance($MODULE_NAME, 'ChatRallyController', new ChatRallyController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatSay', new ChatSay);
	$chatBot->registerInstance($MODULE_NAME, 'ChatAssistController', new ChatAssistController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatTopicController', new ChatTopicController);
	$chatBot->registerInstance($MODULE_NAME, 'ChatLeaderController', new ChatLeaderController);

	// Check macros
	$command->register($MODULE_NAME, "", "check.php", "check", "all", "Checks who of the raidgroup is in the area", "check.txt");

	// Tell
	$command->register($MODULE_NAME, "", "tell.php", "tell", "rl", "Repeats a message 3 times", "tell.txt");
	$command->register($MODULE_NAME, "", "cmd.php", "cmd", "rl", "Creates a highly visible messaage", "cmd.txt");
?>
