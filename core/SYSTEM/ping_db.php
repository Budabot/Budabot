<?php
// if the bot doesn't query the database for 8 hours the db connection is closed
Logger::log('DEBUG', 'CORE', "Pinging database");
$sql = "SELECT * FROM settings_<myname>";
$db->query($sql);
?>