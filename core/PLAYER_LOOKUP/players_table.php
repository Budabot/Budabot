<?php

if ($db->get_type() == 'Mysql') {
	$sql = "CREATE TABLE IF NOT EXISTS players ( `charid` INT NOT NULL, `firstname` varchar(20) NOT NULL  DEFAULT '', `name` varchar(20) NOT NULL, `lastname` varchar(20) NOT NULL DEFAULT '', `level` smallint DEFAULT NULL, `breed` varchar(20) NOT NULL DEFAULT '', `gender` varchar(20) NOT NULL DEFAULT '', `faction` varchar(20) NOT NULL DEFAULT '', `profession` varchar(20) NOT NULL DEFAULT '', `prof_title` varchar(20) NOT NULL DEFAULT '', `ai_rank` varchar(20) NOT NULL DEFAULT '', `ai_level` smallint DEFAULT NULL, `guild_id` int DEFAULT NULL, `guild` varchar(255) NOT NULL DEFAULT '', `guild_rank`  varchar(20) NOT NULL DEFAULT '', `guild_rank_id` smallint DEFAULT NULL, `dimension` smallint, `source`  varchar(50) NOT NULL DEFAULT '', `last_update` INT, INDEX players_name (name))";
	$db->exec($sql);
} else if ($db->get_type() == 'Sqlite') {
	$sql = "CREATE TABLE IF NOT EXISTS players ( `charid` INT NOT NULL, `firstname` varchar(20) NOT NULL  DEFAULT '', `name` varchar(20) NOT NULL, `lastname` varchar(20) NOT NULL DEFAULT '', `level` smallint DEFAULT NULL, `breed` varchar(20) NOT NULL DEFAULT '', `gender` varchar(20) NOT NULL DEFAULT '', `faction` varchar(20) NOT NULL DEFAULT '', `profession` varchar(20) NOT NULL DEFAULT '', `prof_title` varchar(20) NOT NULL DEFAULT '', `ai_rank` varchar(20) NOT NULL DEFAULT '', `ai_level` smallint DEFAULT NULL, `guild_id` int DEFAULT NULL, `guild` varchar(255) NOT NULL DEFAULT '', `guild_rank`  varchar(20) NOT NULL DEFAULT '', `guild_rank_id` smallint DEFAULT NULL, `dimension` smallint, `source`  varchar(50) NOT NULL DEFAULT '', `last_update` INT)";
	$db->exec($sql);
	$sql = "CREATE INDEX IF NOT EXISTS players_name ON players(name)";
	$db->exec($sql);
}

?>