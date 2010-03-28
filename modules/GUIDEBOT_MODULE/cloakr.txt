<? 
$cloakr_txt = "<header>::::: Cloak Of The Reanimated Upgrade Process :::::<end>\n\n"; 
$cloakr_txt = " Cloak of the Reanimated Upgrade Process

Guide suited for: All Classes
Faction: All
Level Range: All Levels

There are actually 6 different types of this cloak, but since they are all made in a same way (with exception of 1 step), we’ll just introduce one of them. First of all, you’ll need Reanimator’s Cloak, which is built from items found in Crypt of Home. Then you’ll also need 8 different items dropping from escaped prisoners. Please note that I said 8 different items, since for fully upgraded cloak you’ll actually need 27 of them. Majority of those is made of Soul of the XXX crystals, you’ll need 4 full sets of 6 as well as additional one that sets the type of cloak you wish to build. So let’s review what’s needed 
Mobs are found in Milky Way. Update to version 1.3.1 of AORK map, drill into level 3 and the prisoner locations are marked.

<font color='#69E61E'>Reanimator’s Cloak</font>
 
 <font color = yellow>4x Soul of the Jester
 4x Soul of the Ranger
 4x Soul of the Summoner
 4x Soul of the Healer
 4x Soul of the Gladiator
 4x Soul of the Illusionist
 Empty Spirit Vest
 Soul Ripper
 1x Soul of the XXX</font> that sets the type
 
Before the frustration sets in, let me explain you don’t need all the items before you can start building and reap the benefit of cloak. But with each upgrade, cloak mods are becoming stronger, unfortunately but this also means requirements to wear are higher. 
But let’s get cracking 

<font color='#69E61E'>Reanimator’s Cloak</font>   +   <font color='#69E61E'>Soul Ripper</font>   =   <font color = yellow>Explosion of Light</font> 


Explosion of light is in reality an effect that will spawn Soul of the Reanimator and Grey Cloak in your inventory. Soul Ripper gets used up in the process. 


<font color='#69E61E'>Soul of the Reanimator</font>  +   <font color='#69E61E'>Soul of the XXX</font> =   <font color = yellow>Soul of the Reanimated XXX </font>


At this stage you set the type of Cloak you’re building. For the purpose of presenting build, we’ll describe how to build Cloak of the Reanimated Jester, but you can build whatever cloak you wish on same principle. 


<font color='#69E61E'>Soul of the Reanimated XXX</font>   +   <font color='#69E61E'>Empty Spirit Vest</font>   =   <font color = yellow>Jester’s Spirit Vest</font> 

<font color='#69E61E'>Jester’s Spirit Vest</font>   +   <font color='#69E61E'>Grey Cloak</font>   =   <font color = yellow>Cloak of the Reanimated Jester </font>


So in four easy steps you can have working cloak. To upgrade it further it will require a lot more of farming Soul crystals and also a lot more combining 


<font color='#69E61E'>Soul of the Ranger</font>   +   <font color='#69E61E'>Soul of the Gladiator</font>   =   <font color = yellow>Dull Combined Soul Crystal </font>

<font color='#69E61E'>Dull Combined Soul Crystal</font>   +   <font color='#69E61E'>Soul of the Healer</font>   =   <font color = yellow>Dim Combined Soul Crystal </font>

<font color='#69E61E'>Dim Combined Soul Crystal</font>   +   <font color='#69E61E'>Soul of the Summoner</font>   =   <font color = yellow>Sparkling Combined Soul Crystal </font>

<font color='#69E61E'>Sparkling Combined Soul Crystal</font>   +   <font color='#69E61E'>Soul of the Illusionist</font>   =   <font color = yellow>Shining Combined Soul Crystal </font>

<font color='#69E61E'>Shining Combined Soul Crystal</font>   +   <font color='#69E61E'>Soul of the Jester</font>   =   <font color = yellow>Awaken Soul Crystal </font>


These steps will require no skill to make, beside patience, since for fully upgraded Cloak you’ll need to repeat it 4 times. 


<font color='#69E61E'>Cloak of the Reanimated Jester</font>   +   <font color='#69E61E'>Awaken Soul Crystal</font>   =   <font color = yellow>Cloak of the Reanimated Jester </font>

<font color='#69E61E'>Cloak of the Reanimated Jester</font>   +   <font color='#69E61E'>Awaken Soul Crystal</font>   =   <font color = yellow>Cloak of the Reanimated Jester </font>

<font color='#69E61E'>Cloak of the Reanimated Jester</font>   +   <font color='#69E61E'>Awaken Soul Crystal</font>   =   <font color = yellow>Cloak of the Reanimated Jester </font>

<font color='#69E61E'>Cloak of the Reanimated Jester</font>   +   <font color='#69E61E'>Awaken Soul Crystal</font>   =   <font color = yellow>Cloak of the Reanimated Jester </font>


A final note if you forget which upgrade you’re currently own, just check description of Cloak. 
The number of lit lights can tell you that. 
And when the Cloak is fully upgraded, it get's a special attack, which you can use every 10 minutes. 

Guide courtesy of AO Universe 18 June 2008
";
$cloakr_txt = bot::makeLink("Cloak Of The Reanimated Upgrade Process", $cloakr_txt); 
if($type == "msg") 
bot::send($cloakr_txt, $sender); 
elseif($type == "all") 
bot::send($cloakr_txt); 
else 
bot::send($cloakr_txt, "guild"); 
?>