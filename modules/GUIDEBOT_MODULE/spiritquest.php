<?php
 /*
   ** Author: Plugsz (RK1)
   ** Description: Guides
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03.18.2007
   ** Date(last modified): 03.18.2007
   ** 
   ** Copyright (C) 2007 Donald Vanatta
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

$blob = "<header>::::: Inferno Quests: Spirits Quest  :::::<end>\n\n
Spirits Quests

Guide suited for: All Classes
Faction: All
Level Range: 190-220

Note: Colored numbers behind NPCs named are spots marked on Inferno Quest: Map. 

One starts this quest chain with talking to One Whose Words Happen to Rhyme(1) (if you're wondering about name, it's Yutto), located at <font color = green>900x180</font> in Valley of the Dead (just past Penumbra-Inferno portal). 


<font color = yellow>Spirit Hunt</font>

You're sent out to kill 5 Contemplating Spirits, 5 Unrepentant Spirits and 1 Spirit of Disruption(2), located at <font color = green>985x170</font> in Valley of the Dead. 
That's among ruins just a bit North of One Whose Words Happen to Rhyme. 
Once you killed them and returned to Yutto, he'll reward you with A Never-ending Being of Light and send you on your next quest. 


<font color = yellow>Search for nuclei</font>

Now you need to kill 5 Somphos Argef, 5 Somphos Argeele and 1 Somphos Sorlivet(3) at <font color = green>1730x890</font> in Misty Marshes (just a bit East of Drop-off Point). 
After you killed each group of mobs, nuclei will spawn in your inventory (Nucleus of Somphos Argef,Nucleus of Somphos Argeele and Nucleus of Somphos Sorlivet, in that order). 
Those you need to deliver to a different Yutto, One Who Asks The Unasked(4) at <font color = green>1190x755</font> in Valley of the Dead. It will take over sending you to quests till end of this chain. 


<font color = yellow>Lost stories</font>

You need to talk to NPC spider, called Anansi Gopher(5) at <font color = green>855x722</font> in Valley of the Dead (start of ramp towards Drop-Off Point). This enables it to be attacked. 
Once you killed it, loot The Tale Of The Stubborn Yuttos off it. Since you need quest running in order to talk with it and you can only initiate conversation once, it's essential you get the loot once spider attacks or you're stuck. 
Return with Tale to Yutto. 


<font color = yellow>Capture the essence</font>

This time you need to kill 5 Meditating Spirits, 5 Conflicted Spirits and 1 Spirit of Coercion(6) located at <font color = green>2035 x 1185</font> in Burning marshes. 
Each spirit will drop Essence (Essence of Insight, Essence of Primal Understanding and Essence of Clear Thought), first two kinds you need to tradeskill assemble into Essence Mesh of Insight and Essence Mesh of Primal Understanding and return them to Yutto. 
Killing order in this quest isn't important, since you loot Essences from corpses. 
Reward for this quest is Stabilization Unit. 

<font color = yellow>Chasing Legends</font>

This quest is a bit tricky. One Who Asks The Unasked gives you Blood Of The Innocent and sends you out to use it on proper Tombstone and kill Spirit of Malice(7). 
Unfortunately Blood can only be used on one Tombstone (they are located at <font color = green>2330x1390</font> in Burning Marshes) and it only works on right one. 
One way to overcome this is to initiate conversation with Yutto so long until you gather 7 samples of Blood and use new one on each of Tombstones. 
Other way is to find right Tombstone (it will give vicinity text : Gere curam mei finis Requiem aeternam dona eis Et ira absorbeat eas tartarus 
- only last line is important) 


<font color = yellow>Joining the hunt</font>

This time you need to go after Relentless Spirit(8) and loot Essence of Perception.Bad news is that they don't have 100% dropchance, good news is that there are two camps. 
One is located at <font color = green>1430x375</font> in Burning Marshes (Those spirits are ql 200) and the other at <font color = green>2230x2170</font> in Inferno (Spirits are ql 220). 
Agents, Soldiers and Fixers get Perennium XXX Rebuild Kit. 


<font color = yellow>Snake Eyes</font>

Now Yutto wants you to bring him Ashen Viper Eye. As name indicates, they drop off Ashen Vipers(9), and yet again, it's not 100% dropchance. 
Those mobs have two camps too, one at <font color = green>677x1470 in Burning Marshes and another one at <font color = green>3170x3260</font> in Inferno. 

<font color = yellow>Killing the messenger</font>

You need to confront Ira Atlin at <font color = green>283x199</font> in Jobe Plaza. After you start talking to him, he'll walk off. Catch up with him and start talking again. He'll send you to Eddie. 
Eddie, located at <font color = green>1200x2820 in Street West Bank (just in front of Reet's Retreat) has a new item for sell. For a meager 1k credits you can buy Cryptovariable Deduction Algorithm from him, after you asked him 'What do you do?'. 

Bring back this item to Yutto. This wont end this quest just yet, since One Who Asks The Unasked now wants Compact Message Datadisc. You can pick those up from corpses of Courier Lizards(11), located at <font color = green>1820x1170</font> in Burning Marshes. 

<font color = yellow>Confronting the scientist</font>
This is the last quest from this line. As name suggest, you need to talk with Victor Nonya(12) at <font color = green>2140x1885</font> in Oasis. Once you tell him that Yuttos know what's he up to, he'll spawn 4 shade pets and attack. 
Since Victor warps back to his spot and changes his appearance, best strategy is to pull those pets first and kill them and only then center your attention on Victor. Bringing a team with calmer sure helps a lot. 
Once that is achieved and you return to Yutto to report, it will reward you with Ring of Plausibility. 
"
;
$msg = bot::makeLink("Inferno - Spirits Quest", $blob);
bot::send($msg, $sendto);
?> 
