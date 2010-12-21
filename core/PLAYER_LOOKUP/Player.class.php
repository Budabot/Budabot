<?php

class Player {
	public static function get_by_name($name, $dimension = null) {
		global $db;
		global $vars;
		
		$name = ucfirst(strtolower($name));
		
		if ($dimension === null) {
			$dimension = $vars['dimension'];
		}
	
		$sql = "SELECT * FROM players WHERE name LIKE '$name'";
		$db->query($sql);
		$player = $db->fObject();

		if ($player === null) {
			$player = Player::lookup($name, $dimension);
		} else if ($player->last_update < (time() - 86400)) {
			$player2 = Player::lookup($name, $dimension);
			if ($player2 !== null) {
				$player = $player2;
			}
		}
		
		return $player;
	}
	
	private static function lookup($name, $dimension) {
		$xml = Player::lookup_url("http://people.anarchy-online.com/character/bio/d/$dimension/name/$name/bio.xml");
		if ($xml->name == $name) {
			$xml->source = 'people.anarchy-online.com';
			$xml->dimension = $dimension;

			Player::update($xml);

			return $xml;
		}
		
		// if people.anarchy-online.com was too slow to respond or returned invalid data then try to update from auno.org
		$xml = Player::lookup_url("http://auno.org/ao/char.php?output=xml&dimension=$dimension&name=$name");
		if ($xml->name == $name) {
			$xml->source = 'auno.org';
			$xml->dimension = $dimension;

			Player::update($xml);

			return $xml;
		}
		
		return null;
	}
	
	private static function lookup_url($url) {
		$playerbio = xml::getUrl($url);
		
		$xml = new stdClass;
	
		// parsing of the player data		
		$xml->firstname    = xml::spliceData($playerbio, '<firstname>', '</firstname>');
		$xml->name         = xml::spliceData($playerbio, '<nick>', '</nick>');
		$xml->lastname     = xml::spliceData($playerbio, '<lastname>', '</lastname>');
		$xml->level        = xml::spliceData($playerbio, '<level>', '</level>');
		$xml->breed        = xml::spliceData($playerbio, '<breed>', '</breed>');
		$xml->gender       = xml::spliceData($playerbio, '<gender>', '</gender>');
		$xml->faction      = xml::spliceData($playerbio, '<faction>', '</faction>');
		$xml->prof         = xml::spliceData($playerbio, '<profession>', '</profession>');
		$xml->prof_title   = xml::spliceData($playerbio, '<profession_title>', '</profession_title>');
		$xml->ai_rank      = xml::spliceData($playerbio, '<defender_rank>', '</defender_rank>');
		$xml->ai_level     = xml::spliceData($playerbio, '<defender_rank_id>', '</defender_rank_id>');
		$xml->org_id       = xml::spliceData($playerbio, '<organization_id>', '</organization_id>');
		$xml->org          = xml::spliceData($playerbio, '<organization_name>', '</organization_name>');
		$xml->rank         = xml::spliceData($playerbio, '<rank>', '</rank>');
		$xml->rank_id      = xml::spliceData($playerbio, '<rank_id>', '</rank_id>');
		
		return $xml;
	}
	
	private static function update(&$xml) {
		global $db;
		
		$db->beginTransaction();
	
		$sql = "DELETE FROM players WHERE `name` LIKE '$xml->name'";
		$db->exec($sql);
	
		$sql = "INSERT INTO players (
			firstname,
			name,
			lastname,
			level,
			breed,
			gender,
			faction,
			prof,
			prof_title,
			ai_rank,
			ai_level,
			org_id,
			org,
			org_rank,
			org_rank_id,
			dimension,
			source,
			last_update
		) VALUES (
			'{$xml->firstname}',
			'{$xml->name}',
			'{$xml->lastname}',
			'{$xml->level}',
			'{$xml->breed}',
			'{$xml->gender}',
			'{$xml->faction}',
			'{$xml->prof}',
			'{$xml->prof_title}',
			'{$xml->ai_rank}',
			'{$xml->ai_level}',
			'{$xml->org_id}',
			'{$xml->org}',
			'{$xml->rank}',
			'{$xml->rank_id}',
			'{$xml->dimension}',
			'{$xml->source}',
			'" . time() . "'
		)";
		
		$db->exec($sql);
		
		$db->Commit();
	}
}

?>