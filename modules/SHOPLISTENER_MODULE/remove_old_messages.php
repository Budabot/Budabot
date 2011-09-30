<?php

$dt = time() - (86400 * Setting::get('shop_message_age'));

$db->begin_transaction();

$sql = "DELETE FROM shopping_messages WHERE dt < {$dt}";
$db->exec($sql);

$sql = "DELETE FROM shopping_items WHERE message_id NOT IN (SELECT id FROM shopping_messages)";
$db->exec($sql);

$db->commit();

?>