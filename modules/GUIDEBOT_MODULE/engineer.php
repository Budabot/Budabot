<? 
$engineers_txt = "<header>::::: Guide to Engineers :::::<end>\n\n"; 
$engineers_txt = " Starting to Play

When you first start any profession on Rubi-Ka you are given a small number of items to start you off.

For an Engineer these are:

    * Solar-Powered Pistol
    * Engineer: Startup Crystal - Feeble Automation
    * Engineer: Startup Crystal - Swift Weapon

The most important thing you'll have here is your very first Robot Shell. As well as some health kits, first aid kits and if you're Omni or Clan a blank token board.

You may need to raise some of your skills to upload these nano crystals and activate the bot. So either press the 'U' key or the button at the bottom of your screen called 'SKL'. You'll find the skills you need under 'Nano & Aiding'.

You can see a description of any item or player by holding down shift and left clicking on it. The description contains all the information about the skills you need to wear or use the item.

If you need to remake your robot you can do so by right clicking on the Nano Progam under the Space section. This makes a bot shell in your inventory again. Bots cost credits though, so make sure you kill a few creatures before losing your first pet.

This bot will last you until you terminate it, it dies, or you log off. If you lose your connection the bot will wait for 2 minutes for you to reconnect before powering down.

3. Controlling Yourself and the Bot

Pets are controlled through the command line (ie hit enter, type in the command). But don't panic there are ways to make this much easier!

3.1. Macros

You can either have the game help you with this or do them yourself. The command '/macro' creates a little icon for your to place on your shortcut bar (press 'Y' if you can't see that). Which then allows you to perform the command just by pressing a number from 1 - 0 depending on it's position on the bar.

You'll soon see what I mean after you've tried it if that didn't make sense.

If you want the game to help you simply type in '/help pet' and click on each of the links to create the macro.

If you'd like to use your own then the following may be helpful:

/macro Attack /pet attack
/macro Report /pet report
/macro Wait /pet wait
/macro Follow /pet follow
/macro Behind /pet behind
/macro Hunt /pet hunt
/macro Rename /pet rename 'Mini Slayer'

Since our pet can't heal the '/pet heal' command is useless to us.

With regard to the command '/pet hunt' - Pet hunt uses an algorithm that chooses the next target by shortest line distance. This means it has a tendancy to choose targets in a fairly unpredictable fashion. That is it won't follow a logical room to room path if a mob in another room is closer even if it has to run through 4 rooms to get to the 'closer' mob. Its highly recommended that you use this command with extreme caution.

Just a quick couple of notes on bot names:

They must be at least 4 characters long.
If you have a space in the name you must inclose the name in ''.
According to the terms you agree to play the game you cannot name the bot to the same name as an NPC character or static mob.
If you'd like a colourful name for your bot then head over to Kuren's site to his colour helper here.

3.2. Advanced Macros

When you reach level 100 you get the option of a Mech Dog pet in addition to your main attack pet. You can continue to control both pets through a single general command or you can control each seperately.

With a correctly written command you can make it refer to a specific pet by including the pet name in the command.

All pet commands you use take the form:

/pet '<pet name>' <command>

eg /pet 'Bob the Slayer' attack
or /pet 'Ralph the Mech Dog' attack

These types of macro's work particularly well in conjunction with rename macros, otherwise the macro would have to be updated every time the pets name changed. Finally, if you use colourful names then the command must reflect that or it won't work.

3.3. Scripts, Variables & Chat Commands

Scripts are text files that sit in a Scripts folder inside of the Anarchy Online program folder. Scripts do not require any extension (the last part of the file name, like .txt) and will work with and without.

These files are command lists and are fairly versatile.

The following script has no real purpose, but should provide enough of an engineer for script creation.

Filename: Attack

/w Eek! Look out there's a nasty %t!
/pet chat 'Fear not master I shall kill it for you!'
/delay 5000
/v Well go on then
/pet chat 'Could you move this pebble?'
/pet attack
/me sighs

This script would be called by entering the it as a command into AO (ie '/Attack') which would then run through the commands one at a time.

Variables are useful things that allow us to name things in chat or commands without typing the full name.

%t is replaced with the name of your target
%m is replaced by your own name
%f is replaced by the target you are currently fighting
%1 - %5 are replaced by the words immediately following the script name.

engineers:

Filename: fighting
Called with: /fighting

Attacking --> %f <--

Filename: buffs
Called with: /buffs <variable 1> <variable 2> <variable 3> (eg /buffs MPGuy mochams 500k)

/v Hi %1, Could I trouble you for composite %2 please. Paying %3.

Shortcuts for certain chat channels are also available to you.

/s is shout
/v is talk in vicinity
/w is whisper
/g is talk in team chat
/o is talk in organisation (guild) chat
/me refers to yourself

Finally it's also possible to change chat channel like this as well.

/ch clan ooc changes to Clan OOC chat
/ch team changes to team chat

It will also recognise partial names provided it's enough to get a unique match. So if your guild was called 'Super Guild Version 12.4' then '/ch Super' would be enough to switch to that channel.

3.4. Pet Command & Maximum Range

This section covers two issues.

    * Command Range - You can only issue commands to your bot within a relatively small range (approximately 10 - 15 metres). Issuing commands outside of this range will have no effect.
    * Maximum Range - Pets will cease fighting and return to us if the pet gets 50 metres or further away from us. You will recieve a warning and some time to get back in range when this happens.
	
4. Affording your Engineer

Getting enough credits to fund your character is pretty hard to start off with, this is the same for all professions, but I think more so on those that depend on their nanos.

If you find you don't have enough credits to afford the next bot up, then slow down! Take a little while to gather those credits up and relax a little. Levelling is not the most essential part of the game, having fun is.

'It's not the end of the journey that matters, it's the path you take to get there'

 We'll take a closer look at some things that can help you make a few credits.

4.1. Begging

Don't do it. Too much of this happens already and this may well get you ignored by a large quantity of the higher level population. There are other ways, they may take longer but you won't annoy anyone in the process.

4.2. Soloing Missions

Before we begin looking at missions as a way of making credits make sure you get Clicksaver.

Get a Lockpick, nothing worse than being stopped by a locked door, or unable to steal a poor mobs precious belongings because they were inconsiderate enough to have locked it away.

Also make sure that you get yourself a few backpacks to put all the items you loot in. Then it's simply a case of kill and loot everything you can, after all there's no team there with you.

In the next couple of sections I'll include a mission settings for you to try out. The settings below gives you a high chance of human mobs. The choice is then between XP and Money as the reward.

We use the Good / Bad setting in this case just to get missions that are nicer to do. That is avoiding assasination missions and observing missions, as well as return item mission since we want the most stuff for ourselves!

Mission Setting: Bad, Money, Order
Mission Setting: Good, XP, Order

Soloing missions is an excellent (if time consuming) way of getting items and credits for yourself, and it's well worth learning to do.

Soloing team missions is even better than solo missions if you can. Of course to do this you at least temporarily needs a team mate.

4.3. Blitzing Missions

Mission Setting: Bad, Money, Order

Blitzing is a process of running, or calming (if you're an NT, Trader or Crat) through a mission purely for the reward, that means you don't kill anything. Runspeed is probably the most important skills for doing this.

The chance of recieving an xp reward is directly related to the percentage of mobs killed in a mission, we don't want an XP reward since we're not going to kill any mobs.

Always save (use Insurance Terminal) before you attempt a blitz, this way if you die during a blitz you have lost nothing except for the couple of minutes it takes to recover.

4.4. Mission Location

Location is important, the further away from the mission terminal the mission is the more credits you'll get as a reward. This doesn't mean we have to run into the middle of nowhere to get a couple of hundred more credits though. Check out getting missions from other cities or towns and see how easy they are to get to.

4.5. Jewelery

If you find yourself looting a lot of ingots, gems, rings then we can make these more valuable by turning them into jewelery. Check out this guide for what you need and how to do it. Then you can turn what are normally junk items into rings, which will get you more credits from the shops.

To make Jewelery you'll need quite a lot of Agility and Mechanical Engineering.

4.6. Monster Parts

With the use of a Bio-Comminutor (found in the Trade section of any general store) Monster Parts can be converted to Blood Plasma. These sell for substantially more than the monster parts to shops. This can be a nice way to make a bit of quick cash.

Converting Monster Parts to Blood Plasma will need a bit of Pharma Tech.

Check out the trade skill section later on to see what others you might want to do.

5. Skills

5.1. Essential Skills

This section covers the skills that are essential or useful to you as you progress as an engineer.

All skills are effected by something called 'dribble down' from the abilities, this means a small increase in that skill based on the ability. As well as this skills can only be raised a certain amount before the ability needs to be raised as well.

The most important skills for us are those that make our bots, without those we're not much good.

So at every step the following three are essential for us:

Intelligence - Effects all nano skills, most trade skills
Matter Creation - Bot Creation
Time and Space - Bot Creation
Computer Literacy - NCU, Belts & Grid
Matter Metamorphesis - Shields
Treatment - Med kits, nano kits, implants
Body Development - This increases your Hit points
Nano Pool - Increases your Nano points

5.2. Secondary Skills

Any other abilities you raise depend on your Breed choice. As Solitus I always suggest you raise Agility and Stamina (in addition to Intelligence).

There are two reasons for that, the first is that those two abilities also effect 'Matter Creation' and 'Time and Space'. The second reason is that it's very easy to make implants that depend on those skills.

Some of the other skills you might be interested in are below (Any weapon skills are covered in the next section):

Run Speed - Self explanetory
Biological Metamorphesis - The bot heals are listed here. All Bot heals are mission reward only.
Psychological Modification and Sensory Improvement - Raise to at least 66
Map Navigation - Optional, but if you raise to 80 you can have the important map upgrades (people & monsters) quickly. Otherwise use temporary implants to increase and upload maps as you level.

5.5. Buffs

5.5.1. General Buffs

The first buffs you should look at getting in the game are the 'Proficiency' nanos. You can find a list of Proficiency nanos here. It's highly advisable to get these as soon as you can.

After these you should look at getting the appropriate 'Expertise' nanos. You can find a list of Expertise nanos here. Again it is advisable to get these as soon as you can.

A few other general nanos to get as an Engineer are Regeneration which increases the rate your bot will heal, Healing which you can use to heal your bot a small amount and Pet Warp which warps your pet to you, useful if it ever gets stuck or lost.

5.5.2. Engineer Buffs

You can find a list of all Engineer nanos here.

This list contains all of the new nanos added to our profession with Shadowlands.

Each of these Knowledge Nanos adds 80 to trade skills as listed.

Each of these Mastery Nanos adds 125 to trade skills as listed.

One particular line to pay close attention to is our attack rating buffs for the bot. These are very good for increasing the amount of damage your bot does.
	
5.5.3. Where to get the Nanos

As with all professions a large amount of Nano Formulea are available in the shops, however after quality level 125 you have to find things for yourself.

All nanos available in the shops are also available as mission rewards. Most nanos above QL125 are available as mission rewards as well, except for those below.

Clicksaver is useful program to help you find the items / nanos you want without having to read the description for every single mission.

Please note that Funcom do not support Clicksaver in any way.

This list of Nanos are only available as outdoor boss loot.

This list can be found as mission rewards with a couple of notable exceptions.

    * Kamikaze bots do not exist in the game at all and the entry for those nanos should be ignored.
    * Beacon Warp is mission reward / drop only.

All the bot heals are available as mission rewards or mission loot but not in the shops.

All the Sympathetic and Disruptive nanos are available as mission rewards or mission loot but not in the shops.

The Trade skill Knowledge line are available in Schoel. QFT and EE Knowledge both in the Garden the rest in the Sanctuary.

The Trade skill Mastery line are available in Adonis and Penumbra. Weapon Smithing Mastery is in Penumbra Garden all the rest are in Adonis Sancutary.

Team Beacon Warp is a rare drop from any mob, or a common drop from Polymorphed Lunatic in Deep Artery Valley.

Shield of the Obedient Servant is a semi-regular drop of the Obediency Enforcer in Eastern Fouls Plains.

5.5.4. Meta Physicist Buffs

Teachings - Each of these programs adds 25 to one of your nano skills (List)
Masteries - Adds 50 to each nano skill (List)
Infuse with Knowledge - Adds 90 to each nano skill (List)
Mocham's Gift - Adds 140 to each nano skill (List)

5.5.5. Trader Buffs

Skill Wranglers - There are a lot of these buffs so a general description will have to do. Each version adds a certain amount to all nano skills and all weapon skills. (List)
Apprentice - Each of these programs adds 40 to one of your trade skills (List)
Journeyman - Adds 80 to each trade skill (List)
Maestro - Adds 125 to each trade skill (List)

5.6. Items

These are details of some items that add to 'Matter Creation' and 'Time and Space' which we can use to help us summon our bots.

    * Neleb's Nano-circuit Robe - Found in the Steps of Madness at 800 2800 in Greater Omni Forest.
    * Notum Focus - Drops off the Forefather in the Smuggler's Den.
    * Platinum Ring of the Three - Drops in the Temple of Three Winds.
    * Notum Ring of the Three - Drops in the Temple of Three Winds.
    * First Circle of the Inner Sanctum - Drops in the Inner Sanctum.
    * Second Circle of the Inner Sanctum - Drops in the Inner Sanctum.
    * Third Circle of the Inner Sanctum - Drops in the Inner Sanctum.
    * Black Cloak Hood - Drops from the named boss mobs at the temples in Penumbra.
    * Gaily Painted Hood - Drops from Tarasque in the Camelot dungeon (18 hour spawn)
    * Metal Armlet of the Quartet - Drops from Primus and Secundus Mobs in the Ace Camp or the Caves.
    * Sleeves of Azure Reveries - Drops from the Mercenaries at the Ace Camp.
    * Gloves of Azure Reveries - Drops from the Mercenaries at the Ace Camp.
    * Ebony Figurine - Drops from Primus and Secundus Mobs in the Ace Camp or the Caves. Very Rare Drop.
    * The Expensive Kevlar Vest of Professor Jones - Drops from Professor Van Horne. Very hard to get. Nanomage Only (18 hour spawn)
    * Pants of Participation - Drops from the Generals in Pertetual Wastes. Very hard to get. Clan only.
    * Threatening Trousers - Drops from the Generals in Pertetual Wastes. Very hard to get. Omni only.
    * Flower Guard Triplate Metal Helmet - Drops from Juggarnauts. Clan only.
    
5.7.5. Bugged Bots (these may be fixed)

Unfortunately not all bots obey the rules, as they have been sabotaged while leaving the factory. List is incomplete I'll add them as I learn about them.

    * Lesser Automaton - Should require 20 MC and TS to be within OE. Actual requirement is 27 MC and TS - Making this bot the holder of the coverted 'Most Bugged' award and it gets a big 'Do Not Use' sticker.
    * Lesser Gladiatorbot - Nano upload requirement is 258 MC and TS. Shell requirement is 261 MC and TS. Should require 207 MC and TS to be within OE. Actual requirement is 217 - 220 MC and TS.
    * Inferior Gladiatorbot - Nano Upload requirement is 278 MC and TS. Shell requirement is 277 MC and TS.
    * Common Gladiatorbot - Nano Upload requirement is 317 MC and TS, cast requirement is 318 MC and TS. Should require 254 MC and TS to be within OE. Actual requirement is 274 MC and TS.
    * Advanced Gladiatorbot - Should require 303 MC and TS to be within OE. Actual requirement is unknown (testing required).
    * Perfected Gladiatorbot - Should require 312 MC and TS to be within OE. Actual requirement is 316 MC and TS.
    * Lesser Guardbot - Should require 352 MC and TS to be within OE. Actual requirement is unknown (testing required).
    * Inferior Guardbot - Should require 362 MC and TS to be within OE. Actual requirement is unknown (testing required).
    * Guardbot - Should require 405 MC and TS to be within OE. Actual requirement is unknown (415+ requires testing).
    * Perfected Guardbot - Should require 449 MC and TS to be within OE. Actual requirement is unknown (458+ requires testing).
    * Patchwork Warbot - Should require 493 MC and TS to be within OE. Actual requirement is 514 MC and TS.
    * Lesser Warbot - Should require 507 MC and TS to be within OE. Actual requirement is 510+ MC and TS (resting required).
    * Common Warbot - Should require 545 MC and TS to be within OE. Actual requirement is unknown (testing required).
    * Military-Grade Warbot - Should require 578 MC and TS to be within OE. Actual requirement is 584 MC and TS.
    * Patchwork Warmachine - Should require 588 MC and TS to be within OE. Actual requirement is unknown (testing required).
    * Flawed Warmachine - Nano upload requirement is 751 MC and TS. Shell requirement is 753 MC and TS. Should require 601 MC and TS to be within OE. Actual requirement is 612 MC and TS.
    * Slayerdroid Protector - Should require 682 MC and TS to be within OE. Actual requirement is ~686 MC and TS.
	
7. Weapon Choices

As Engineers we get a quite few weapon choices, this section covers many of the options available to us.

7.2. Weapons for Fighting with

7.2.1. Pistol Based Weapons

A common choice for those of us that deal with Trade Skills, or want to deal with Trade Skills. There are a large amount of pistols used for buffing skills, having access to those can be very useful.

    * The Original Electronicum - A reasonable choice for a low to medium level Engineer. Available from the Backyard vendor in Jobe up to QL50. After that boss loot only.
    * Customized IMI Desert Reet - Drops from the Lab Director in the Omni Mine (Biomare Dungeon).
    * Eyemutant Orb Lasers - Low multi requirement, High minimum damage. Has to be made from the Eyemutant Eye if you can find one. Highest currently found is ~QL100.
    * Pain of Patricia - Level 175+ weapon that drops from Patricia at the Ace camp. Very good for Rubi Ka, but bad against Shadowlands Mobs.
    * Upgraded Solar-Powered Pistols - This one requires a lot of special items to get. You can find a guide for making the pistol here.
    * Flux Pistol - With exceptional minimum damage this weapon is ideal for use in Shadowlands. Drops from sided mobs at the temples in Shadowlands. For higher levels works very well when weilded alongside the Master Engineer Pistol.

7.2.2. Grenade Based Weapons

These are impressive weapons, the ones that make your team mates stop and wonder what on earth is going on. A popular choice for Engineers more inclined to fighting.

    * MTI Pocket Launcher - A low level weapon, only available up to QL50.
    * OT 12 Grenade Launcher - For those of us who like weapons that really do make a lot of noise this is an excellent choice. However, it is also boss loot only. Heavy damage, but slow.
    * Ballistic Launcher Pistol - Good minimum damage and uses a combination of Grenade and Pistol skills making use of our Extreme Predjudice buff. Generally drops from sided mobs in the Temples in Shadowlands.
    * OT Hurler - This weapon drops from outdoor bosses on Rubi Ka only. Quite rare, but reasonable damage on Rubi Ka. The low minimum damage makes this weapon less useful for Shadowlands.

7.2.3. Shotgun Based Weapons

Not as much of a common choice, but these weapons tend to be easy to get hold of.

    * Vektor ND Shotguns - These weapons are easy to get at lower levels and require only a single skill, which makes them a good choice for any new starter.
    * Home Defender - As with the Vektor, easy to get at lower levels and only require a single skill. These weapons can also be dual weilded if you choose.

7.2.4. Martial Arts

Even easier to get than the Shotguns above, MA can be a great choice for anyone just starting out alongside a substantial MA skill buff at higher levels.

    * Martial Arts - With the use of the linked nano Martial Arts can be a reasonable choice for a high level engineer. Martial Arts on it's own is also a very good option for a low level engineer since it doesn't conflict in any implant set up, and means you don't have to buy or find weapons.

7.3. Weapons for Improving your skills

None of the weapons in this section are here because of their damage. They all have one purpose: To help you get a bigger and better robot to hit things with.

    * Old English Trading Co. - A great pistol at lower levels, adds 5 to intelligence.
    * O.E.T. Co. Pelastio - Another pistol that adds to Intelligence, ranges from +10 to +20.
    * O.E.T. Co. Jess - Adds 20 to Psychic and 20 to Intelligence.
    * O.E.T. Co. Maharanee - Yet another, 25 to Intel and 25 to Psychic this time.
    * Krutt Assault 219 Waltzing Queen Special - Adds 24 Stamina, 24 Intelligence and 20 Computer Literacy. Rare mission drop.
    * Galahad Khan - Common high level (180 - 200) mission drop
    * Soft Pepper Pistol - Drops as a sealed weapon recepticle. Random chest item. Adds to Bio Met, Matter Met and Matter Creation.
    * Galahad Inc. T70 Tsuyoshi - Adds to 20 Matter Met and Bio Met.

7.4. Other skills

Inits (Initiatives) control the speed of attack and the speed of recharge for all standard weapons, so increasing Ranged Init increases ranged weapon fire rate, Melee Init for melee weapons (clubs, bats, swords) and Physical Init for barehanded attacks and some bows.

Nano Init effects Nano cast time is a similar way, however it does not change the recharge time in any way.

Dodge-Ranged improves your chances of avoiding hits by ranged weapons, it also reduces the chance of a critical hit.

Evade Close Combat as above but for close combat attacks such as Melee weapons and Martial arts.

Duck Explosions is generally required to avoid damage from shotguns and grenade launchers, particularly good if you're having trouble with trader mobs in missions.

If you never raise evades at all every mob that hits you will critical, so it's highly advisable to get all of them up to around twice the mobs level. So for Rubi Ka 400 in evades against a level 200 mob will generally save you from being hit by a critical every time.

Shadowlands requires higher evades than Rubi Ka since the mobs hit a lot harder. If you find yourself getting in trouble with Shadowlands mobs you might want to consider raising the evades higher.

It's well worth noting that Add All Def in your implant set (Level 100+ only) and high evades increase the effectiveness of our Area Blind aura.

Multi Ranged and Multi Melee are both the ability to hold and use two weapons as once. When you wear a ranged weapon the lowest of the two Multi requiremensts is used to figure out if you can wear it - both of these skills are very very expensive for us so normally left until high levels.

8. Trimmers & Android NCU Upgrades

This section covers the items you can use on your robot to enhance it's performance. Trimmers can be used as many times as you need and won't disapear on use. NCU Upgrades are single use only.

8.1. Permanent Effect Trimmers

When used these trimmers have a permenent effect on the robot, so you only need to use them once per bot.

8.1.1. Attack Speed Trimmer

This line of trimmers can be bought from general stores (remember that the stock in General Stores is random, so you might have to check a few times to find the highest Quality). The maximum Quality is 100 which represents either full defense or full offense.

    * Positive Trimmers - These increase the attack speed but decrease the bots ability to evade.
    * Negative Trimmers - These decrease the attack speed but increase the bots ability to evade.

This range of trimmers basically alters the position of your pets Aggressive-Defensive slider. The effect is much the same as altering your own, at full offense the bot hits faster but has less effective evades so gets hit more.

The majority of our bots are more than capable of taking a little punishment though so we tend to favour the Positive Trimmers over the Negative ones.

8.1.2. Taunting Trimmer

This trimmer is made using components that can be easily bought in the shops. The Quality ranges from 30 to 200 and will require Mech Eng and a small amount of Chemistry to build.

The components needed are:

    * XU-11 Serum - From Pharma Tech Components
    * Chemical Impact Injector - From Melee Weapon Components
    * Trimmer Casing - From Mechanical Engineering Components

Which will give you:

    * Trimmer - Increase Aggressiveness

This trimmer adds a relatively small taunt value to your pets hits. In effect each hit making the mob more angry at the robot. Used in conjunction with Trimmer - Positive Aggressive-Defensive this can make the pet very good at holding the mobs attention.

8.2. Temporary Effect Trimmers

These trimmers have a time limited effect on your robot. They aren't as important as the two trimmers mentioned above, but can be useful sometimes. Each can only be used once every 5 minutes. Each of the trimmers gives a minor skill increase to the skill they lock (for engineer at QL200 3 to Electrical Engineering).

    * Trimmer - Divert Energy to Avoidance - Increases your bots Evades at the expense of it's Maximum Health. The trimmer is currently bugged and increases Poison AC instead of Duck Explosions. It can be very useful for PVP as the damage the bot takes when you use it will break calms. Locks Electrical Engineering for 5 minutes.
    * Trimmer - Divert Energy to Defense - Increases the bots Armour Class at the expense of it's Attack Rating. Locks Mechanical Engineering.
    * Trimmer - Divert Energy to Hitpoints - Increases the bots Maximum Health at the expense of it's Defensive Ability. Locks Electrical Engineering for 5 minutes.
    * Trimmer - Divert Energy to Offense - Increases the bots Attack Rating at the expense of Armour Class. Locks Mechanical Engineering for 5 minutes.

8.3. NCU Upgrades

Android NCU Upgrades provide additional NCU space for your bot, 110 more NCU at QL200. These do not come without a price though, each upgrade lowers the Armour on your bot (400 AC with a QL200 upgrade).

For the most part these NCU Upgrades are unnecessary as the bots generally have enough room for you to run a reflect shield, damage shield, AC buff and an Attack Rating buff. However you may want a keep a few handy if you really need them, especially since they drop fairly regularly in missions.

The lower Quality NCU upgrades tend to be more useful than the higher level ones as you're unlikely to need 110 more NCU on a robot.

0. Soloing & Teaming

This is where it all comes together, in this section I'm going to give a few pointers and suggestions to make everything that we've done up to now work.

At this point I will assume that anyone reading this section is new to the game and operating with the normal engineer set up. So some of this section may not be relevant.

10.1. Terms & Concepts

Before I go ahead and start suggesting things to try I'll just quickly run through some of the terms and concepts we'll be going through.

    * Mobs - Short for Mobiles, can be any creature in the game not controlled by a player.
    * Hate List - A mobs list of enemies in 'Must Die!' order.
    * Aggro Slider - The Agg / Def Slider in the stats window.
    * Line of Sight (LOS) - Self explanetory
    * Calms - A nano program that stops a mob fighting and makes it unable to attack unless attacked first. (Orange effect around the target)
    * Mez - Short for Mesmerize, same as calm.
    * Root - A nano program used to fix a mob to the spot, this won't effect its combat abilities. (Light Blue effect around the target)
    * Snare - A nano program used to reduce the run speed of a mob. (Green effect around the target)
    * Blind - A nano program that reduces the attack rating of the target (Black Cloud effect around the head of the target)
    * Tank - The primary damage taker in any situation, a successful tank must be able to hold the mobs attention.
    * Puller - Person responsible for drawing mobs towards a team, or for the splitting of groups of mobs into bit sized chunks.
    * Taunt - A device, nano program or component of a nano program that increases mob hate towards the user.

10.2. Pets in Combat

Our robots are, in effect, controlled mobs. This means they are subject to many of the rules and behavioural patterns that govern regular mobs.

So like a mob your pet will have a Hate List.

Mobs or Players might be added to the Hate List because:

    * Old Command - You told your bot to attack a mob by mistake or changed your mind.
    * Mob / Player Attacks Bot - Something or someone attacked your bot.
    * Mob / Player Attacks You - Something or someone attacked you.

Which ever state the bot is in it will build a hate list, even if the bot is standing there in Wait mode it will add attackers to its list.

If the bot is in Guard mode it will immediately engage the first attacker, in a team situation that's any mob that attacks any member of the team. If in Attack mode it will engage the attacker as soon as it's current target is finished.

In any other mode it will not act on this list until you give it a command. Then, depending on the command, it will either start to work through it or disregard it entirely.

If you set your bot to attack mode it will then engage the target you select, followed by each mob on the Hate List.

You can clear a Hate List at any time by issuing the command '/pet wait'.

The following engineer should hopefully illustrate this:

You're in a team consisting of yourself, a Crat, a Doctor and an Enforcer.

We'll assume that the team is built up of bad and good players. No sterotypes intended, just as engineers. This is a very common situation.

Ahead of you is a big room with 2 mobs in it, and 11 mobs surrounding it all in their little rooms.

The Enforcer isn't so bright, he prefers the head on method of rushing into a room and hitting things. He has no regard at all for the mobs that are watching his little display, and indeed little regard for the wellfare of the rest of the team.

He firmly believes that the crat should calm them all and the doc should heal him. If they don't then the fault lies entirely with them.

Fortunately for the Enforcer both the Doc and the Crat are very good if a little annoyed at the play style of your Enforcer teammate.

So the fight commences, the Enforcer engages the first mob, you send your bot to attack it. Everything is going fine.

Then in the age old fashion the cry 'ADD!' rings out as 4 of the mobs from surrounding rooms attack a mixture of you, your bot, the Enforcer and the doc. Thankfully the crat manages to swiftly calm these.

During this your bot has started to build a Hate List. The first being the mob it's currently fighting, it won't switch until that mob is dead. It's also added onto it's hate list all the mobs that just joined the fight and are now calmed.

The fight ends, everyone is in a bad way and needs to recharge for a moment, but your bot has just moved right onto the next target!

Rewind...

You see the adds, wait until they're calmed, then you issue the Wait command to your bot. Then immediately after the attack command to the current target that everyone is fighting.

You just cleared your bots Hate List, it will finish it's current target and enter Guard mode until other commands are issued.

The fight ends and everyone recharges. Your bot waits patiently for you to issue another command. While the mobs stand around looking bored.

Controlling your bot effectively is very important, you generally cannot let it go on an uncontrolled rampage, it'll upset people in a team and may make soloing hard work.

10.3. Solo Play

Engineers are a good profession for this, the bots ability to tank make us able to solo missions well above our level. In effect it is essential that the Engineer and the Bot behave as a small team.

Every mob in the game uses Hate Lists to determine who should be attacked. The idea is to always be lower down on this list than the bot.

Reasons you might move up this list include:

    * Damage - If you do more damage than your bot or team members then you might find the mob starts to hit you instead. Unless you want this to happen adjust your 'Aggro Slider' so your defenses are favoured over your attacks.
    * Healing - Healing nano programs carry an indirect taunt value, healing a target near the mob, specifically a target the mob is attacking will move you up the 'Hate List'.
    * First Seen - If the mob sees you before anything else you will automatically be at the top of the 'Hate List'.

This does not mean that mobs will never randomly attack you, so a certain amount of luck and good judgement is involved.

Of course when we do this we have to be careful. At very low levels the bot won't be able to taunt the mobs, or hold the mobs attention very well on its own. In this situation you can use Line of Sight (LoS) to your advantage.

This especially applies when healing. If you remain out of Line of Sight (of the mob) and heal the bot it's unlikely that the mob will switch its attention to you.

This is why we advise the use of Trimmers, as well as anything you can do to make the bot hit harder or annoy the mob more.

A Damage Shield for engineer won't do very much extra damage, nor will it really shield you or the bot from much. But casting it on your bot will mean the mob is being annoyed slightly more each time it hits.

One of the hardest things to do is learning to seperate mobs. If two mobs are in the same room attacking one will immediately catch the attention of the other.

The prefered method to deal with this situation is by pulling one mob into another room. You do this by waiting in the mobs room until it flashes up an attacked by message. At which point you have to get to the first room as fast as you can!

Pulling with a reflect or a damage shield running is much harder, the only way to pull a single mob in that instance is by not letting the mob hit you at all. If the mob you're pulling takes any damage then it will be noticed by the second mob and the effort is wasted.

This is a risk, if your bot (or your team members later on) can't pull the aggro from you or heal you then it might be an idea to use the mission door.

As you level and your skills increase you may want to get the Blind line. Engineer blinds are the most powerful in the game, they reduce the attack rating of the mob to such an extent that they can be incapable of hitting you. All our blinds are in the form of a 20 second area pulse which effects all hostile targets in the area.

However, if the mob takes even a single point of damage the blind will be broken and the mob back up to full power. Our blinds are also very short range and contain a taunt, so use with care.

Along with these is a Snare line that pulses out from the bot (every 30 seconds). The snare contains a taunt which improves chances of the mob attacking the bot.

10.4. Team Play

Acting as part of a team can be very hard work at times, some teams are better than others.

As a new player to this game you'll discover many low level characters that are refered to as 'Twinks'. These characters are normally alternates of higher level characters which have been improved using the resources of that higher level player. Whether that is by getting the best possible implants in, the best possible armour on or any other method of making a character better than average.

A team of twinks isn't necessarily a good team though, any team needs to be able to work together and communicate. Approaching any scenario with a little care will make the play experience better.

This is where we come onto team roles.

Some teams won't bother and will just run through the mission killing everything, looting everything and just hoping that no one dies in the process.

Other teams will use strategies, which in theory at least, allow them to contend with greater challenges than the first type.

Some professions are better suited to certain jobs than others.

Traditionally Enforcers and Soldiers are tank professions, they have taunts and are equipped to take a great deal of damage.

Fixers can also tank sometimes, but because of their speed they can make very very good pullers.

If a team is willing to use these kinds of methods then they can very quickly and efficiently clear a mission full of creatures much higher level than themselves.

Be a little more careful in teams with some of our programs. The snare program the bot has can attract adds and break calms.

The Blind program and other offensive auras we have can sometimes (not all the time) break calms.

10.5. Offensive and Defensive Auras

One final thing I want to cover in this section is a bit more of a detailed description of our defensive and offensive auras.

These auras have two components, the main component (server) runs in your own NCU, this will pulse out every 20 seconds or so the other half of the program (client). The client is the important bit since that's the one that provides the modifiers. You will end up with server and client running in your NCU when you run it, all other members will only have the client side.

This list contains all our defensive auras, they range in effect from AC auras to Damage increasing auras. You'll only be able to run one at a time.

Which you use depends on the situation really, the reflect and damage shield auras are only useful if members of the team are using those kinds of shield. The AC aura is one of the most useful in my opinion.

Damage buff auras aren't used much but you might want to consider them, the highest adds 25 extra damage for a cost of 2 NCU.

Offensive auras can be very useful. We're the only profession that can debuff certain types of shields, and we possess the most powerful blinds in the game.

This list contains all our offensive auras. Going to look at these more closely than the defensive auras.

Please note that all our Offensive auras carry a chance to break calms or draw in extra mobs to the fight so use them with care.

10.5.1. Damage Shield Debuff

This line of auras reduces the damage you take from any damage shield. There are 5 nanos in this line.

I find these most useful when facing boss mobs with obscene damage shields. It does seem to have some problems landing on occasion, but boss mobs have pretty obscene nano resist.

However, teams can be very grateful when the 150 damage they've been taking per hit is substantially reduced.

Disruptive Field Negator -4 Damage
Disruptive Shielding Negator -14 Damage
Disruptive Barrier Negator -28 Damage
Disruptive Retaliatory Negator -44 Damage
Disruptive Retributive Negator -61 Damage

10.5.2. Reflect Shield Debuff

This line reduces the effectiveness of reflect shields, it reduces the percentage of damage reflected, which makes it very powerful against all reflect shield users.

This includes the Soldier program Total Mirror Shield and the NT program Nullity Sphere.

Disruptive Field Harmonics -15%
Disruptive Cocoon Harmonics -22%
Disruptive Phase Harmonics -36%
Null Space Disruptor -48%

10.5.3. Blind (Add All Off Debuff)

Finally the most powerful of our lines. This one has a negative Add All Off modifier. Add All Off directly effects Attack Rating. Attack Rating this is the number used to figure out whether something hits, misses or criticals. The blinds simply reduce the Attack Rating value by a very large amount.

The blinds can be used to completely shut down a mob making it unable to hit you, this doesn't stop it fighting, just makes it as dangerous as a common backyard leet.

In effect this line provides us with some basic crowd control, it's not as effective as a calm, but it does give us an option in a difficult situation.

They are short range and will break on a single point of damage, or if another offensive program lands on the target.

11. Trade Skills

11.1. Introduction to Trade Skills

This is probably one of your biggest choices. Engineers really excel in this field, but it's not an easy path. For a start there's a lot to learn, every trade skill process has different rules. But most importantly this will absorb all of your free IP all the way until well into Title Level 6.

As a result of the IP sink involved in trade skills it's next to impossible to keep more than a couple of these up before level 125.

You should also remember that there is no such thing as a Trade skill only engineer, you have a choice of pure Combat Engineer or a Hybrid Trade skill / Combat skill Engineer. This is because you can't level off trade skills, the only way to level is through combat.

A comprehensive list of items for trade skills has already been created and compiled by Caddock which can be found here.

11.2. Trade Skill Processes

Trade skill abreviations:

Mechanical Engineering - ME
Electrical Engineering - EE
Quatum Field Tech - QFT
Weapon Smithing - WS
Pharma Tech - PT
Nano Programming - NP
Computer Literacy - CL
Psychology - Psychol
Chemistry - Chem
Breaking & Entering - B&E
Quality Level - QL

    * Implants (Req. NP) - The most popular trade skill, all normal implants (not Jobe implants) require NP in varying amounts. The skill requirement for this process varies from implant to implant, but around 5 NP per QL is enough for most implants.
    * Nano Crystals (Req. NP/CL/EE/QFT/ME) - Guide.
    * Implant Disassembly (Req. B&E/NP) - This requires an Implant Disassembly Clinic. Only standard implants (not refined and not Jobe) can be stripped. Needs 4.75 B&E per QL and a small amount of Nano Programming.
    * Maussers (Req. WS/ME/B&E) - The main use for this skill is building fixers their Mausser Chemical Streamers. This process needs 4.2 WS per QL and 4 ME per QL for the last step. Guide
    * Stims & Emergency Treatment Labs (Req. PT) - For making Emergency Treatment Labratories at varying quality levels. Guide
    * Aggression Enhancer (Req. ME/WS) - The Aggression Enhancer and Aggression Multiplier can both be upgraded to the linked forms. Requires 4.5 ME and WS per QL. Both of these are done by combining 3 charges of Essence of Pure Jealousy with the standard versions of each item. You find Essence of Pure Jealousy on a mob called Jealousy in the Steps of Madness dungeon.
    * Virral Triumvirate Egg (Req. ME/EE) - This item is requires ME to complete most parts, EE is required for the completion of one step. You'll need to be able to get up to 1000 ME for this process. Guide
    * Augmented All-Matching Bow Tie (Req. Psychol) - Guide
    * Over-tuning Tank Armour (Req. QFT/B&E) - Guide
    * CAS & Barter Armour (Req. Chem/NP) - Guide
    * Metallic Mantis Armour (Req. Chem) - Also needs Shape Soft Armour. Guide
    * Carbonum & Junk Metal Armour (Req. ME/EE/Chem) - Very similar processes. Carbonum Guide Junkmetal Guide

11.3. Charging for Trade Skills

Whether you use them or not Elbo has produced a price list for all trade skill processes should you need a guide about what to charge people if you wish to.

11.4. Special Tools

There are some items in the game that can only be found in a limited number of places. Most of these aren't a problem as you can always get them later on. However, 5 of these items can only be found easily in Missions at relatively low level.

    * Robot Instruction Discs - These three can only be found as mission loot at the same levels as the discs.
    * Shape Armour - These two can only be found as boss loot (indoor or outdoor) on approximately level 60 bosses.

11.5. Maximum Skills for each Breed

If you're making a character specifically for Trade skills then your Breed choice is very important.

Below are the maximum, unaugmented trade skills for each Breed. Each one assumes you're level 200 and have maxed all abilities.

Nanomage

    * Mechanical Engineering - 721
    * Electrical Engineering - 721
    * Quantum FT - 727
    * Weapon Smithing - 721
    * Nano Programming - 727
    * Compter Literacy - 727
    * Chemistry - 719
    * Pharma Tech - 665
    * Psychology - 663
    * Tutoring - 726

Opifex

    * Mechanical Engineering - 725
    * Electrical Engineering - 722
    * Quantum FT - 713
    * Weapon Smithing - 715
    * Nano Programming - 715
    * Compter Literacy - 715
    * Chemistry - 717
    * Pharma Tech - 669
    * Psychology - 661
    * Tutoring - 717

Solitus

    * Mechanical Engineering - 719
    * Electrical Engineering - 719
    * Quantum FT - 719
    * Weapon Smithing - 718
    * Nano Programming - 719
    * Compter Literacy - 719
    * Chemistry - 719
    * Pharma Tech - 659
    * Psychology - 659
    * Tutoring - 719

Atrox

    * Mechanical Engineering - 709
    * Electrical Engineering - 711
    * Quantum FT - 699
    * Weapon Smithing - 713
    * Nano Programming - 699
    * Compter Literacy - 699
    * Chemistry - 713
    * Pharma Tech - 643
    * Psychology - 639
    * Tutoring - 699

11.6. Tutoring and Why It's Important

Tutoring is required to be able to use Tutoring Devices, these are items in the game that allow you to termporarily increase one of your own trade skills.

The devices start at Quality Level 1 and go all the way up to 200. Providing an increase of up to 50 points.

That is an increase of 0.25 skill per quality level, but we don't work in bits of numbers, only full numbers. So QL1-7 provide a skill increase of 1 point, QL8 - 12 an increase of 2 points, up until the increase of 50 points with a QL200 device.

Tutoring is absolutely essential for many high level trade skills, but it takes a long time to make it really useful. Especially since the highest quality devices need 1000 tutoring to use.

So while you might want tutoring eventually, it's probably little use to you before level 150.

13.2. Where to find parts for the Engineer Pistol

Most of this is taken from Lyricia's guide at the Tir School of Engineering

13.2.1. Solar-Powered Pistol

    * Auto Targetting Computer -IIR 4 -Ultra- - Shop item from Ranged Weapon Components.
    * Basic Calculator - Shop item from the Bookstore
    * Jandawit Cleanup Cluster - Shop item from Ranged Weapon Components.
    * Irreparable River MV - Ql1 - 20 version of the River MV, can be found in shops sometimes, or as mission loot or reward.

13.2.2. Solar-Powered Tinker Pistol

    * Jenson Personal Ore Extractor - Shop item in Mechanical and Electrical Engineering Tools.
    * Screwdriver - Shop item from ??
    * Robotic Self-Preservation System - Random drop off robotic mobs. Above ~QL30 are NoDrop.
    * Notum Chip / Fragment - Random drop off monster / human mobs, occasional chest loot.

13.2.3. Solar-Powered Mender Pistol

    * Technoscavenger Brain - Drops from Junk-bot and Buzzsaw Technoscavenger type robots. The area around the Trash King or Greater Tir County have these robot types.
    * Bio Analyzing Computer - Shop item from ??

13.2.4. Solar-Powered Mechanic Pistol

    * Electrical Engineering Tutoring Device - These can be bought from the Bookstore in shops, or found as mission loot and rewards.
    * Disposabal Electrical Toolset - Drops from the Ratcatcher bot at 800 1800 Greater Tir County (18 hour spawn). Also a rare drop from Jack the Leg-Chopper clones in Varmint Woods, which are 20 minute placeholder type spawns.

13.2.5. Solar-Powered Machinist Pistol

    * Field Quantum Physics All-Purpose Tool - Shop item from Mechanichal and Electrical Engineering Tools.
    * Hacker Tool - Shop item from Fixer Shop, normal shops or mission loot.
    * Notum Threaded Zodiacal Blanket - Drops from Medusa mobs.

13.2.6. Solar-Powered Engineer Pistol

    * Experimental Nanobot Classifying Computer - Drops from Maurader-class Slayerdroids. These can be found in Broken Shores missions if you're clan.
    * Mist Filled Jar - Mission chest loot.

13.2.7. Solar-Powered Master Engineer Pistol

    * Nano Programming Interface - Shop item from Tools.
    * Nano Formula Recompiler - Mission reward or Loot.
    * Lock Pick - Shop item from Tools.
    * NCU Memory - Mission reward or Loot.
    * Crystalized Medusa Queen Hippocampus - Common drop at Hollow Island from the 4th Brood Champion upwards.
 ";

$engineers_txt = bot::makeLink("Guide to Engineers", $engineers_txt); 
if($type == "msg") 
bot::send($engineers_txt, $sender); 
elseif($type == "all") 
bot::send($engineers_txt); 
else 
bot::send($engineers_txt, "guild"); 
?>