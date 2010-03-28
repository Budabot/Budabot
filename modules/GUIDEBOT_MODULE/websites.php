<? 
/*
   ** Author: Plugsz (RK1)
   ** Description: Guides
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.21.2006
   ** Date(last modified): 12.21.2006
   ** 
   ** Copyright (C) 2006 Donald Vanatta
   **
   ** Licence Infos: 
   ** This file is for use with Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
   
$websites_txt = "<header>:::::Anarchy Online Websites  :::::<end>\n\n"; 
$websites_txt = "<font color = blue>-= Valuable Anarchy Online Websites =-</font>

<font color = yellow>Note: These links will be automatically opened in your web browser.</font>

General/Overall Information
 - <a href='chatcmd:///start http://naers.forumer.com/ '><font color = yellow>Newcomers Alliance Forums</font></a>
 
 - <a href='chatcmd:///start http://forums.anarchy-online.com/ '><font color = yellow>Official Forums</font></a>
 - <a href='chatcmd:///start http://www.aofroobs.com/ '><font color = yellow>AO Froobs.com</font></a>
 - <a href='chatcmd:///start http://www.ao-universe.com/ '><font color = yellow>AO Universe</font></a>
 - <a href='chatcmd:///start http://arcanum.aodevs.com/index.html '><font color = yellow>Anarchy Arcanum</font></a>
 - <a href='chatcmd:///start http://www.auno.org '><font color = yellow>Auno</font></a>
 - <a href='chatcmd:///start http://ao.stratics.com/index.php '><font color = yellow>AO Stratics</font></a>
 - <a href='chatcmd:///start http://gridstream.org/ '><font color = yellow>Gridstream Productions</font></a>
 - <a href='chatcmd:///start http://wiki.flw.nu/tiki-index.php?page=Atlas%20of%20Rubi-Ka '><font color = yellow>Atlas of Rubi-Ka</font></a>
 - <a href='chatcmd:///start http://travel.to/rubi-ka/ '><font color = yellow>Huge Map</font></a>
 - <a href='chatcmd:///start http://wiki.aodevs.com/wiki/Main_Page '><font color = yellow>AO Wiki</font></a>
 - <a href='chatcmd:///start http://www.creativestudent.com/ao/ '>CSP Map</a>
 - <a href='chatcmd:///start http://www.tirschool.com/ '><font color = yellow>Tir School Of Engineering</font></a>
 - <a href='chatcmd:///start http://www.unimob.aomarket.com/ '><font color = yellow>Unique Mobs and their Loot</font></a>
 - <a href='chatcmd:///start http://www.aotradeskills.streamlinenettrial.co.uk/index.htm '>AO Tradeskills (AOTS)</a>
 - <a href='chatcmd:///start http://www.anarchy-online.com/content/game/notumwars/map/atlantean.html '>Land Control Status</a>
 - <a href='chatcmd:///start http://arpa3.net/anarchy-online/ '>Kimi's AO Resources</a>
 - <a href='chatcmd:///start http://forums.vhabot.net/ '>Vhabot.net</a>
 - <a href='chatcmd:///start http://aovault.ign.com/ '>The Vault</a>
 - <a href='chatcmd:///start http://www.unityoftherose.com/tools/aggdefcalc.htm '><font color = yellow>Agg/Def Calculator </font></a>
 - <a href='chatcmd:///start http://www.aogms.com/default.aspx'><font color = yellow>AOGMS</font></a>
 - <a href='chatcmd:///start http://www.zanthyna.com/implant_clusters.htm'><font color = yellow>Premade Implants Listing</font></a>
Shadowlands Related

 - <a href='chatcmd:///start http://www.jexai.co.uk/anarchy/index.php '><font color = yellow>Jexai's AO Site</font></a>
 - <a href='chatcmd:///start http://aopocket.tngk.com/ '><font color = yellow>AO Pocket Symbiant Information</font></a>
 - <a href='chatcmd:///start http://www.forsaken.no/forums/viewtopic.php?t=1428 '>Spheremap</a> 


<font color = yellow>Note: These links will be automatically opened in your web browser.</font> "; 

$websites_txt = bot::makeLink("Anarchy Online Websites", $websites_txt); 
if($type == "msg") 
bot::send($websites_txt, $sender); 
elseif($type == "all") 
bot::send($websites_txt); 
else 
bot::send($websites_txt, "guild"); 
?>