<?php
$rochsanckey_txt = "<header>::::: Scheol: Roch Sanctuary Garden Key Quest :::::<end>\n\n"; 
$rochsanckey_txt = "Scheol: Roch Sanctuary Garden Key Quest


To do this quest you need at least one Novictalized Notum Crystal with 'Weary Empath Min-Ji Liu' (see Scheol: Roch Garden Key Quest walktrough for info how to obtain and create it. 

You start the quest by talking to Hypnagogic Urga-Xum Roch. He will ask you to talk to Omega Eric Miller that is standing close to the center of the Garden. He will ask for the Novictalized Notum Crystal. 

When you have given the novictalized crystal to Miller you have to spawn the Empath. Just select the empath and wait. (Don't kill it, you need to keep it alive until later) You should get a mission complete and you get a updated mission description: 

'Upon re-attuning the pattern, it somehow got blessed and it's now unuseable for your purposes. Only Roch can remove the blessing. Perhaps an immortal incarnation, with a strong connection to Roch, can help you call the Divine to interfere.' 

Go back and talk to Omega Eric Miller. He will then give you the Blessed Pattern of Min-Ji Liu. Now go to the Unredeemed temple (Near The Temple Bog) and talked to Prophet Eckel Roch. (staight trough the first room. Take left in the next. Through a door and follow the right wall). He then summon Unredeemed Roch for you so you can talk to him. He removes the blessing on the pattern and will turned it into Defiled Pattern of Min-Ji Liu. 

The updated Quest [Now a use item on item mission] tells you that the pattern needs to be bound to a Soul Catcher. 

It's a part of the Ancient machinery just outside Ergo in Scheol. Just use the pattern on the Ancient machinery and you will get a quest update. The last step is to go kill the Empath. 

You will have to respawn her if someone killed it while you where doing the other steps. When you kill her you'll get a mission update. Return to Omega Eric Miller. He will ask for the Defiled pattern and then give you the Sanctuary key. 

IMPORTANT: You will have be the one doing the majority of damage on the Empath. You won't get a mission update if you don't get loot rights. I highly recommend you bring some friends to keep you healed.  ";

$rochsanckey_txt = bot::makeLink("Scheol: Roch Sanctuary Garden Key Quest", $rochsanckey_txt); 
if($type == "msg") 
bot::send($rochsanckey_txt, $sender); 
elseif($type == "all") 
bot::send($rochsanckey_txt); 
else 
bot::send($rochsanckey_txt, "guild"); 
?>