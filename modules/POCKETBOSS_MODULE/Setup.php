<?
$db->query("CREATE TABLE IF NOT EXISTS pbdb (`id` INT, `pb` VARCHAR(30), `pb_location` VARCHAR(50), `bp_mob` VARCHAR(50), `bp_lvl` INT, `bp_location` VARCHAR(50), `type` VARCHAR(25), `slot` VARCHAR(25), `line` VARCHAR(25), `ql` VARCHAR(5), `itemid` INT)");

$db->query("SELECT * FROM pbdb");
$filearray = file("./sql/pbdb.sql");
if($db->numrows() != count($filearray)) {
  	$db->query("DELETE FROM pbdb");
	echo "Creating Pocketboss Database. \nDepending on the Database you are using this process can take a few mins.\n";	
	$items = count($filearray);
	echo "$items Items needs to be added....";
	$db->beginTransaction();
	foreach($filearray as $num => $line)
		$db->query(rtrim($line));
    $db->Commit();			
	echo "Done.\n";
}
?>