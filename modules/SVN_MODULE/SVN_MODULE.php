<?php
	$command->register($MODULE_NAME, "", "svn.php", "svn", "admin", "Updates your bot from svn repository");

	$setting->add($MODULE_NAME, "svnconflict", "How to handle conflicts", "edit", "options", "theirs-conflict", "theirs-conflict;mine-conflict;theirs-full;mine-full;postpone", '', "admin", "");
	$setting->add($MODULE_NAME, "svnpath", "Path to svn binary", "edit", "text", "svn", "svn;/usr/bin/svn");
?>
