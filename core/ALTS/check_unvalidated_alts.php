<?php

if ($chatBot->is_ready() && $setting->get('alts_inherit_admin') == 1) {
	$altInfo = Alts::get_alt_info($sender);
	
	if ($altInfo->hasUnvalidatedAlts() && ($sender == $altInfo->main || ($setting->get('validate_from_validated_alt') == 1 && $altInfo->is_validated($sender)))) {
		$msg = "You have unvalidated alts. Please validate them.";
		$chatBot->sendTell($msg, $sender);
		$chatBot->sendTell($altInfo->get_alts_blob(true), $sender);
	}
}

?>