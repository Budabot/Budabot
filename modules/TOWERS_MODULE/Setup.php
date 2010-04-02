<?
$db->query("CREATE TABLE IF NOT EXISTS tower_attack_<myname> (`time` int, `att_guild` VARCHAR(50), `att_side` VARCHAR(10), `att_player` VARCHAR(20), `att_level` int, `att_profession` VARCHAR(15), `def_guild` VARCHAR(50), `def_side` VARCHAR(10), `zone` VARCHAR(50), `x` INT, `y` INT)");

$db->query("CREATE TABLE IF NOT EXISTS tower_result_<myname> (`time` int, `win_guild` VARCHAR(50), `win_side` VARCHAR(10), `lose_guild` VARCHAR(50), `lose_side` VARCHAR(10))");

$db->query("CREATE TABLE IF NOT EXISTS towerranges (`id` int(11), `level` varchar(10) default NULL, `playfield` varchar(250) default NULL, `hugemaploc` varchar(10) default NULL, `coordx` varchar(10) 	default NULL, `coordy` varchar(10) default NULL, `location` varchar(250) default NULL)");
		  
$db->query("SELECT * FROM towerranges");
$filearray = file("./sql/towerranges.sql");
if($db->numrows() != count($filearray)) 	{
  	$db->query("DELETE FROM towerranges");
	echo "Creating Tower Database. \nDepending on the Database you are using this process can take a few mins.\n";	
	$items = count($filearray);
	echo "$items Items needs to be added....";
	$db->beginTransaction();
	foreach($filearray as $num => $line)
		$db->query(rtrim($line));
    $db->Commit();			
	echo "Done.\n";
}		  
?>