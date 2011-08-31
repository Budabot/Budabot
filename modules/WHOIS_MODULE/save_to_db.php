<?php

if (isset($chatBot->data['name_history_cache']) && count($chatBot->data['name_history_cache']) > 0 && !$db->in_transaction()) {
	$db->begin_transaction();
	forEach ($chatBot->data['name_history_cache'] as $entry) {
		list($charid, $name) = $entry;
		if ($db->get_type() == "Sqlite") {
			$db->exec("INSERT OR IGNORE INTO name_history (name, charid, dt) VALUES ('{$name}', '{$charid}', " . time() . ")");
		} else { // if ($db->get_type() == "Mysql")
			$db->exec("INSERT IGNORE INTO name_history (name, charid, dt) VALUES ('{$name}', '{$charid}', " . time() . ")");
		}
	}
	$db->commit();
	
	unset($chatBot->data['name_history_cache']);
}

?>