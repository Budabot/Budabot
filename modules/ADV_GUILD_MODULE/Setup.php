<?
$db->query("CREATE TABLE IF NOT EXISTS org_city_<myname> (`time` INT, `action` VARCHAR(10), `player` VARCHAR(25))");
$db->query("CREATE TABLE IF NOT EXISTS news_<myname> (`id` INT PRIMARY KEY AUTO_INCREMENT, `time` INT NOT NULL, `name` VARCHAR(30), `news` TEXT)");
$this->vars["newsdelay"] = time() + $this->settings["CronDelay"] + 60;
?>
