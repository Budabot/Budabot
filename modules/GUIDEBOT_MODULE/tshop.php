<?php
$blob = "<header>::::: Guide to Trader Shops :::::<end>\n\n
The shop is an easily overlooked terminal marked Specialist Commerce found in each special quality superstore around Rubi-Ka. The terminal can only be accessed by Traders of at least level 25. The security is too tight for even the finest undercover Agents. In fact, the security grid seemed embarassed at our attempts.
  The shop holds a selection of items not found in the common stores. The items are in no way meant for Traders however. Rather, they are meant for other professions that need a Trader to act as the middleman and access the shop for them.
  In addition to the items that can be bought from the store, it offers Traders increased prices when selling items to the store. The increase may not seem major to the corporate types, but the clan affiliated will find that it is quite lucrative to sell the more valuable items here instead of any old regular shop.
  Most of the items available are various weapons, including the Adventurer pistol BBI Faithful and the Mark I version of the popular Nova Flow plasma series. For a full list of the contents, see below. Like regular stores, the contents of this one cycle and you might be unable to find all of the items on the list at times.

<font color = yellow>BBI Faithful</font>
Another gun aimed at explorers, pilots and pioneers. While many 'people of the wild' prefer weapons that don't use ammo, some still swear to pistols because they have low weight and are easy to conceal. The 'Faithful' is a quality design, easy to fire and clean, with a good potential damage output.
		  	
<font color = yellow>Tango Dirk</font>
This dirk is known to be favoured by rich dilettantes and amateurs trying to fake a genuine 'rough bar fighter' personality. In the hands of a professional it can be quite deadly, but it's uncertain whether a professional would really choose to use one.
		  	
<font color = YELLOW>True Katana</font>
'There is a common misconception about Katanas: That they should be fast and light. This is totally wrong - the original katana was rather slow and heavy, made for killing with one blow, not several small slashes - like a short sword.' These are the sales arguments for the 'True Katana'.
		  	
<font color = YELLOW>Arwen MO-404 Grenade Launcher</font>
The supply of the MO-404 is limited. It has got a mixed reception from common users, but the general opinion in the press seems to be positive.
		  	
<font color = YELLOW>Nova Flow - Mark I</font>
Being one of the major breakthroughs in the Nova Flow plasma series, the Mark I is much sought after today.
		  	
<font color = YELLOW>Schuyler's </font>
make classical bows, without all the fancy nano-guidance systems, layered bioplast straws and heated cams that doesn't help anyone much anyway. Schuyler's are simple bows, using the best of materials, requiring pure focus to fire.
		  	
<font color = YELLOW>Igelkott Automatic</font>
Igelkott is a specialist gun normally used by bureaucrats. It is meant to be easy to fire - and thus it uses pretty complicated technology.
		  	
<font color = YELLOW>Russian Good Day</font>
The real name of this gun is 'Zdrastvuite' - which is a greeting in the old language called 'russian'. Few can prounounce this word though, so the gun is commonly known as 'Russian Good Day'.

In addition to the many means of harming your fellow man or leet, the shop also sports the largest concentration of sugarfree rums on Rubi-Ka. The rums can be dual wielded with neglible skills and give various boosts from 1 Intelligence to 2 Break&Entry.

Sugarfree Rum
This ridiculous idea of rum was a hit in the 29350's. Several types were sold, all tasting mostly the same, but then again taste wasn't really what it was all about.

There are also the Experienced Aviator Glasses, something of a must for any and all that wants to look extra cool inside their yalmahas! The glasses are quite expensive and adds a bonus of 15 to both Vehicle Air and Vehicle Ground skills.

Experienced Aviator Glasses
Most people have seen the episode where Captain Jalle finds the gold planet - just to lose it in a black hole while he flirts with the mysterious 'Experienced Aviator'. These sunglasses are a copy of the sunglasses she wears in that episode.

Another novelty item is the Cap of the Besieger, just recently in - its a lowlevel headdress that will protect your noggin' from some beating. Its fairly low, ending at QL15 with a 45 Stamina and Psychic requirement but still a nice item, especially if you dislike walking around with helmets obscuring your face's lovely features. As far as AC goes, its not the best bet in the world, but it makes up for the lack of AC in its style.

Cap of the Besieger
This cap was used during the second rebellion on Titan-IC. It positively helped the rebels survive the heavy barrage from biochemical and nuclear weapons during their sieges. They took more than 10 cities during the first month, but were pushed back
when reinforcements arrived from Omni Prime

And there you are. Though a few of these weapons remain good, it has been a while since they landed on Rubi-Ka and the quality stagnates as new ones arrive. So it could be hoped that this store would also get some new entries one of these days. Perhaps something other than weapons, perhaps something for Traders themselves.
";

$msg = bot::makeLink("Guide to Trader Shops", $blob); 
bot::send($msg, $sendto);
?>