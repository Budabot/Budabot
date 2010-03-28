<? 
$adogarn_txt = "<header>::::: Adonis Garden Nanos  :::::<end>\n\n
<font color='#69E61E'>
<a href='itemref://218073/218073/103'>LINK </a> Aegis of Stone - MA
<a href='itemref://218105/218105/100'>LINK </a> Blood of Hephaestos - NT
<a href='itemref://239752/239752/125'>LINK </a> Build Spirit Tech Source Drill - Adv
<a href='itemref://223248/223248/100'>LINK </a> Cleanse Outfit (Team) - Trader
<a href='itemref://218113/218113/102'>LINK </a> Combustive Envelopment - NT
<a href='itemref://220336/220336/100'>LINK </a> Composite Infuse With Knowledge - MP
<a href='itemref://233044/233044/106'>LINK </a> Empowered Partial Reflective Field - Solider
<a href='itemref://223192/223192/108'>LINK </a> Heighten Fight (Team) - Soldier
<a href='itemref://210392/210392/110'>LINK </a> Intrinsic Dissipation - Shade
<a href='itemref://223262/223262/100'>LINK </a> Mutagenic Venom - Doctor
<a href='itemref://224120/224120/102'>LINK </a> Puissant Illusionary Paralysis - Crat
<a href='itemref://210379/210379/111'>LINK </a> Quintessence of Paralyzation - Shade
<a href='itemref://210364/210364/102'>LINK </a> Sacrificial Touch - Shade
<a href='itemref://218115/218115/111'>LINK </a> Searing Dioxin Shower - NT
<a href='itemref://223314/223314/201'>LINK </a> Slayerdroid Annihilator - Engie
<a href='itemref://224409/224409/103'>LINK </a> Summon Shadowweb Spinner MK III - Fixer
<a href='itemref://223328/223328/180'>LINK </a> Upgraded Predator M-30 - Engie
<a href='itemref://226414/226414/100'>LINK </a> Waves of Jarring - Agent</font> ";

$adogarn_txt = bot::makeLink("Adonis Garden Nanos", $adogarn_txt); 
if($type == "msg") 
bot::send($adogarn_txt, $sender); 
elseif($type == "all") 
bot::send($adogarn_txt); 
else 
bot::send($adogarn_txt, "guild"); 
?>