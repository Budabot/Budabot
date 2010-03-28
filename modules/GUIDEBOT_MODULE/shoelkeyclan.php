<? 
$shoelkeyclan_txt = "<header>::::: Shoel Garden Key Quest CLAN :::::<end>\n\n"; 
$shoelkeyclan_txt = "Shoel Garden Key Quest CLAN

<font color='#ff9933'><highlight>Ocra Garden Key Quest</font></end>

<highlight>Guide suited for:</end> All Classes
<highlight>Faction:</end> Clan
<highlight>Level Range:</end> 50-100

Ocra Garden Key Quest

Guide suited for: All Classes
Faction: Clan
Level Range: 50-100

To start the quest you need to head to the Redeemed Temple and find Ecclesiast Ocra Lux, after speaking to him he will ask you to find out about a trapped spirit and will send you speak to someone else within the temple. 
Talk to Diviner Ocra Bela-Ilad (in the same room) who will tell you what you are looking for and where to find it. 

The Diviner will tell you to collect 4 separate pieces of the blueprint pattern of the Weary Empath Min-Ji Liu. These can be looted from Unredeemed mobs around the temples, it shouldn't take too long before you find all 4 patterns. 

Once you have all the four parts combine them as shown in our Pocket Boss Guide (do NOT use Crystal filled by the source on it, just show the Complete Blueprint Pattern). 

Head back to the temple and show it to Ecclesiast Ocra Lux, he will hand it back to you with an Insignia of Ocra and ask you to show it to the Sipius in the garden. 

There is an Ocra statue south from the Temple, enter the garden and find Sipius Ocra Nuir-Cama, show him the completed pattern. He will tell you it needs to be Novictalized, complete the rest of the crystal as explained in the guide to receive your next mission. 

Note: The requirements are quite a lot lower for this Pocket Boss than usual: 

1. Only 337 Nano Programming is required rather than the 810 that would normally be needed. If you do not have this yourself, you can ask someone to do it for you. 

2. Only 160-165 Quantum FT is required rather than the 608 that would normally be needed. You are required to complete the last step yourself to get the new mission. 

Now you are ready to spawn the Weary Empath Min-Ji Liu. You can use the Incarnator in Scheol and get ready to talk to her (do NOT attack her). 

Also just like all other PB you will be reduced to 1 point of health when you spawn her. 
I suggest you have a healer type friend (doctor, adv, ma) with you just in case. 
The good news is that she will not aggro you even if you're lower level than her. 

The Empath will act like an NPC (unless you attack her), she'll talk to you for a while.  
As the Empath vanishes she'll leave an Old piece of cloth sparkling with notum. 
Head back to the garden and give it to Sipius Ocra Nuir-Cama and you will be rewarded with The Key to the Garden of Ocra! 

--------------------------------------------------------------------------------
Last updated on 10.08.2006 by Trgeorge
Information originally provided by Windguaerd.
Courtesy of AO Universe
"
;
$shoelkeyclan_txt = bot::makeLink("Shoel: Garden Key Quest CLAN", $shoelkeyclan_txt); 
if($type == "msg") 
bot::send($shoelkeyclan_txt, $sender); 
elseif($type == "all") 
bot::send($shoelkeyclan_txt); 
else 
bot::send($shoelkeyclan_txt, "guild"); 
?>