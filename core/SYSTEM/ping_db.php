<?php
echo "Pinging database...\n";
$sql = "SELECT * FROM settings_<myname>";
$db->query($sql);
?>