<? 
$pensancn_txt = "<header>::::: Penumbra Sanctuary Garden Nanos  :::::<end>\n\n"; 
$pensancn_txt = "Penumbra Sanctuary (Vanya)
<font color='#69E61E'>
<a href='itemref://226712/226712/166'>LINK </a> Abolish Aluminium - Trader
<a href='itemref://229099/229099/170'>LINK </a> Aggressive Insult - Solder
<a href='itemref://223318/223318/209'>LINK </a> Battlefield Devastator Drone - Engie
<a href='itemref://218133/218133/161'>LINK </a> Biomolecular Corrosion - NT
<a href='itemref://223194/223194/171'>LINK </a> Boost Fight (Team) - Solder
<a href='itemref://210505/210505/168'>LINK </a> Carnage Reaper - Keeper
<a href='itemref://210394/210394/162'>LINK </a> Elemental Dissipation - Shade
<a href='itemref://224138/224138/170'>LINK </a> Empowered Cubicle Dweller - Crat
<a href='itemref://233052/233052/164'>LINK </a> Empowered Greater Reflective Field - Solder
<a href='itemref://230387/230387/190'>LINK </a> Gallant Slave: The Incensed Subordinate - Crat
<a href='itemref://226274/226274/171'>LINK </a> Irebringer - Enforcer
<a href='itemref://236511/236511/198'>LINK </a> Malaise of Desire - Crat
<a href='itemref://223264/223264/170'>LINK </a> Mutagenic Contamination - Doctor
<a href='itemref://223130/223130/174'>LINK </a> Path of Shadow - Fixer
<a href='itemref://210368/210368/165'>LINK </a> Sacrificial Grasp - Shade
<a href='itemref://227393/227393/175'>LINK </a> Slip of Idea - Fixer
<a href='itemref://223232/223232/170'>LINK </a> Snitch Notum - Trader
<a href='itemref://225895/225895/170'>LINK </a> Summon Biazu the Vile - MP
<a href='itemref://224413/224413/170'>LINK </a> Summon Shadowweb Spinner MK V - Fixer
<a href='itemref://234078/234078/165'>LINK </a> Tap Notum Vein: Penumbra - Fixer
<a href='itemref://218135/218135/173'>LINK </a> Touch of the Pyre - NT
<a href='itemref://235284/235284/170'>LINK </a> Umbral Wrangler (Advanced) - Trader
<a href='itemref://226416/226416/170'>LINK </a> Waves of Numbing - Agent</FONT> ";

$pensancn_txt = bot::makeLink("Penumbra Sanctuary Garden Nanos", $pensancn_txt); 
if($type == "msg") 
bot::send($pensancn_txt, $sender); 
elseif($type == "all") 
bot::send($pensancn_txt); 
else 
bot::send($pensancn_txt, "guild"); 
?>