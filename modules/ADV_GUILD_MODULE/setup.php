<?php
$db->query("CREATE TABLE IF NOT EXISTS news_<myname> (`id` INTEGER PRIMARY KEY AUTO_INCREMENT, `time` INT NOT NULL, `name` VARCHAR(30), `news` TEXT)");
$this->vars["newsdelay"] = time() + $this->settings["CronDelay"] + 60;
?>
