<? 
$elygar_txt = "<header>::::: Elysium Garden Nanos  :::::<end>\n\n"; 
$elygar_txt = "Elysium Garden    

<font color='#69E61E'>
<a href='itemref://223224/223224/38'>LINK </a> Artillery Fire - Soldier
<a href='itemref://229941/229941/180'>LINK </a> Boon of Ergo - Enel-Roch - MP
<a href='itemref://229931/229931/100'>LINK </a> Boon of Ergo - Ocra-Roch - MP
<a href='itemref://239744/239744/40'>LINK </a> Build Yuttos Upgraded Spirit Tech Source Projector - Adv
<a href='itemref://223373/223373/25'>LINK </a> Composite Attribute Boost (1 hour) - Gen
<a href='itemref://223381/223381/25'>LINK </a> Composite Nano Expertise (1 hour) - Gen
<a href='itemref://223349/223349/25'>LINK </a> Composite Ranged Expertise (1 hour) - Gen
<a href='itemref://223365/223365/25'>LINK </a> Composite Ranged Special Expertise (1 hour) - Gen
<a href='itemref://220332/220332/25'>LINK </a> Composite Teachings - MP
<a href='itemref://223361/223361/25'>LINK </a> Composite Melee Expertise (1 hour) - Gen
<a href='itemref://210402/210402/38'>LINK </a> Degeneration of Rapidity - Shade
<a href='itemref://219021/219021/26'>LINK </a> Empowered Distracted Gaze - Crat
<a href='itemref://233015/233015/32'>LINK </a> Empowered Lesser Deflection Shield - Soldier
<a href='itemref://233101/233101/28'>LINK </a> Empowered Partial Harmonic Cocoon - Engie
<a href='itemref://217040/217040/26'>LINK </a> Intense Micro Entanglement - Fixer
<a href='itemref://223188/223188/26'>LINK </a> Intensify Fight (Team) - Soldier
<a href='itemref://220346/220346/28'>LINK </a> Neuronal Stimulator - NT
<a href='itemref://223242/223242/25'>LINK </a> Refresh Outfit (Team) - Trader
<a href='itemref://210358/210358/34'>LINK </a> Ritualistic Grasp - Shade
<a href='itemref://224404/224404/25'>LINK </a> Summon Shadowweb Spinner MK I - Fixer
<a href='itemref://234081/234081/25'>LINK </a> Tap Notum Vein: Nascense - Fixer
<a href='itemref://231015/231015/31'>LINK </a> Team Empowered Dominate Psyche - Crat
<a href='itemref://231016/231016/39'>LINK </a> Team Empowered Solicit Support - Crat
<a href='itemref://210513/210513/39'>LINK </a> Ward - Keeper
<a href='itemref://226412/226412/25'>LINK </a> Waves of Anger - Agent</font> ";

$elygar_txt = bot::makeLink("Elysium Garden Nanos", $elygar_txt); 
if($type == "msg") 
bot::send($elygar_txt, $sender); 
elseif($type == "all") 
bot::send($elygar_txt); 
else 
bot::send($elygar_txt, "guild"); 
?>