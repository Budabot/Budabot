<?php

class Towers {

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @Command("towerstats")
	 * @AccessLevel("all")
	 * @Description("Show how many towers each faction has lost")
	 */
	public function towerStatsCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^towerstats$/i", $message)) {
			$budatime = "1d";
		} else if (preg_match("/^towerstats (.+)$/i", $message, $arr)) {
			$budatime = $arr[1];
		}
		
		$time = $this->util->parseTime($budatime);
		if ($time < 1) {
			$msg = "You must enter a valid time parameter.";
			$this->chatBot->send($msg, $sendto);
			return;
		}
		
		$timeString = $this->util->unixtime_to_readable($time);
		
		$blob = "<header> :::::: Tower Stats for the Last $timeString :::::: <end>\n\n";
		
		$sql = "SELECT
				att_faction,
				COUNT(att_faction) AS num
			FROM
				tower_attack_<myname>
			WHERE
				`time` >= ?
			GROUP BY
				att_faction
			ORDER BY
				num DESC";

		$data = $this->db->query($sql, time() - $time);
		forEach ($data as $row) {
			$blob .= "<{$row->att_faction}>{$row->att_faction}<end> have attacked <highlight>{$row->num}<end> times.\n";
		}
		if (count($data) > 0) {
			$blob .= "\n";
		}
		
		$sql = "SELECT
				lose_faction,
				COUNT(lose_faction) AS num
			FROM
				tower_victory_<myname>
			WHERE
				`time` >= ?
			GROUP BY
				lose_faction
			ORDER BY
				num DESC";

		$data = $this->db->query($sql, time() - $time);
		forEach ($data as $row) {
			$blob .= "<{$row->lose_faction}>{$row->lose_faction}<end> have lost <highlight>{$row->num}<end> tower sites.\n";
		}
		
		$msg = $this->text->make_blob("Tower Stats for the Last $timeString", $blob);
		$this->chatBot->send($msg, $sendto);
	}

	public function get_tower_info($playfield_id, $site_number) {
		$sql = "
			SELECT
				*
			FROM
				tower_site t
			WHERE
				`playfield_id` = ?
				AND `site_number` = ?
			LIMIT 1";
		
		return $this->db->queryRow($sql, $playfield_id, $site_number);
	}
	
	public function find_sites_in_playfield($playfield_id) {
		$sql = "SELECT * FROM tower_site WHERE `playfield_id` = ?";

		return $this->db->query($sql, $playfield_id);
	}
	
	public function get_closest_site($playfield_id, $x_coords, $y_coords) {
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
					playfield_id = ?) t
			ORDER BY
				radius ASC
			LIMIT 1";

		return $this->db->queryRow($sql, $playfield_id);
	}

	public function get_last_attack($att_faction, $att_guild_name, $def_faction, $def_guild_name, $playfield_id) {
		$time = time() - (7 * 3600);
		
		$sql = "
			SELECT
				*
			FROM
				tower_attack_<myname>
			WHERE
				`att_guild_name` = ?
				AND `att_faction` = ?
				AND `def_guild_name` = ?
				AND `def_faction` = ?
				AND `playfield_id` = ?
				AND `time` >= ?
			ORDER BY
				`time` DESC
			LIMIT 1";
		
		return $this->db->queryRow($sql, $att_guild_name, $att_faction, $def_guild_name, $def_faction, $playfield_id, $time);
	}
	
	public function record_attack($whois, $def_faction, $def_guild_name, $x_coords, $y_coords, $closest_site) {
		$sql = "
			INSERT INTO tower_attack_<myname> (
				`time`,
				`att_guild_name`,
				`att_faction`,
				`att_player`,
				`att_level`,
				`att_ai_level`,
				`att_profession`,
				`def_guild_name`,
				`def_faction`,
				`playfield_id`,
				`site_number`,
				`x_coords`,
				`y_coords`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";
		
		return $this->db->exec($sql, time(), $whois->guild, $whois->faction, $whois->name, $whois->level, $whois->ai_level, $whois->profession,
			$def_guild_name, $def_faction, $closest_site->playfield_id, $closest_site->site_number, $x_coords, $y_coords);
	}
	
	public function find_all_scouted_sites() {
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
				guild_name, ct_ql";

		return $this->db->query($sql);
	}
	
	public function get_last_victory($playfield_id, $site_number) {
		$sql = "
			SELECT
				*
			FROM
				tower_victory_<myname> v
				JOIN tower_attack_<myname> a ON (v.attack_id = a.id)
			WHERE
				a.`playfield_id` = ?
				AND a.`site_number` >= ?
			ORDER BY
				v.`time` DESC
			LIMIT 1";
		
		return $this->db->queryRow($sql, $playfield_id, $site_number);
	}
	
	public function record_victory($last_attack) {
		$sql = "
			INSERT INTO tower_victory_<myname> (
				`time`,
				`win_guild_name`,
				`win_faction`,
				`lose_guild_name`,
				`lose_faction`,
				`attack_id`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?
			)";
		
		return $this->db->exec($sql, time(), $last_attack->att_guild_name, $last_attack->att_faction, $last_attack->def_guild_name, $last_attack->def_faction, $last_attack->id);
	}
	
	public function add_scout_site($playfield_id, $site_number, $close_time, $ct_ql, $faction, $guild_name, $scouted_by) {
		$this->db->begin_transaction();
		
		$this->db->exec("DELETE FROM scout_info WHERE `playfield_id` = ? AND `site_number` = ?", $playfield_id, $site_number);
		
		$sql = "
			INSERT INTO scout_info (
				`playfield_id`,
				`site_number`,
				`scouted_on`,
				`scouted_by`,
				`ct_ql`,
				`guild_name`,
				`faction`,
				`close_time`
			) VALUES (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)";

		$numrows = $this->db->exec($sql, $playfield_id, $site_number, time(), $scouted_by, $ct_ql, $guild_name, $faction, $close_time);
		
		if ($numrows == 0) {
			$this->db->rollback();
		} else {
			$this->db->commit();
		}
		
		return $numrows;
	}
	
	public function rem_scout_site($playfield_id, $site_number) {
		$sql = "DELETE FROM scout_info WHERE `playfield_id` = ? AND `site_number` = ?";

		return $this->db->exec($sql, $playfield_id, $site_number);
	}
	
	public function check_guild_name($guild_name) {
		$sql = "SELECT * FROM tower_attack_<myname> WHERE `att_guild_name` LIKE ? OR `def_guild_name` LIKE ? LIMIT 1";
		
		$data = $this->db->query($sql, $guild_name, $guild_name);
		if (count($data) === 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function getSitesInPenalty($time) {
		$sql = "
			SELECT att_guild_name, att_faction, MAX(IFNULL(t2.time, t1.time)) AS penalty_time
			FROM tower_attack_<myname> t1
				LEFT JOIN tower_victory_<myname> t2 ON t1.id = t2.id
			WHERE (t2.time IS NULL AND t1.time > ?) OR t2.time > ?
			GROUP BY att_guild_name, att_faction
			ORDER BY att_faction ASC, penalty_time DESC";
		return $this->db->query($sql, $time, $time);
	}
	
	
}

?>