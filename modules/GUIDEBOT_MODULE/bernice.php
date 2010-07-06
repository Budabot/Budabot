<? 
$bernice_txt = "<header>::::: Guide to Thin Bernice :::::<end>\n\n"; 
$bernice_txt = "In Perpetual Wastelands in the outpost at 1050 x 2500 you can find a peddler called Thin Bernice selling her wares at discount prices. In her case, discount means expensive. Very expensive. But hey - nobody is perfect. You can also grab some ammunition from her if you should want to restock on that, but the real gems are the selections of tanks and Battlesuits.

Both the regular versions as well as the Omni-Tek only versions of the Tank Armors is available in her store inside the quality range of 50 to 125. If you really don't feel like running a mission for your Tank armor, Thin Bernice might very well be the answer to your prayers.
<FONT COLOR = YELLOW>
Very Light Tank Armor
Very Light Omni-Tek Tank Armor
Light Tank Armor
Light Omni-Tek Tank Armor
Medium Tank Armor
Medium Omni-Tek Tank Armor
Heavy Tank Armor
Heavy Omni-Tek Tank Armor
</FONT>

Apart from the Tank Armors, Thin Bernice also has received rather a large shipment of Battlesuits. Since Thin Bernice is the only merchant selling these items specifically the odds of you finding something along the lines of what you are looking for are pretty big.
<FONT COLOR = GREEN>
Urban Battle Suit
Plasteel Battle Suit
Enhanced Battle Suit
Desert Battle Suit
</FONT>

Keep in mind that Battlesuits have a rather hefty nanocost penalty - all the way up to 200% nanocost will hurt your nanopool as much as the cost of them will hurt your wallet. 

The Enhanced one is actually everything but enhanced compared to the rest. It has nice three main AC's covered, but the rest of them are really a shambles. The Urban Battlesuit has a slightly higher Chemical AC, the Plasteel has really nice Fire AC but the real gem is the Desert Battle Suit, covering pretty much all the AC's except for Radiation and Poison.

Of course, all of the Battle Suits suffer from the same drawback, namely the nano% cost penalty. The penalty at low levels can really be painful, especially if you are playing a class that relies to it or if you have a really low nanopool.
 ";

$bernice_txt = $this->makeLink("Guide to Thin Bernice", $bernice_txt); 
if($type == "msg") 
$this->send($bernice_txt, $sender); 
elseif($type == "all") 
$this->send($bernice_txt); 
else 
$this->send($bernice_txt, "guild"); 
?>