<?php

class Towers {
	public static function get_tower_info($playfield_id, $site_number) {
		global $db;

		$sql = "
			SELECT
				*
			FROM
				tower_site t
			WHERE
				`playfield_id` = {$playfield_id}
				AND `site_number` = {$site_number}
			LIMIT 1";
		
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function find_sites_in_playfield($playfield_id) {
		global $db;

		$sql = "SELECT * FROM tower_site WHERE `playfield_id` = {$playfield_id}";

		$db->query($sql);
		return $db->fObject('all');
	}
	
	public static function get_closest_site($playfield_id, $x_coords, $y_coords) {
		global $db;

		$sql = "
			SELECT
				*,
				((x_distance * x_distance) + (y_distance * y_distance)) radius
			FROM
				(SELECT
					playfield_id,
					site_number,
					min_ql,
					max_ql,
					x_coord,
					y_coord,
					site_name,
					(x_coord - {$x_coords}) as x_distance,
					(y_coord - {$y_coords}) as y_distance
				FROM
					tower_site
				WHERE
					playfield_id = {$playfield_id}) t
			ORDER BY
				radius ASC
			LIMIT 1";

		$db->query($sql);
		return $db->fObject();		
	}

	public static function get_last_attack($att_faction, $att_org_name, $def_faction, $def_org_name, $playfield_id) {
		global $db;
		
		$att_org_name = str_replace("'", "''", $att_org_name);
		$def_org_name = str_replace("'", "''", $def_org_name);
		
		$time = time() - (7 * 3600);
		
		$sql = "
			SELECT
				*
			FROM
				tower_attack
			WHERE
				`att_org_name` = '{$att_org_name}'
				AND `att_faction` = '{$att_faction}'
				AND `def_org_name` = '{$def_org_name}'
				AND `def_faction` =  '{$def_faction}'
				AND `playfield_id` = {$playfield_id}
				AND `time` >= {$time}
			ORDER BY
				`time` DESC
			LIMIT 1";
		
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function record_attack($whois, $def_faction, $def_org_name, $x_coords, $y_coords, $closest_site) {
		global $db;
		
		$att_org_name = str_replace("'", "''", $whois->org);
		$def_org_name = str_replace("'", "''", $def_org_name);
		
		$sql = "
			INSERT INTO tower_attack (
				`time`,
				`att_org_name`,
				`att_faction`,
				`att_player`,
				`att_level`,
				`att_profession`,
				`def_org_name`,
				`def_faction`,
				`playfield_id`,
				`site_number`,
				`x_coords`,
				`y_coords`
			) VALUES (
				".time().",
				'{$att_org_name}',
				'{$whois->faction}',
				'{$whois->name}',
				'{$whois->level}',
				'{$whois->prof}',
				'{$def_org_name}',
				'{$def_faction}',
				{$closest_site->playfield_id},
				{$closest_site->site_number},
				{$x_coords},
				{$y_coords}
			)";
		
		return $db->exec($sql);
	}
	
	public static function find_all_scouted_sites() {
		global $db;
		
		$sql = 
			"SELECT
				*
			FROM
				scout_info s
				JOIN tower_site t
					ON (s.playfield_id = t.playfield_id AND s.site_number = t.site_number)
				JOIN playfields p
					ON (s.playfield_id = p.id)
			ORDER BY
				org_name, ct_ql";

		$db->query($sql);
		return $db->fObject('all');
	}
	
	public static function get_last_victory($playfield_id, $site_number) {
		global $db;
		
		$sql = "
			SELECT
				*
			FROM
				tower_victory v
				JOIN tower_attack a ON (v.attack_id = a.id)
			WHERE
				a.`playfield_id` = {$playfield_id}
				AND a.`site_number` >= {$site_number}
			ORDER BY
				v.`time` DESC
			LIMIT 1";
		
		$db->query($sql);
		return $db->fObject();
	}
	
	public static function record_victory($last_attack) {
		global $db;
		
		$win_org_name = str_replace("'", "''", $last_attack->att_org_name);
		$lose_org_name = str_replace("'", "''", $last_attack->def_org_name);
		
		if ($last_attack->site_number == '') {
			$last_attack->site_number = 'NULL';
		}
		
		$sql = "
			INSERT INTO tower_victory (
				`time`,
				`win_org_name`,
				`win_faction`,
				`lose_org_name`,
				`lose_faction`,
				`attack_id`
			) VALUES (
				".time().",
				'{$win_org_name}',
				'{$last_attack->att_faction}',
				'{$lose_org_name}',
				'{$last_attack->def_faction}',
				{$last_attack->id}
			)";
		
		return $db->exec($sql);
	}
	
	public static function scout_site($playfield_id, $site_number, $close_time, $ct_ql, $faction, $org_name, $scouted_by) {
		global $db;
		
		$org_name = str_replace("'", "''", $org_name);
		
		$sql = "
			INSERT INTO scout_info (
				`playfield_id`,
				`site_number`,
				`scouted_on`,
				`scouted_by`,
				`ct_ql`,
				`org_name`,
				`faction`,
				`close_time`
			) VALUES (
				{$playfield_id},
				{$site_number},
				NOW(),
				'{$scouted_by}',
				{$ct_ql},
				'{$org_name}',
				'{$faction}',
				{$close_time}
			)";

		$db->exec($sql);
	}
	
	public static function check_org_name($org_name) {
		global $db;
		
		$org_name = str_replace("'", "''", $org_name);
	
		$sql = "SELECT * FROM tower_attack WHERE `att_org_name` LIKE '{$org_name}' OR `def_org_name` LIKE '{$org_name}' LIMIT 1";
		
		$db->query($sql);
		if ($db->numrows() === 0) {
			return false;
		} else {
			return true;
		}
	}
}

?>