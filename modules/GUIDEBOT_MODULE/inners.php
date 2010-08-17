<?php
$blob = "<header>::::: Guide to The Inner Sanctum :::::<end>\n\n
The Inner Sanctum dungeon is a high level dungeon geared towards players of level 125+ (though, rest assured that level 125+ players won't last very long in there). As the 'extension' of the 'Temple of Three Winds', this dungeon is similar in theme to the TotW, housing a mysterious cult of Hezak. Great adventures and treasures can be found inside the Inner Sanctum.

Location:
The Inner Sanctum can be reached by handing an 'Exarch Robe' from the TotW to the Blind Cultist, standing next to the temple. In return she will hand you a 'Sealed Inner Sanctum Pass'. Right-clicked, this returns an 'Inner Sanctum Pass' which can in turn be used on the portal next to the Blind Cultist, to be teleported to the Inner Sanctum. Inside the Inner Sanctum, you can get 'Inner Sanctum Knowledge' parts. the top and lower half of these combined result in the permanent key 'Inner Sanctum Knowledge', which will not disappear when used on the portal, contrary to the Inner Sanctum passes, which disappear once used.
Top Half of Key + Bottom Half of Key = Permanent Inner Sanctum Key

Where to get the key parts:

The lvl 235 mobs on both sides in room #2 drop a part of the permanent key each. Kill both to get a permanent key. Those 2 mobs also spawn lvl 100 pets called 'Fanatics'. They live for 42 seconds and after that they self-destruct with an AoE nuke of 7000-9000 dmg. So when you fight the 235s you now know what your priority should be.....

If you cannot get a permanent key right away, the High Exarch Robes and Archdeacon Robes dropping inside will do as keys to get back in. Simply hand one of these robes to the Blind Cultist to get a pass again.

Bosses you will encounter in this dungeon:

Hezak the Immortal

FIRST FLOOR:

High exarch robe
Bloodmark
Soulmark
Parasitic Hecataleech
Archdeacon robe
Corrupted Flesh
First Circle Of Inner Sanctum (various types)
Rod of Dismissal
Saemus' Crystallizer
Books for Karmic Fist
Funeral Urns

SECOND FLOOR:

Charred Abaddon Chassis + upgrade cards
Blighted Soulmark
Bloodshed Armband
Carapace of Infernal Tyrant armor parts
Ceremonial Watchman's Hood (small obsidian jar)
Ceremonial Chief's Headwear (obsidian jar)
Defiled Bloodmark
Dominus Robe
Frost Bound Reaper
Icebound Heart
Inner Sanctum Knowledge top & bottom half (for permanent key)
Iskop's Ascendancy (unconfirmed)
Jeuru's Oscillating Ligature
Maw of the Abyss
Preceptor Robe
Prelude to Chaos
Right Hand of Entropy (UNCONFIRMED - MAY NOT BE DROPPING!)
Second Circle of the Inner Sanctum(various types)
Bloodseal of the Infernal Tyrant
Bloodslave ring (unconfirmed)
Ring of Putrescent Flesh(unconfirmed)
Ring of Wilting Flame(unconfirmed)
Twilight's Murder

THIRD FLOOR:

Abyssal Desecrator (unconfirmed)
Desecrated Bloodmark(unconfirmed)
Impious Dominator(unconfirmed)
Might of the Revenant (unconfirmed)
Mnemonic Fragment (unconfirmed)
Permafrost (unconfirmed)
Skull of Misery (unconfirmed)
Skull of Despair(unconfirmed)
Soul Siphon (unconfirmed)
Teachings of the Immortal One(unconfirmed)
Third Circle of Inner Sanctum (various, unconfirmed)
Unhallowed Chalice (unconfirmed)
Gloomfall armor parts (unconfirmed)
Gelid Blade of Inobak

Dropped by Hezak the Immortal:

Constrained Gridspace Waveform
Rod of Dismissal
Mnemonic Fragment
Ichor of the Immortal One
Third Circle of the Inner Sanctum
 ";

$msg = bot::makeLink("Guide to Inner Sanctum", $blob); 
bot::send($msg, $sendto);
?>