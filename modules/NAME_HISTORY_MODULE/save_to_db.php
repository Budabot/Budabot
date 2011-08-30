<?php

if (isset($chatBot->data['name_history_cache']) && count($chatBot->data['name_history_cache']) > 0 && !$db->in_transaction()) {
	$db->begin_transaction();
	forEach ($chatBot->data['name_history_cache'] as $entry) {
		list($charid, $name) = $entry;
		$db->query("SELECT * FROM name_history WHERE name = '{$name}' AND charid = '{$charid}'");
		$data = $db->fObject('all');
		if (count($data) == 0) {
			$db->exec("INSERT INTO name_history (name, charid, dt) VALUES ('{$name}', '{$charid}', " . time() . ")");
		}
	}
	$db->commit();
	
	unset($chatBot->data['name_history_cache']);
}

?>