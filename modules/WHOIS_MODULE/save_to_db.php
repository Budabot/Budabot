<?php

if (isset($chatBot->data['name_history_cache']) && count($chatBot->data['name_history_cache']) > 0 && !$db->in_transaction()) {
	$db->begin_transaction();
	forEach ($chatBot->data['name_history_cache'] as $entry) {
		list($charid, $name) = $entry;
		if ($db->get_type() == "sqlite") {
			$db->exec("INSERT OR IGNORE INTO name_history (name, charid, dimension, dt) VALUES (?, ?, <dim>, ?)", $name, $charid, time());
		} else { // if ($db->get_type() == "mysql")
			$db->exec("INSERT IGNORE INTO name_history (name, charid, dimension, dt) VALUES (?, ?, <dim>, ?)", $name, $charid, time());
		}
	}
	$db->commit();
	
	unset($chatBot->data['name_history_cache']);
}

?>