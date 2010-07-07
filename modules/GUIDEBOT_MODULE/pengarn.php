<? 
$pengarn_txt = "<header>::::: Penumbra Garden Nanos  :::::<end>\n\n"; 
$pengarn_txt = "Penumbra Garden
<font color='#69E61E'>
<font color='#69E61E'><a href='itemref://233850/233850/150'>LINK </a> Awaken Shadowland Soul Memory - MP
<font color='#69E61E'><a href='itemref://226430/226430/150'>LINK </a> Blast of Neglect - Agent
<font color='#69E61E'><a href='itemref://218123/218123/147'>LINK </a> Chilling Presence - NT
<font color='#69E61E'><a href='itemref://223214/223214/153'>LINK </a> Circumvent Me - Soldier
<font color='#69E61E'><a href='itemref://223316/223316/205'>LINK </a> Devastator Drone - Engie
<font color='#69E61E'><a href='itemref://227110/227110/151'>LINK </a> Distortion of Resolve - MP
<font color='#69E61E'><a href='itemref://227150/227150/150'>LINK </a> Distortion of Will - MP
<font color='#69E61E'><a href='itemref://222707/222707/149'>LINK </a> Element of Ice - Enforcer
<font color='#69E61E'><a href='itemref://233096/233096/154'>LINK </a> Empowered Greater Harmonic Cocoon - Engie
<font color='#69E61E'><a href='itemref://233050/233050/148'>LINK </a> Empowered Reflective Field - Solider
<font color='#69E61E'><a href='itemref://220348/220348/150'>LINK </a> Enfraam's Cortex Accelerator - NT
<font color='#69E61E'><a href='itemref://218131/218131/149'>LINK </a> Enfraam's Glacial Encasement - NT
<font color='#69E61E'><a href='itemref://223250/223250/150'>LINK </a> Enliven Outfit (Team) - Trader
<font color='#69E61E'><a href='itemref://218125/218125/156'>LINK </a> Entropy's Advance - NT
<font color='#69E61E'><a href='itemref://223139/223138/153'>LINK </a> Intense Gravity Bindings - Fixer
<font color='#69E61E'><a href='itemref://236509/236509/171'>LINK </a> Malaise of Emotion - Crat
<font color='#69E61E'><a href='itemref://218079/218079/152'>LINK </a> Petals on Water - MA
<font color='#69E61E'><a href='itemref://224122/224122/151'>LINK </a> Puissant Musculature Command - Crat
<font color='#69E61E'><a href='itemref://223332/223332/207'>LINK </a> Semi-Sentient Predator M-30 - Engie
<font color='#69E61E'><a href='itemref://233848/233848/150'>LINK </a> Shadowland Soul Fetter - MP
<font color='#69E61E'><a href='itemref://224411/224411/153'>LINK </a> Summon Shadowweb Spinner MK IV - Fixer
<font color='#69E61E'><a href='itemref://234076/234076/145'>LINK </a> Tap Notum Vein: Adonis - Fixer
<font color='#69E61E'><a href='itemref://231021/231021/150'>LINK </a> Team Empowered Total Mental Domination - Crat
<font color='#69E61E'><a href='itemref://223744/223744/159'>LINK </a> Vengeance of the Virtuous - Keeper
<font color='#69E61E'><a href='itemref://227673/227673/140'>LINK </a> Weapon Smithing Mastery - Engie</font> ";

$pengarn_txt = bot::makeLink("Pennumbra Garden Nanos", $pengarn_txt); 
if($type == "msg") 
bot::send($pengarn_txt, $sender); 
elseif($type == "all") 
bot::send($pengarn_txt); 
else 
bot::send($pengarn_txt, "guild"); 
?>