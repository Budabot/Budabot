<? 
$fshop_txt = "<header>::::: Guide to The Fixer Shop :::::<end>\n\n"; 
$fshop_txt = "The shop connection is well hidden, but make your way to the neutral city of Borealis first. The easiest way should be through the Wompa in Newland City. Once there, head South-West over the city walls and you will come to a mountain, circled by a road and some small shack settlements along it. Probably where the workers of the various Omni-Mines in the area live.

Behind one of the shacks you'll come across a dump of junk and old tires, but taking a closer look one of the tires has a well hidden computer interface. You can target this - named 'Wrecked Shop' - and right-click it to get in touch with the shady characters running this little outlet of illegal merchandise.

The exact location is around 440 x 400, Zone: Borealis, Area: Azure Lake.

Keep in mind that only fixers or agents in disguise can access this, and that they need a Break & Entry skill of 180 to do so. That however does not stop you from enlisting a Fixer or an Agent to do some shopping for you if the price is right - I'm sure many of the fixers will be happy to make a little on the side for this.

Now, the real focus of this shop. Contraband, blackmarket items and otherwise useful gadgets you'd love to get your hands on.

First is a line of ICC cloaks, normally preserved for ICC personell, but equally useful for anyone else as long as they don't find out right? The cloaks go on your back and comes in different types and colors.
<font color = yellow>
ICC Bodyguard Cloak</font>
Requires Strength & Stamina to wear. Adds to your Bodydev and Psychology skills.
<font color = yellow>
ICC Messenger Cloak</font>
Requires Agility & Sense to wear. Adds to your Run Speed and Swimming skills. 	 
<font color = yellow>
ICC Delegate Cloak</font>
Requires Intelligence & Psychic to wear. Adds to your Max Nano and Nano Pool skills. 	  		  	
<font color = yellow>
ICC Pilot Cloak</font>
Requires Sense & Psychic to wear. Adds to your Vehicle Ground and Vehicle Water skills. 	 
<font color = yellow>  		  	
ICC Internops Cloak</font>
Requires Agility & stamina to wear. Adds to your Break & Entry and Concealment skills. 	  		  	
<font color = yellow>
ICC Secretary Cloak</font>
Requires Stamina & Psychic to wear. Adds to your Adventuring and First Aid skills. 	 

The cloaks all seem to come in various quality levels from 50-100 and the amount of boost to the listed skills range from 2-8. The usefulness of these cloaks can be debated, but you never known when a couple points in a particular skill is needed.

Among other clothing, we can also find some protective radiation pieces Omni-Tek uses for emergency situations. There is also a Skirt version, but they seemed to be currently out of stock.
<font color = yellow>
Wet Anti-radiation Body</font>
Requires strength to wear and has a higher than normal protection against radiation. There's also a little protection against chemicals. 	  		  	
<font color = yellow>
Wet Anti-radiation Sleeves</font>
Requires strength to wear and has a higher than normal protection against radiation. 	 

Next is a few usable gadgets:
<font color = yellow>
Large Sparkle Plate</font>
A protective shield that goes in your back-slot. Adds to Nano Pool and Max Nano,
as well as give you a +2 Reflect Energy Damage. The requirements to wear this is Computer Literacy and Stamina, as well as being level 60+
<font color = yellow>
Nanobot Factory Unit</font>
A gadget you can put in your NCU belt instead of NCU chips. Adds +5 to Nano Pool
and +40 to Max Nano for each piece (up to 6 with a 6-slot belt) and requires 191 Computer Literacy to install.
<font color = yellow>
Pick-a-Finger</font>
A ring really, for your right-finger, boosting your Break & Entry and Trap Disarming
skills by 16 each. Requirements are 251 Computer Literacy, 190 Break & Entry and level 60+
<font color = yellow>
Soltoyz Intimate Helper</font>
A quite handy little item for Neutrals I believe, as it goes in your neck slot.
Adds +40 to Max Health and +40 to Max Nano. Requirements are 151 Stamina and
level 40+

This is also were agents can get their hands on the Kevlar Wool Balaclava. First bought as a container - Agent Gear: Kevlar Wool Balaclava , with the Kevlar Wool Balaclava Helmet inside (right click the container to take it out). This is a 'level-item' that you can upgrade each level, and it will raise in AC and bonuses - a process that costs credits for each level upgraded. The key features of the Balaclava hood is the Critical Chance, Break & Entry, Perception and Run Speed bonuses. Note that this item is Agent-only.

Kevlar Wool Balaclava Helmet
Kevlar wool has lately got its renaissance. It does not offer superior protection, but the porousity of this type of wool makes it possible to augment certain abilities of the user

The last item of interest in this shop is the Implant Disassembly Unit that is used in the production of the tool that can remove clusters from implants, but that is another guide that should be up shortly.

Implant Disassembly Unit
This unit allows inserted clusters to be removed from any kind of basic implant.
It needs a backup machinery to work though, and sterile working conditions.

Fixers also get their special Cluster Bullets from this shop for their special attack.

And that was pretty much it. Some items from this shop have questionable usefulness, and some are highly useful for the right profession, level or side, so be sure to enlist your local fixer if you need anything from here. ";

$fshop_txt = $this->makeLink("Guide to The Fixer Shop", $fshop_txt); 
if($type == "msg") 
$this->send($fshop_txt, $sender); 
elseif($type == "all") 
$this->send($fshop_txt); 
else 
$this->send($fshop_txt, "guild"); 
?>