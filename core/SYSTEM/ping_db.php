<?php

// if the bot doesn't query the mysql database for 8 hours the db connection is closed
LegacyLogger::log('DEBUG', 'CORE', "Pinging database");
$sql = "SELECT * FROM settings_<myname>";
$db->query($sql);

?>