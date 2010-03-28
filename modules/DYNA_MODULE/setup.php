<?php

$filearray = file("./modules/DYNA_MODULE/dynadb.sql");
echo "Creating Dynacamp Database. \nDepending on the Database you are using this process can take a few mins.\n";
$db->beginTransaction();
foreach($filearray as $num => $line)
	$db->query(rtrim($line));
$db->Commit();
echo "Done.\n";
?>