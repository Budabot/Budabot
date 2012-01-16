<?php

   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows a random cybor message(Ported over from a bebot plugin written by Xenixa (RK1))
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 24.07.2006
   ** Date(last modified): 24.07.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
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
   
$cybor = array("I touch you on your lettuce, you massage my spinach... sexily.",
	"Don't worry about it.I'm wearing a lacy black bra.My soft breasts are rising and falling, as I breath harder and harder.",
	"My turnips listen for the soft cry of your love. My insides turn to celery as I unleash my warm and sticky cauliflower of love.",
	"I ride your buttocks like they were amber waves of grains.", 
	"We're in my bedroom.There's soft music playing on the stereo and candles on my dresser and night table.I'm looking up into your eyes, smiling. My hand works its way down to your crotch and begins to fondle your huge, swelling bulge.",
	"Noooo, i cant get my combined back on!",
	"Are you ready for my fresh produce?",
	"Are you ready to get nasty, baby? I'm as hot as a pizza oven",
	"Baby, I been havin a tough night so treat me nice aight?",
	"Don't worry about it. I'm wearing a lacy black bra. My soft breasts are rising and falling, as I breath harder and harder.",
	"hot damn am I excited now!",
	"haha, ok, u know that turns me on.",
	"I am completely inside of you. You are my puppet. I put on a little play.",
	"I am wearing a red silk blouse, a miniskirt and high heels. I work out every day, I'm toned and perfect. My measurements are 36-24-36. What do you look like?",
	"I can no longer resist the pizza. I open the box and unzip my pants with my other hand. As I penetrate the gooey cheese, I moan in ecstacy. The mushrooms and Italian sausage are rough, but the sauce is deliciously soothing. I blow my load in seconds. As you leave the bathroom.",
	"I cast Lvl. 3 Eroticism. You turn into a real beautiful woman.",
	"I execute standing position 12 from the Kama Sutra. Passion fills the room. Your head is close to the ceiling fan.",
	"I kiss you on the mouth, hard, but then gently.",
	"I kiss you softly on your chest.",
	"I lick your earlobe, and undo your watch.",
	"I make some toast and eat it off your ass. Land O' Lakes butter all in your crack. Mmmm.",
	"I meditate to regain my mana, before casting Lvl. 8 tool of the Infinite. I spend my mana reserves to cast Mighty Thrust of the Beyondness.",
	"I mouth the words to you, as if in slow motion: do me, do me.",
	"I put my hand through ur hair, and kiss u on the neck.",
	"I ride your buttocks like they were amber waves of grains.",
	"I slip out of my pants, just for you, bloodninja.",
	"I smack you thick booty.",
	"I stomp the ground, and snort, to alert you that you are in my breeding territory.",
	"I take off my trenchcoat, I'm nekked beneath, with pistols on my belt.",
	"I take yo pants off, grunting like a troll.",
	"I take your hand and kiss it softly.I'm reaching back undoing the clasp. The bra slides off my body. The air caresses my breasts. My nipples are erect for you.",
	"I touch you on your lettuce, you massage my spinach... sexily.",
	"I unzip my pants...",
	"I want you. Would you like to screw me?",
	"I was great. You loved it.",
	"I'm arching my back. Oh baby. I just want to feel your tongue all over me.",
	"I'm bending over the bed. Give it to me, baby!",
	"I'm fumbling with the clasp on your bra. I think it's stuck. Do you have any scissors?",
	"I'm moaning softly.",
	"I'm moving my buttocks back and forth, moaning. I can't stand it another second! Take me now!",
	"I'm on the bed arching for you.",
	"I'm pulling off your panties. My tongue is going all over, in and out nibbling on you... umm... wait a minute.",
	"I'm pulling up your shirt and kissing your chest.",
	"I'm taking hold of your blouse and sliding it off slowly.",
	"I'm throwing my head back in pleasure. The cool silk slides off my warm skin. I'm rubbing your bulge faster, pulling and rubbing.",
	"I'm touching your smooth butt. It feels so nice. I kiss your neck.",
	"I'm tuggin' off your pants. I'm moaning. I want you so badly.",
	"I've got a pubic hair caught in my throat. I'm choking.",
	"My turnips listen for the soft cry of your love. My insides turn to celery as I unleash my warm and sticky cauliflower of love.",
	"my zucchinis carresses your carrots.",
	"No, never mind. I'm getting dressed. I'm putting on my underwear. Now I'm putting on my wet nasty blouse. I'm buttoning my blouse. Now I'm putting on my shoes.",
	"Now I'm unbuttoning your blouse. My hands are trembling.",
	"Now my manhood won't get hard for a week.",
	"Oh	yeah, aight. Aight, I put on my robe and wizard hat.",
	"Ok baby, we got to hurry, I don't know how long I can keep it ready for you.",
	"OK, now I'm going to put my... you know... thing... in your... you know... woman's thing.",
	"OK. I'm pulling your sweat pants down and rubbing your hard tool.",
	"Oooohh yeah. I step out of the shower and I'm all wet and cold. Warm me up baby",
	"Slip out of those pants baby, yeah.",
	"So you ready to do IT then?  You unbutton my pants, spew your load at the sight of my underwear, and your spent.",
	"So you're really a 18 yr old girl right?",
	"Sweet, I start by rubbing your ass all around. You love this.",
	"Thats cool, so you wanna see my gun? I pull off my pants and show you my 'gun'.",
	"thats ok. ok i'm a japanese schoolgirl, what r u?",
	"Then send me the picture.",
	"To your light bulb I am the Thomas Edison of your sex. Withought my light you would be lost in a sea of darkness.",
	"You wanna cyber *name*? That'll be *creds* per min please.",
	"We are like two dancers, for whom the music never stops. I Kiss the top of your hand. You are taken aback by the bulge that forms pressed into your thigh.",
	"We're in my bedroom.There's soft music playing on the stereo and candles on my dresser and night table.I'm looking up into your eyes, smiling. My hand works its way down to your crotch and begins to fondle your huge, swelling bulge.",
	"Yeah, well I already unleashed my cauliflower, all over your olives, and up in your eyes. Now you can't see.",
	"Yes! Do it, baby! Do it!",
	"You bend over to harvest your radishes.",
	"You carress my ass, and trim my pubes...",
	"You sound sexy. I bet you want me in the back of your car...",
	"You start getting frisky so I put my hand down your undies.",
	"You're wet already. I can smell your womanhood from here.",
	"Your pants are off. I kiss you passionately-our naked bodies pressing each other.");
		
if (preg_match("/^cybor/i", $message)) {
    $cred = rand(10000,9999999);
	$msg = Util::rand_array_value($cybor);
    $msg = str_replace("*name*", $sender, $msg);
    $msg = str_replace("*creds*", $cred, $msg);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>