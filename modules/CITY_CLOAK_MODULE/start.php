<?php
if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $message, $arr)) {
	// delete previous entries
	$db->query("DELETE FROM wave_counter_<myname>");
	$this->send("Wave counter started.", "guild");
	$db->query("INSERT INTO `wave_counter_<myname>` (`time`, `wave`) VALUES (".time().", 1)");
} else if (preg_match("/^startraid/i", $message)) {
	$db->query("SELECT * FROM wave_counter_<myname>");
	if ($db->numrows() != 0) {
		$this->send("A raid is already in progress.", "guild");
	} else {
		$this->send("Wave counter started.", "guild");
		$db->query("INSERT INTO `wave_counter_<myname>` (`time`, `wave`) VALUES (".time().", 1)");
	}
}
?>