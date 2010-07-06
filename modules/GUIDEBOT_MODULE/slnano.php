<? 
$slnano_txt = "<header>::::: Shadowlands Garden Nanos:::::<end>\n\n"; 
$slnano_txt = "Here is an updated list of Nano Crystals sold in Gardens and Sanctuaries. 
Please notify your bot developer of any changes, errors, or discrepencies. 
<font color = yellow>
<a href='chatcmd:///tell <myname> nascgar'> - Nascence Garden Nanos</a>
<a href='chatcmd:///tell <myname> elygar'> - Elysium Garden Nanos.</a>
<a href='chatcmd:///tell <myname> elysancn'> - Elysium Sanctuary Garden.</a>
<a href='chatcmd:///tell <myname> shogar'> - Shoel Garden Nanos.</a>
<a href='chatcmd:///tell <myname> shosancn'> - Shoel Sanctuary Garden Nanos.</a>
<a href='chatcmd:///tell <myname> adogarn'> - Adonis Garden Nanos.</a>
<a href='chatcmd:///tell <myname> adosancn'> - Adonis Sanctuary Garden Nanos.</a>
<a href='chatcmd:///tell <myname> pengarn'> - Penumbra Garden Nanos.</a>
<a href='chatcmd:///tell <myname> pensancn'> -  Penumbra Sanctuary Garden Nanos.</a>
<a href='chatcmd:///tell <myname> infgarn'> - Inferno Garden Nanos.</a>
<a href='chatcmd:///tell <myname> infsancn'> - Inferno Sanctuary Garden Nanos.</a>
<a href='chatcmd:///tell <myname> panven'> - Pandemonium Vendors Nanos.</font></a> ";

$slnano_txt = $this->makeLink("Shadowlands Garden Nanos", $slnano_txt); 
if($type == "msg") 
$this->send($slnano_txt, $sender); 
elseif($type == "all") 
$this->send($slnano_txt); 
else 
$this->send($slnano_txt, "guild"); 
?>