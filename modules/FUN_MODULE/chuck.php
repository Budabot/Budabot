<?php

   /*
   ** Author: Honge (RK2)
   ** Description: Shows a random chuck quote 
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created):2.6.08
   ** Date(last modified): 2.7.08
   ** 
   ** 
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
   
$chuck = array( 
    "Chuck Norris counted to infinity - twice.", 
    "Some kids piss their name in the snow. Chuck Norris can piss his name into concrete.",
    "Leading hand sanitizers claim they can kill 99.9 percent of germs. Chuck Norris can kill 100 percent of whatever the fuck he wants.",
    "Chuck Norris' calendar goes straight from March 31st to April 2nd; no one fools Chuck Norris.",
    "Chuck Norris' tears cure cancer. Too bad he has never cried.",
    "Chuck Norris does not sleep. He waits.",
    "Once, while having sex in a tractor-trailer, part of Chuck Norris' sperm escaped and got into the engine. We now know this truck as Optimus Prime.",
    "Chuck Norris can speak braille.",
    "Chuck Norris puts the laughter in manslaughter",
    "If you spell Chuck Norris wrong on Google it doesn't say, Did you mean Chuck Norris? It simply replies, Run while you still have the chance.",
    "Chuck Norris owns the greatest Poker Face of all-time. It helped him win the 1983 World Series of Poker despite him holding just a Joker, a Get out of Jail Free Monopoly card, a 2 of clubs, 7 of spades and a green #4 card from the game Uno.",
    "On a high school math test, Chuck Norris put down Violence as every one of the answers. He got an A+ on the test because Chuck Norris solves all his problems with Violence.",
    "Chuck Norris can do a wheelie on a unicycle.",
    "Chuck Norris once won a game of Connect Four in 3 moves.",
    "Chuck Norris can delete the Recycling Bin.",
    "Once a cobra bit Chuck Norris leg. After five days of excruciating pain, the cobra died.",
    "When the Boogeyman goes to sleep every night he checks his closet for Chuck Norris.",
    "Chuck Norris was originally cast as the main character in 24, but was replaced by the producers when he managed to kill every terrorist and save the day in 12 minutes and 37 seconds.",
    "Chuck Norris died ten years ago, but the Grim Reaper can't get up the courage to tell him.",
    "Chuck Norris does not hunt because the word hunting implies the possibility of failure. Chuck Norris goes killing.",
    "Chuck Norris can slam revolving doors.",
    "If it looks like chicken, tastes like chicken, and feels like chicken but Chuck Norris says its beef, then it's fucking beef.",
    "Superman owns a pair of Chuck Norris pajamas.",
    "Giraffes were created when Chuck Norris uppercutted a horse.",
    "Chuck Norris can have both feet on the ground and kick ass at the same time.",
    "Chuck Norris' house does not have security guards. Rather, he employs a single man in uniform to lead burglars to his bedroom, where they are never heard from again.",
    "Chuck Norris doesn't read books. He stares them down until he gets the information he wants.",
    "Chuck Norris sleeps with a night light. Not because Chuck Norris is afraid of the dark, but the dark is afraid of Chuck Norris",
    "Chuck Norris secretly sleeps with every woman in the world once a month. They bleed for a week as a result.",
    "When Chuck Norris gives you the finger, he's telling you how many seconds you have left to live.",
    "Chuck Norris sold his soul to the devil for his rugged good looks and unparalleled martial arts ability. Shortly after the transaction was finalized, Chuck roundhouse kicked the devil in the face and took his soul back. The devil, who appreciates irony, couldn't stay mad and admitted he should have seen it coming. They now play poker every second Wednesday of the month.",
    "If you play Led Zeppelin's Stairway to Heaven backwards, you will hear Chuck Norris banging your sister.",
    "Chuck Norris' dog is trained to pick up his own poop because Chuck Norris will not take shit from anyone.",
    "Chuck Norris can kill two stones with one bird.",
    "Chuck Norris was once on Celebrity Wheel of Fortune and was the first to spin. The next 29 minutes of the show consisted of everyone standing around awkwardly, waiting for the wheel to stop.",
    "Chuck Norris is always on top during sex because Chuck Norris never fucks up.",
    "Chuck Norris can play the violin with a piano",
    "Chuck Norris is the only person on the planet that can kick you in the back of the face.",
    "Chuck Norris doesn't have hair on his testicles, because hair does not grow on steel.",
    "Chuck Norris eats the core of an apple first.",
    "Ghosts are actually caused by Chuck Norris killing people faster than Death can process them.",
    "Bill Gates lives in constant fear that Chuck Norris' PC will crash.",
    "Chuck Norris doesn't pop his collar, his shirts just get erections when they touch his body.",
    "The best part of waking up is not Folgers in your cup, but knowing that Chuck Norris didn't kill you in your sleep.",
    "Death once had a near-Chuck-Norris experience.",
    "Chuck Norris never retreats, he just attacks in the opposite direction.",
    "Chuck Norris was once charged with three attempted murdered in Boulder County, but the Judge quickly dropped the charges because Chuck Norris does not attempt murder.",
    "Chuck Norris can strangle you with a cordless phone.",
    "The reason newborn babies cry is because they know they have just entered a world with Chuck Norris.",
    "Chuck Norris has to maintain a concealed weapon license in all 50 states in order to legally wear pants.",
    "Chuck Norris can build a snowman out of rain.",
    "Chuck Norris plays russian roulette with a fully loded revolver... and wins.",
    "M.C. Hammer learned the hard way that Chuck Norris can touch this.",
    "Chuck Norris is not hung like a horse... horses are hung like Chuck Norris",
    "Chuck Norris once punched a man in the soul.",
    "Mr. T once defeated Chuck Norris in a game of Tic-Tac-Toe. In retaliation, Chuck Norris invented racism.",
    "Chuck Norris likes to knit sweaters in his free time. And by knit, I mean kick, and by sweaters, I mean babies",
    "Chuck Norris is 1/8th Cherokee. This has nothing to do with ancestry, the man ate a fucking Jeep.",
    "Chuck Norris once had a heart attack; his heart lost.",
    "It is considered a great accomplishment to go down Niagara Falls in a wooden barrel. Chuck Norris can go up Niagara Falls in a cardboard box.",
    "When Chuck Norris looks in a mirror the mirror shatters, because not even glass is stupid enough to get in between Chuck Norris and Chuck Norris.",
    "When Chuck Norris enters a room, he doesn't turn the lights on, he turns the dark off.",
    "Chuck Norris can drown a fish.",
    "Jack was nimble, Jack was quick, but Jack still couldn't dodge Chuck Norris' roundhouse kick.",
    "The only time Chuck Norris was wrong was when he thought he had made a mistake.",
    "Chuck Norris doesn't need a miracle in order to split the ocean. He just walks in and the water gets the fuck out of the way.",
    "A Handicap parking sign does not signify that this spot is for handicapped people. It is actually in fact a warning, that the spot belongs to Chuck Norris and that you will be handicapped if you park there.",
    "Rosa Parks refused to get out of her seat because she was saving it for Chuck Norris.",
    "A rogue squirrel once challenged Chuck Norris to a nut hunt around the park. Before beginning, Chuck simply dropped his pants, instantly killing the squirrel and 3 small children. Chuck knows you can't find bigger, better nuts than that.",
    "Chuck Norris can make a paraplegic run for his life.",
    "Chuck Norris doesn't use pickup lines, he simply says, Now.",
    "Brett Favre can throw a football over 50 yards. Chuck Norris can throw Brett Favre even further.",
    "The chief export of Chuck Norris is pain.",
    "When God said, Let there be light, Chuck Norris said, say please.",
    "The last digit of pi is Chuck Norris. He is the end of all things.",
    "Chuck Norris can create a rock so heavy that even he can't lift it. And then he lifts it anyways, just to show you who the fuck Chuck Norris is.",
    "Chuck Norris is the only person that can punch a cyclops between the eye.",
    "Chuck Norris once bowled a 300. Without a ball. He wasn't even in a bowling alley.",
    "Chuck Norris can tie his shoes with his feet.",
    "The quickest way to a man's heart is with Chuck Norris's fist.",
    "Chuck Norris doesn't play hide-and-seek. He plays hide-and-pray-I-don't-find-you.",
    "If you can see Chuck Norris, he can see you. If you can't see Chuck Norris you may be only seconds away from death.",
    "Chuck Norris is currently suing NBC, claiming Law and Order are trademarked names for his left and right legs.",
    "The phrase, You are what you eat cannot be true based on the amount of pussy Chuck Norris eats.",
    "Chuck Norris once had an erection while lying face down and struck oil.",
    "Chuck Norris was originally offered the role as Frodo in Lord of the Rings. He declined because, Only a pussy would need three movies to destroy a piece of jewelery.",
    "Chuck Norris does not know where you live, but he knows where you will die.",
    "Chuck Norris is currently in a legal battle with the makers of Bubble Tape. Norris claims 6 Feet of Fun is actually the trademark for his penis.",
    "Bullets dodge Chuck Norris.",
    "When Chuck Norris goes cow-tipping, he lifts a cow up and drop kicks it into the neighboring farm. All the other cows simply tip themselves over to keep from having to walk back in the dark.",
    "Chuck Norris used to beat the shit out of his shadow because it was following to close. It now stands a safe 30 feet behind him.",
    "The saddest moment for a child is not when he learns Santa Claus isn't real, it's when he learns Chuck Norris is.",
    "Someone once tried to tell Chuck Norris that roundhouse kicks aren't the best way to kick someone. This has been recorded by historians as the worst mistake anyone has ever made.",
    "Before Chuck Norris was born, the martial arts weapons with two pieces of wood connected by a chain were called NunBarrys. No one ever did find out what happened to Barry.",
    "Pinatas were made in an attempt to get Chuck Norris to stop kicking the people of Mexico. Sadly this backfired, as all it has resulted in is Chuck Norris now looking for candy after he kicks his victims.",
    "Chuck Norris cannot predict the future; the future just better fucking do what Chuck Norris says.",
    "Most men are okay with their wives fantasizing about Chuck Norris during sex, because they are doing the same thing.",
    "Upon hearing that his good friend, Lance Armstrong, lost his testicles to cancer, Chuck Norris donated one of his to Lance. With just one of Chuck's nuts, Lance was able to win the Tour De France seven times. By the way, Chuck still has two testicles; either he was able to produce a new one simply by flexing, or he had three to begin with. No one knows for sure.",
    "We all know the magic word is please. As in the sentence, Please don't kill me. Too bad Chuck Norris doesn't believe in magic.",
    "Chuck Norris built a time machine and went back in time to stop the JFK assassination. As Oswald shot, Chuck Norris met all three bullets with his beard, deflecting them. JFK's head exploded out of sheer amazement.",
    "Chuck Norris has already been to Mars; that's why there are no signs of life there.",
    "They once made a Chuck Norris toilet paper, but it wouldn't take shit from anybody.",
    "A blind man once stepped on Chuck Norris' shoe. Chuck replied, Don't you know who I am? I'm Chuck Norris! The mere mention of his name cured this man blindness. Sadly the first, last, and only thing this man ever saw, was a fatal roundhouse delivered by Chuck Norris.",
    "There is no chin behind Chuck Norris' beard. There is only another fist.",
    "In fine print on the last page of the Guinness Book of World Records it notes that all world records are held by Chuck Norris, and those listed in the book are simply the closest anyone else has ever gotten.", 
    "The Great Wall of China was originally created to keep Chuck Norris out. It failed misserably.",
    "Crop circles are Chuck Norris' way of telling the world that sometimes corn needs to lie the fuck down.",
    "Chuck Norris once ate an entire ream of rice paper and shat out origami swans and Mister Miyagi from Karate Kid.",
    "Chuck Norris is ten feet tall, weighs two-tons, breathes fire, and could eat a hammer and take a shotgun blast standing. ",
    "The Four Horsemen of the Apocalypse actually live in Chuck Norris's nutsack.",
    "Chuck Norris put humpty dumpty back together again, only to roundhouse kick him in the face. Later Chuck dined on scrambled eggs with all the king's horses and all the king's men. The king himself could not attend for unspecified reasons. Coincidentally, the autopsoy revealed the cause of death to be a roundhouse kick to the face. There is only one King.",
    "When Chuck Norris played golf for money, chuck marked down a hole in 0 every time, a pro at the golf club, said to Chuck: excuse me sir, but you cant score zero on a hole. Chuck Norris turned towards the man and said, im Chuck Norris, the man then proceeded to pour gas over his body and set himself on fire because that would be less painful than getting roundhouse kicked by Chuck Norris, Chuck Norris roundhouse kicked him in the face anyways.",
    "Chuck Norris made Ellen Degeneres straight.",
    "Chuck Norris kicked Neo out of Zion , now Neo is The Two",
    "Chuck Norris' iPod came with a real charger instead of just a USB cord",
    "Chuck Norris knows where Carmen Sandiego is.",
    "Rudolph has a red nose because he got lippy and Chuck Norris roundhouse kicked him across the face several times",
    "China was once bordering the United States, until Chuck Norris roundhouse kicked it all the way through the Earth.",
    "Chuck Norris is what Willis was talking about",
    "If you have five dollars and Chuck Norris has five dollars, Chuck Norris has more money than you.",
    "When Chuck Norris had surgery, the anesthesia was applied to the doctors.",
    "Chuck Norris once broke the land speed record on a bicycle that was missing its chain and the back tire.",
    "Chuck Norris once kicked a baby elephant into puberty",
    "Multiple people have died from Chuck Norris giving them the finger.",
    "Chuck Norris once tried to wear glasses. The result was him seeing around the world to the point where he was looking at the back of his own head.",
    "Pee Wee Herman got arrested for masturbating in public. The same day, Chuck Norris got an award for masturbating in public.",
    "Once a grizzly bear threatened to eat Chuck Norris. Chuck showed the bear his fist and the bear proceeded to eat himself, because it would be the less painful way to die.",
    "If Chuck Norris is late, time better slow the fuck down",
    "Chuck Norris ordered a Big Mac at Burger King, and got one.",
    "Chuck Norris frequently donates blood to the Red Cross. Just never his own.",
    "There is no such thing as tornados. Chuck Norris just hates trailer parks",
    "Chuck Norris doesn't worry about changing his clock twice a year for daylight savings time. The sun rises and sets when Chuck tells it to.",
    "Chuck Norris was the fourth Wiseman. He brought baby Jesus the gift of \"beard\". Jesus wore it proudly to his dying day. The other Wisemen, jealous of Jesus' obvious gift favoritism, used their combined influence to have Chuck omitted from the Bible. Shortly after all three died of roundhouse kick related deaths. ",
    "If paper beats rock, rock beats scissors, and scissors beats paper, what beats all 3 at the same time? Answer: Chuck Norris. ",
    "Although it is not common knowledge, there are actually three sides to the Force: the light side, the dark side, and Chuck Norris.",
    "Scientists used to believe that diamond was the world's hardest substance. But then they met Chuck Norris, who gave them a roundhouse kick to the face so hard, and with so much heat and pressure, that the scientists turned into artificial Chuck Norris. ",
    "When Chuck Norris was denied a Bacon McMuffin at McDonalds because it was 10:35, he roundhouse kicked the store so hard it became a KFC.",
    "Chuck Norris drinks napalm to quell his heartburn.",
    "A duck's quack does not echo. Chuck Norris is solely responsible for this phenomenon. When asked why he will simply stare at you, grimly.",
    "Chuck Norris' roundhouse kick is so powerful, it can be seen from outer space by the naked eye. ",
    "Chuck Norris doesn't believe in Germany. ",
    "If you want a list of Chuck Norris' enemies, just check the extinct species list. ",
    "Chuck Norris has never blinked in his entire life. Never. ",
    "Chuck Norris eats transformer toys in vehicle mode and poos them out transformed into a robot. ",
    "Ironically, Chuck Norris' hidden talent is invisibility. ",
    "Chuck Norris invented water.",
    "Chuck Norris went looking for a bar but couldn't find one. He walked to a vacant lot and sat there. Sure enough within an hour an a half someone constructed a bar around him. He then ordered a shot, drank it, and then burned the place to the ground. Chuck Norris yelled over the roar of the flames, always leave things the way you found em.",
    "One time while sparring with Wolverine, Chuck Norris accidentally lost his left testicle. You might be familiar with it to this very day by its technical term: Jupiter. ",
    "Chuck Norris is Luke Skywalker's real father.",
    "Chuck Norris once visited the Virgin Islands. They are now The Islands. "); 

if(eregi("^chuck", $message)) {
	$randval = rand(1, sizeof($chuck) - 1);
	$msg = $chuck[$randval];
if($type == "guild")
	bot::send($msg, "guild");
elseif($type == "msg")
  bot::send($msg, $sender);
}
?>