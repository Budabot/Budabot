<?php

class Towers {
	public static function get_site_info($zone, $site_number) {
		global $db;
		
		$sql = "SELECT * FROM towerranges WHERE `playfield` = '$zone' AND `hugemaploc` = $site_number";
		$db->query($sql);
		return $db->fOject();
	}
	
	public static function find_sites_in_zone($zone) {
		global $db;
		
		$sql = "SELECT * FROM towerranges WHERE `playfield` = '$zone'";
		$db->query($sql);
		return $db->fOject('all');
	}
	
	public static function get_closest_site($zone, $x_coords, $y_coords) {
		global $db;
		
		$sql = "
			SELECT
				*,
				(ABS(coordx - $x_coords) + ABS(coordy - $y_coords)) proximity
			FROM
				towerranges
			WHERE
				`playfield` LIKE '$zone'
			ORDER BY
				proximity ASC
			LIMIT 1";
		$db->query($sql);
		return $db->fObject();		
	}
}

?>