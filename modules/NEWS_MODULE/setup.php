<?php
$db->query("CREATE TABLE IF NOT EXISTS news (`id` INTEGER PRIMARY KEY AUTO_INCREMENT, `time` INT NOT NULL, `name` VARCHAR(30), `news` TEXT)");
?>
