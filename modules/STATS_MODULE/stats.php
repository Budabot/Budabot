<?php
   /*
   ** Module: STATS
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Shows links that you can click on to see your stats for certain, unseen skills
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 7-Feb-2010
   ** Date(last modified): 9-Mar-2010
   **
   ** Copyright (C) 2009 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos:
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

if (eregi("^stats$", $message, $arr)) {

	$window = '';
	if (method_exists('bot', 'makeHeader')) {
		$window = bot::makeHeader("Stats", "none");
	} else {
		$window = "<header>::::: Stats :::::<end>\n";	
	}
	
	$window .= "
		<font color=#FFF000>::: Offense / Defense :::</font>
		<a href=skillid://276>Offense (Addall-Off)</a>
		<a href=skillid://277>Defense (Addall-Def)</a>
		<a href=skillid://51>Aggdef-Slider</a>
		<a href=skillid://4>Attack Speed</a>
		
		<font color=#FFF000>::: Critical Strike :::</font>
		<a href=skillid://379>Crit increase</a>
		<a href=skillid://391>Crit decrease</a>
		
		<font color=#FFF000>::: Heal :::</font>
		<a href=skillid://342>Heal delta (interval)</a> <font color=#FFFFFF>(tick in secs)</font>
		<a href=skillid://343>Heal delta (amount)</a>
		<a href=skillid://535>Heal modifier</a>
		
		<font color=#FFF000>::: Nano :::</font>
		<a href=skillid://363>Nano delta (interval)</a> <font color=#FFFFFF>(tick in secs)</font>
		<a href=skillid://364>Nano delta (amount)</a>
		<a href=skillid://318>Nano execution cost</a>
		<a href=skillid://536>Nano modifier</a>
		<a href=skillid://383>Interrupt modifier</a>
		<a href=skillid://381>Range Increase Nanoformula</a>
		
		<font color=#FFF000>::: Add Damage (Amount) :::</font>
		<a href=skillid://279>+Damage - Melee</a>
		<a href=skillid://280>+Damage - Energy</a>
		<a href=skillid://281>+Damage - Chemical</a>
		<a href=skillid://282>+Damage - Radiation</a>
		<a href=skillid://278>+Damage - Projectile</a>
		<a href=skillid://311>+Damage - Cold</a>
		<a href=skillid://315>+Damage - Nano</a>
		<a href=skillid://316>+Damage - Fire</a>
		<a href=skillid://317>+Damage - Poison</a>
		
		<font color=#FFF000>::: Reflect Shield (Percentage) :::</font>
		<a href=skillid://205>ReflectProjectileAC</a>
		<a href=skillid://206>ReflectMeleeAC</a>
		<a href=skillid://207>ReflectEnergyAC</a>
		<a href=skillid://208>ReflectChemicalAC</a>
		<a href=skillid://216>ReflectRadiationAC</a>
		<a href=skillid://217>ReflectColdAC</a>
		<a href=skillid://218>ReflectNanoAC</a>
		<a href=skillid://219>ReflectFireAC</a>
		<a href=skillid://225>ReflectPoisonAC</a>
		
		<font color=#FFF000>::: Reflect Shield (Amount) :::</font>
		<a href=skillid://475>MaxReflectedProjectileDmg</a>
		<a href=skillid://476>MaxReflectedMeleeDmg</a>
		<a href=skillid://477>MaxReflectedEnergyDmg</a>
		<a href=skillid://278>MaxReflectedChemicalDmg</a>
		<a href=skillid://479>MaxReflectedRadiationDmg</a>
		<a href=skillid://480>MaxReflectedColdDmg</a>
		<a href=skillid://481>MaxReflectedNanoDmg</a>
		<a href=skillid://482>MaxReflectedFireDmg</a>
		<a href=skillid://483>MaxReflectedPoisonDmg</a>
		
		<font color=#FFF000>::: Damage Shield (Amount) :::</font>
		<a href=skillid://226>ShieldProjectileAC</a>
		<a href=skillid://227>ShieldMeleeAC</a>
		<a href=skillid://228>ShieldEnergyAC</a>
		<a href=skillid://229>ShieldChemicalAC</a>
		<a href=skillid://230>ShieldRadiationAC</a>
		<a href=skillid://231>ShieldColdAC</a>
		<a href=skillid://232>ShieldNanoAC</a>
		<a href=skillid://233>ShieldFireAC</a>
		<a href=skillid://234>ShieldPoisonAC</a>
		
		<font color=#FFF000>::: Damage Absorb (Amount) :::</font>
		<a href=skillid://238>AbsorbProjectileAC</a>
		<a href=skillid://239>AbsorbMeleeAC</a>
		<a href=skillid://240>AbsorbEnergyAC</a>
		<a href=skillid://241>AbsorbChemicalAC</a>
		<a href=skillid://242>AbsorbRadiationAC</a>
		<a href=skillid://243>AbsorbColdAC</a>
		<a href=skillid://244>AbsorbFireAC</a>
		<a href=skillid://245>AbsorbPoisonAC</a>
		<a href=skillid://246>AbsorbNanoAC</a>
		
		<font color=#FFF000>::: Misc :::</font>
		<a href=skillid://319>XP Bonus</a>
		<a href=skillid://382>SkillLockModifier</a>
		<a href=skillid://380>Weapon Range Increase</a>
		<a href=skillid://517>Special Attack Blockers</a>
		<a href=skillid://199>Reset Points</a>
		<a href=skillid://360>Scale</a>
		<a href=skillid://201>Char base aggro</a> (unknown)
		<a href=skillid://202>Char stability</a> (unknown)
		<a href=skillid://203>Char extroverty</a> (unknown)";

	$msg = bot::makeLink('Stats', $window, 'blob');

	if ($type == "msg")
	{
	    bot::send($msg, $sender);
	}
	else if ($type == "priv")
	{
	    bot::send($msg);
	}
	else if ($type == "guild")
	{
	    bot::send($msg, "guild");
	}
}

?>
