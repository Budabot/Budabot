<?php
$db->query("CREATE TABLE IF NOT EXISTS priv_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `faction` CHAR(10), `profession` CHAR(20), `guild` CHAR(255), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0', `guest` INT DEFAULT '0')");

// Create a list of members that were in the chatbot before the restart/crash
if(!isset($this->vars["members_before_restart"])) {
	$db->query("SELECT * FROM priv_chatlist_<myname>");
	if($db->numrows() != 0) {
		while($row = $db->fObject())
			$this->vars["members_before_restart"][$row->name] = true;
	} else
		$this->vars["members_before_restart"] = "";
}

// Clear Chatlist
$db->query("DELETE FROM priv_chatlist_<myname>");
$this->vars["topicdelay"] = time() + $this->settings["CronDelay"] + 60;
?>
