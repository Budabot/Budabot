<?php

$db->query("CREATE TABLE IF NOT EXISTS whereis (`whereisid` int(10),`name` varchar(255),`answer` text,`keywords` text)");
$db->query("SELECT * FROM whereis");
$filearray = file("./modules/WHEREIS_MODULE/whereis.sql");
if ($db->numrows() != count($filearray)) {
	$db->query("DELETE FROM whereis");
	echo "Creating Whereis Database. \nDepending on the Database you are using this process can take a few minutes.\n";
	$items = count($filearray);
	echo "$items Items needs to be added....";
	$db->beginTransaction();
	foreach($filearray as $num => $line)
		$db->query(rtrim($line));
	$db->Commit();
	echo "Done.\n";
}
?>