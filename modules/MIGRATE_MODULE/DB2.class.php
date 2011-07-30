<?php
	
class DB2 extends DB {
	function __construct($type, $dbName, $host, $user, $pass, $botname) {
		parent::__construct($type, $dbName, $host, $user, $pass);
		$this->botname = strtolower($botname);
	}
	
	function formatSql($sql) {
		$sql = str_replace("<myname>", $this->botname, $sql);
		$sql = parent::formatSql($sql);

		return $sql;
	}
}

?>
