<?php
$infgarn_txt = "<header>::::: Inferno Garden Nanos  :::::<end>\n\n"; 
$infgarn_txt = "Inferno Garden
<font color='#69E61E'>
<a href='itemref://226717/226717/196'>LINK </a> Abolish Gallium - Trader
<a href='itemref://218075/218075/198'>LINK </a> Aegis of Metal - MA
<a href='itemref://227675/227675/185'>LINK </a> Assurance Advocacy - Engie
<a href='itemref://223182/223182/203'>LINK </a> Augmented Mirror Shield MK II - Soldier
<a href='itemref://223228/223228/197'>LINK </a> Battery Fire - Soldier
<a href='itemref://239757/239756/200'>LINK </a> Build Spirit Tech Source Waterfall Projector - Adv
<a href='itemref://227771/227771/185'>LINK </a> Burst Out - Agent
<a href='itemref://223282/223282/180'>LINK </a> Cellular Recuperation - Doctor
<a href='itemref://223204/223204/190'>LINK </a> Clear Victim - Soldier
<a href='itemref://220338/220338/190'>LINK </a> Composite Mochams (1 hour) - MP
<a href='itemref://252058/252058/208'>LINK </a> Cosmic Relaxation - Trader
<a href='itemref://227114/227114/181'>LINK </a> Corruption of Resolve - MP
<a href='itemref://227112/227112/180'>LINK </a> Corruption of Will - MP
<a href='itemref://226731/226731/191'>LINK </a> Debilitate Defense - Trader
<a href='itemref://224178/224178/219'>LINK </a> Degeneration of Celerity - Shade
<a href='itemref://226729/226729/191'>LINK </a> Disable Defense - Trader
<a href='itemref://218081/218081/191'>LINK </a> Dragon Stance - MA
<a href='itemref://222916/222916/194'>LINK </a> Element of Corrosion - Enforcer
<a href='itemref://223216/223216/195'>LINK </a> Elude Me - Soldier
<a href='itemref://224146/224146/193'>LINK </a> Empowered Introspective Engagement - Crat
<a href='itemref://233098/223098/185'>LINK </a> Empowered Reactive Harmonic Cocoon - Engie
<a href='itemref://233021/223021/192'>LINK </a> Empowered Reactive Reflective Field - Soldier
<a href='itemref://218137/218137/185'>LINK </a> Enfraam>s Ultimate Destroyer - NT
<a href='itemref://223320/223320/213'>LINK </a> Fieldsweeper Devastator Drone - Engie
<a href='itemref://252053/252053/204'>LINK </a> Flourishing Heal - MA
<a href='itemref://229668/229668/180'>LINK </a> Grove Curator - Adv
<a href='itemref://224071/224071/183'>LINK </a> Imminence of Bloodshed - Keeper
<a href='itemref://223118/223118/190'>LINK </a> Impart Resentment - Enforcer
<a href='itemref://233846/233846/190'>LINK </a> Impel Shadowland Recall - Engie
<a href='itemref://218139/218139/197'>LINK </a> Implacability of the Second Law - NT
<a href='itemref://223140/223140/199'>LINK </a> Intense Targetted Nanoweb - Fixer
<a href='itemref://223310/223310/196'>LINK </a> Isochronal Sloughing Defensive Shield - Engie
<a href='itemref://223154/223154/195'>LINK </a> Lesser Slobber Wounds - Adv
<a href='itemref://252007/252007/205'>LINK </a> Lifecure - Adv
<a href='itemref://252009/252009/209'>LINK </a> Light of Life - Adv
<a href='itemref://236513/236513/204'>LINK </a> Malaise of Passion - Crat
<a href='itemref://223266/223266/195'>LINK </a> Mutagenic Catalyser - Doctor
<a href='itemref://223334/223334/211'>LINK </a> Military-Grade Predator M-30 - Engie
<a href='itemref://226400/226400/180'>LINK </a> Numbing Shock - Agent
<a href='itemref://231027/231027/190'>LINK </a> Peer Pressure - Crat
<a href='itemref://224124/224124/190'>LINK </a> Puissant Inhibit Motion - Crat
<a href='itemref://210381/210381/197'>LINK </a> Quintessence of Petrification - Shade
<a href='itemref://226440/226440/190'>LINK </a> Relieve Stress - Agent
<a href='itemref://210370/210370/183'>LINK </a> Sacrificial Caress - Shade
<a href='itemref://210372/210371/199'>LINK </a> Sacrificial Embrace - Shade
<a href='itemref://223146/223146/195'>LINK </a> Scale Repair - Adv
<a href='itemref://210519/210519/199'>LINK </a> Sentinel Ward - Keeper
<a href='itemref://233842/233842/190'>LINK </a> Shadowland Safeguard - Engie
<a href='itemref://252055/252055/209'>LINK </a> Soul of Rubi - MA
<a href='itemref://225897/225897/195'>LINK </a> Summon Urolok the Rotten - MP
<a href='itemref://252156/252156/206'>LINK </a> Supreme Health Haggler - Trader
<a href='itemref://223306/223306/190'>LINK </a> Synchronized Energy Spike - Engie
<a href='itemref://231024/231024/180'>LINK </a> Team Empowered Voice of Truth - Crat
<a href='itemref://231022/231022/180'>LINK </a> The Voice of Truth - Crat
<a href='itemref://223328/223328/180'>LINK </a> Upgraded Predator M-30 - Engie
<a href='itemref://223746/223746/192'>LINK </a> Vengeance of the Virtuous - Keeper
<a href='itemref://218065/218065/181'>LINK </a> Ward Blow - MA
<a href='itemref://226418/226418/195'>LINK </a> Waves of Illness - Agent</FONT> ";

$infgarn_txt = bot::makeLink("Inferno Garden Nanos", $infgarn_txt); 
if($type == "msg") 
bot::send($infgarn_txt, $sender); 
elseif($type == "all") 
bot::send($infgarn_txt); 
else 
bot::send($infgarn_txt, "guild"); 
?>