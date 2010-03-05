<?php
$db->query("CREATE TABLE IF NOT EXISTS boss_namedb (`bossid` int(10),`bossname` varchar(50),`location` varchar(50), 'keyname' varchar(200)Default NULL)");

$db->query("SELECT * FROM boss_namedb");
$filearray = file("./sql/boss_namedb.sql");
if ($db->numrows() != count($filearray)) {
	$db->query("DELETE FROM boss_namedb");
	echo "Creating Boss Name Database. \nDepending on the Database you are using this process can take a few minutes.\n";
	$items = count($filearray);
	echo "$items Items needs to be added....";
	$db->beginTransaction();
	foreach($filearray as $num => $line)
		$db->query(rtrim($line));
	$db->Commit();
	echo "Done.\n";
}
$db->query("CREATE TABLE IF NOT EXISTS boss_lootdb (`bossid` int(10),`itemid` int(10), 'itemname' varchar (100))");

$db->query("SELECT * FROM boss_lootdb");
$filearray = file("./sql/boss_lootdb.sql");
if ($db->numrows() != count($filearray)) {
	$db->query("DELETE FROM boss_lootdb");
	echo "Creating Boss Loot Database. \nDepending on the Database you are using this process can take a few minutes.\n";
	$items = count($filearray);
	echo "$items Items needs to be added....";
	$db->beginTransaction();
	foreach($filearray as $num => $line)
		$db->query(rtrim($line));
	$db->Commit();
	echo "Done.\n";
}

?>