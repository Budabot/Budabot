<?php
	function interpolate($x1, $x2, $y1, $y2, $x) {
		$result = ($y2 - $y1)/($x2 - $x1) * ($x - $x1) + $y1;
		$result = round($result,0);
		return $result;
	}
	
	function timestamp($sec_value) {
		$stamp = "";
		if ($sec_value > 3599) {
			$hours = floor($sec_value/3600);
			$sec_value = $sec_value - $hours*3600;
			$stamp .= "<orange>".$hours."<end> hour(s) ";
		}
		if ($sec_value > 59) {
			$minutes = floor($sec_value/60);
			$sec_value = $sec_value - $minutes*60;
			$stamp .= "<orange>".$minutes."<end> minute(s) ";
		}
		if (!($sec_value == 0))
			$stamp .= "<orange>".$sec_value."<end> second(s)";
		return $stamp;
	}
	
	function cap_full_auto($attack_time, $recharge_time, $full_auto_recharge) {
		$FACap = floor(10 + $attack_time);
		$FA_Skill_Cap = ((40 * $recharge_time) + ($full_auto_recharge / 100) - 11) * 25;

		return array($FACap, $FA_Skill_Cap);
	}
	
	function cap_burst($attack_time, $recharge_time, $burst_recharge) {
		$cap = round($attack_time + 8,0);
		$burstskillcap = floor((($recharge_time * 20) + ($burst_recharge / 100) - 8) * 25);
		
		return array($cap, $burstskillcap);
	}
	
	function cap_fling_shot($attack_time) {
		$flinghardcap = 4 + $attack_time;
		$flingskillcap = (($attack_time * 16) - $flinghardcap) * 100;

		return array($flinghardcap, $flingskillcap);
	}
	
	function cap_fast_attack($attack_time) {
		$fasthardcap = 4 + $attack_time;
		$fastskillcap = (($attack_time * 16) - $fasthardcap) * 100;

		return array($fasthardcap, $fastskillcap);
	}

	function cap_aimed_shot($attack_time, $recharge_time) {
		$cap = floor($attack_time + 10);
		$ASCap = ceil(((4000 * $recharge_time) - 1000) / 3);

		return array($cap, $ASCap);
	}
?>