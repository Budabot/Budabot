<? 
$nasckey_txt = "<header>::::: Guide to Nascence Garden Key :::::<end>\n\n"; 
$nasckey_txt = "Nascence: Thrak (Omni) Garden Key Quest


This quest gives you the ability to travel between the Unredeemed transit statues of Nascense without using insignias. You need at least 5 Insignias of Thrak to complete it. Insignias drop from Redeemed mobs and creatures which are not affiliated with the Unredeemed faction. 

Insignia of Thrak


First you need to have a bracer from Scientist Drake in the Jobe Training Ground. If you don't have one, talk to him and tell him so and he'll give you one. You don't need to actually do any of his tests, but you do need the bracer. 

Use the portal to Nascene in 'The Harbour' area of Jobe City to get to Nascense Frontier, find Scientist Veronica Escobar and speak to her until she gives you an Ancient Device and a mission. 

Ancient Device

The next step is for you to get to the Unredeemed village located on the west side of the area (Brawl garden exit). 


Speak to Prophet Yutt at the Unredeemed village and show him the Ancient Device. 

He then asks for proof of Thrak's existence, show him an Insignia of Thrak. Next he will asks you to find and mark the Statue of Thrak. Get back to the statue that you used to reach him (closest to him, check the map above), use an Insignia to enter to the Garden of Thrak. 

At the Garden speak to Hypnagogic Urga-Lum Thrak and he'll tell you more about the device, and asks you to capture three souls with an Ancient Device. 

Take the second of these to get back to to Donna Red, then head west toward the Frontier Bridge. Near the bridge you should be able to find 3 Swift Silvertails, they're usually in groups. 

Shift right-click an Insignia of Thrak on the Ancient Device to turn it into an Ancient Pattern Analyser and speak to the first Silvertail, putting the device over it's eyes. You will know that it worked once the Swift Silvertail becomes a Cursed Silvertail which should get a mission accomplished message, the device then will reset for the next one. Repeat the procedure for the 2nd and 3rd Swift Silvertails (which means that you need to use another 2 insignias). 


Ancient Device   +   
Insignia of Thrak   =   
Ancient Pattern Analyzer favored by the Chosen One 

Go back to the Unredeemed Garden and show the device to Hypnagogic Urga-Lum Thrak and he'll give you the key as a reward for your effort. 

The Key to the Garden of Thrak ";

$nasckey_txt = $this->makeLink("Guide to Nascence Garden Key", $nasckey_txt); 
if($type == "msg") 
$this->send($nasckey_txt, $sender); 
elseif($type == "all") 
$this->send($nasckey_txt); 
else 
$this->send($nasckey_txt, "guild"); 
?>