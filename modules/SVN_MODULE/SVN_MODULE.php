<?php
	$MODULE_NAME = "SVN_MODULE";

	Command::register($MODULE_NAME, "", "svn.php", "svn", "admin", "Updates your bot from svn repository");
	
	Setting::add($MODULE_NAME, "svnconflict", "How to handle conflicts", "edit", "theirs-conflict", "theirs-conflict;mine-conflict;theirs-full;mine-full;postpone", '0', "admin", "");
?>