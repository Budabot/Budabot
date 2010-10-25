<?php

class Player {
	private $xml;
	
	public static function create($input) {
		global $chatBot;

		if (is_int($input)) {
			// if input is the uid
			$uid = (int)$input;

			return new Player::get_instance_by_uid($uid);
		} else {
			// if input is the player name
			$name = ucfirst(strtolower($input));
			$uid = $chatBot->get_uid($input);
			if ($uid === false) {
				return null;
			}

			return new Player::get_instance_by_name($uid, $name);
		}
	}
	
	private static function get_instance_by_uid($uid) {
		global $db;

		$sql = "SELECT * FROM players WHERE `uid` = {$uid}";
		$xml = $db->query($sql, true);
		if ($xml === null) {
			// TODO log "Could not find player with uid: '$uid'"
			return null;
		} else {
			return new Player($xml);
		}
	}
	
	private static function get_instance_by_name($name, $dimension = null) {
		global $db;
		global $vars;
		
		if ($dimension === null) {
			$dimension = $vars['dimension'];
		}
	
		$sql = "SELECT * FROM players WHERE `name` = {$name} AND dimension = {$dimension}";
		$xml = $db->query($sql, true);
		if ($xml === null || $xml->last_update < (time() - 86400)) {
			$xml = Player::lookup($name, $vars['dimension']);
			if ($xml === null) {
				$xml = new stdObject;
				$xml->source = 'none';
			}
			$xml->uid = $uid;
			$xml->dimension = $vars["dimension"];
			$xml->last_update = time();

			Player::update($xml);
		}
		
		return new Player($xml);
	}
	
	public static function lookup($name, $rk_num) {
		$xml = Player::lookup_url("http://people.anarchy-online.com/character/bio/d/$rk_num/name/$name/bio.xml");
		if ($xml !== null) {
			$xml->source = 'people.anarchy-online.com';
			Player::update($xml);

			return $xml;
		}
		
		// if people.anarchy-online.com was too slow to respond or returned invalid data then try to update from auno.org
		$xml = Player::lookup_url("http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name");
		if ($xml !== null) {
			$xml->source = 'auno.org';
			Player::update($xml);
			
			return $xml;
		}
		
		return null;
	}
	
	public static function lookup_url($url) {
		$playerbio = xml::getUrl($url);
		if (xml::spliceData($playerbio, '<nick>', '</nick>') == $name) {
			$xml = new stdObject;
		
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
		
		return null;
	}
	
	public static function update_name($uid, $name) {
		global $db;
		global $vars;
		
		$sql = "SELECT * FROM players WHERE `uid` = {$uid}";
		$row = $db->query($sql, true);
		if ($row === null) {
			$sql = "INSERT INTO players (`uid`, `name`, dimension, `last_update`) VALUES ({$uid}, '{$name}', {$vars['dimension']}, " . time() . ")";
			$db->exec($sql);
		} else {
			$sql = "UPDATE players SET `name` = '{$name}', `last_update` = " . time() . " WHERE `uid` = {$uid}";
			$db->exec($sql);
		}
	}
	
	public static function update(&$xml) {
	
	}
	
	private function __construct($xml) {
		$this->xml = $xml;
	}
	
	// gives property-like syntax for xml values
	public function __get($name) {
		return $this->xml->$name();
	}
	
	public function get_access_level() {
		//return AccessLevel::get_user_access_level($this);
	}
	
	public function get_is_online() {
		//$buddy = Buddylist::get($this->uid);
		//return ($buddy === null ? null : $buddy['online']);
	}
	
	public function add_to_buddylist($type) {
		//return Buddylist::add($this->uid, $type);
	}
	
	public function remove_from_buddylist($type) {
		//return Buddylist::remove($this->uid, $type);
	}
	
	public function get_is_org_member() {
		// TODO
	}
	
	public function get_is_member() {
		// TODO
	}
	
	public function get_is_admin() {
		// TODO
	}
}

?>