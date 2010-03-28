<?
$apf42guide_txt = "<header>::::: Guide to Apf Sector 42 :::::<end>\n\n";
$apf42guide_txt = "
<font color=#4170e1>Quest: Sector 42 Key</font>

<font color=#4170e1>Quest Part 1: Data Cores</font>

To access Sector 42, you need a Data Core from the boss of Sectors 13, 28, and 25. <font color=#ff0000>You only need one Data Core from each boss. Please do not loot another core if you already have your key part!</font>

* <a href='itemref://258294/258294/1'>Kyr'Ozch Data Core</a> (Master Genesplicer - Cha'Khaz, Sector 13)
* <a href='itemref://258293/258293/1'>Kyr'Ozch Data Core</a> (Embalmer - Cha'Khaz, Sector 28 )
* <a href='itemref://258292/258292/1'>Kyr'Ozch Data Core</a> (Field Marshal - Cha'Khaz, Sector 35)

<font color=#4170e1>Quest Part 2: Encrypted Keys</font>
Trade your data cores with the Unicorn Administrator and he will give you one of three Encrypted Kyr'Ozch Keys (you get a different key for each Data Core. Each key has a different label.

* <a href='itemref://257536/257536/1'>Encrypted Kyr'Ozch Key</a> (OS BURR)
* <a href='itemref://257536/257535/1'>Encrypted Kyr'Ozch Key</a> (LUNG SEAL)
* <a href='itemref://257536/257534/1'>Encrypted Kyr'Ozch Key</a> (SABLE)

<font color=#4170e1>Quest Part 3: ???</font>
The rest of this quest has not yet been added to the game. :)
";

$apf42guide_txt = bot::makeLink("Guide to Apf Sector 42", $apf42guide_txt);
if($type == "msg")
bot::send($apf42guide_txt, $sender);
elseif($type == "all")
bot::send($apf42guide_txt);
else
bot::send($apf42guide_txt, "guild");
?>