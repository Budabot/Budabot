<? 
$rochkey_txt = "<header>::::: Scheol: Roch Garden Key Quest :::::<end>\n\n"; 
$rochkey_txt = "Scheol: Roch Garden Key Quest


To start the quest you need to head to the Unredeemed Temple and find Prophet Eckel-Roch, after speaking to him he'll tell you that some underlings are searching for a certain Empath of the Redeemed side, and of course you have to help them out. 

The Prophet will tell you to collect 4 separate pieces of the blueprint pattern of the Weary Empath Min-Ji Liu. These can be looted from Reedeemed mobs around the temples, it shouldn't take too long before you find all 4 patterns. 

Once you have all the four parts combine them as shown in our Pocket Boss Guide (do NOT use Crystal filled by the source on it, just show the Complete Blueprint Pattern). 

Head back to the temple and show it to Prophet Eckel Roch, he will hand it back to you with an Insignia of Roch and ask you to show it to the Sipius in the garden. 

There is an Ocra statue south from the Temple, enter the garden and find Hypnagogic Urga-Xum Roch, show him the completed pattern. He will tell you it needs to be Novictalized, complete the rest of the crystal as explained in the guide to receive your next mission. 

Note: The requirements are quite a lot lower for this Pocket Boss than usual: 

1. Only 337 Nano Programming is required rather than the 810 that would normally be needed. If you do not have this yourself, you can ask someone to do it for you. 

2. Only 160-165 Quantum FT is required rather than the 608 that would normally be needed. You are required to complete the last step yourself to get the new mission. 

Now you are ready to spawn the Weary Empath Min-Ji Liu. You can use the Incarnator in Scheol to spawn her. The Empath is level 140 and she is not easy. You won't be able to do this alone at level 100, so bring some friends along to help you. 

Also just like all other PB you will be reduced to 1 point of health when you spawn her. The good news is that she will not aggro you even if you're lower level than her. 

Kill the Empath and from her corpse loot the Old piece of cloth sparkling with notum. Head back to the garden and give it to Hypnagogic Urga-Xum Roch and you will be rewarded with The Key to the Garden of Roch! ";

$rochkey_txt = bot::makeLink("Scheol: Roch Garden Key Quest", $rochkey_txt); 
if($type == "msg") 
bot::send($rochkey_txt, $sender); 
elseif($type == "all") 
bot::send($rochkey_txt); 
else 
bot::send($rochkey_txt, "guild"); 
?>