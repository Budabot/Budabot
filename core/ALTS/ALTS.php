<?php
	require_once 'Alts.class.php';
	
	$db->loadSQLFile($MODULE_NAME, "alts");
	
	$event->register($MODULE_NAME, "logOn", "check_unvalidated_alts.php", "Reminds players logging in to validate alts");
	
	$command->register($MODULE_NAME, "", "altvalidate.php", "altvalidate", "member", "Validate alts for admin privileges", "altvalidate.txt", 1);
	$command->register($MODULE_NAME, "", "altsadmin.php", "altsadmin", "mod", "Alt character handling (admin)", "altsadmin.txt", 1);
	$command->register($MODULE_NAME, "", "altscmd.php", "alts", "member", "Alt character handling", "alts.txt", 1);

	$subcommand->register($MODULE_NAME, "", "alts_main.php", "alts main (.+)", "member", "alts", "Add yourself as an alt to a main", '', 1);

	$setting->add($MODULE_NAME, 'alts_inherit_admin', 'Alts inherit admin privileges from main', 'edit', "options", 0, "true;false", "1;0", 'mod', "alts_inherit_admin.txt");
	$setting->add($MODULE_NAME, "validate_from_validated_alt", "Validate alts from any validated alt", "edit", "options", "1", "true;false", "1;0");
?>