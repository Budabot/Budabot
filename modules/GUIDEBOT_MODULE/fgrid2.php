<? 
$fgrid2_txt = "<header>::::: Guide to Fixer Grid Part 2 :::::<end>\n\n"; 
$fgrid2_txt = "Fixer Grid Part 2 
Faction  All 
Approximate Level  100+ 
Author  Silirrion 
Main  This quest rewards a Fixer with the team version of their Fixer Grid Crystal, I don't need to tell you how popular that will make you! 


It's a degree harder then the first quest and also requires an against the clock rush round three NPCs in a simialr vein to the 'great NPC race' in the first quest. 


It is highly recommended (not necessary mind, but very, very helpful) to have 2 or 3 friends capable of mezzing mobs to help you out. The mobs you have to visit can agro you so having them mezzed helps greatly. You have an extremely limited amount of time, and if the mob aggros before you can open trade with it, it's all over. 

In addition the mobs are not static, and might even have been killed by another player before you get there. The three mobs that you will have to visit in the course of this quest are as follows: 

Cyborg Grid Jumper: 1709, 1196 Southern Artery Valley (on island with bridge) 
Cyborg Grid Hacker: 1495, 3167 Southern Foul Hills (on the top of a big rock) 
Cyborg Grid Fixer: 2740 1260 Southern Artery Valley (next to a waterfall) 

If you have help, send the mezzers over to the above mobs and get them mezzed. Then it's time to start the quest! 

Ready to Start? 

You have to be over level 100 before Sirroco will talk to you and give this quest. 

Ok, remember the Hackers card that Sirroco gave you at the end of quest one? Remember that he also said he might have some more work for you? Well it doesn't take a genius to figure out where to start the second part! 

Head over to Old Athens (211,217), and find Sirroco again, trade him the hackers card. He will begin spilling his mumbo jumbo, give the card back, and then ask if you are ready. Say 'YES'. 

Now he will act paranoid, talk about grid cyborgs, and stop talking, once he does so say 'I BELIEVE YOU.' 

That will convince him to continue to talk to you and he will launch into some more dialogue. 

Say 'I AM READY' and in a few short moments you will begin a fun run! A mission will be given and it;s time to start the run... 

Immediately cast Reckless Digitisation (might help to have a HoT on) and then exit the Grid at the closest least laggy grid terminal. Jump into the fixer grid head to the 7th floor, and take the middle Southern Artery Valley exit. The Cyborg Grid Jumper will be a few steps north. Trade him the crystal, quest music, and you will have now 9 minutes to get to the next grid point. Grid out again 

Now grid back to your favoured terminal. Once again, jump to the fixer grid, go back to the 7th floor and take the right most Southern Foul Hills exit. From where you come up in the elevator, this will be behind you on your left. Get in a plane or yalm (sorry to the yalmless, you really need it to get to the top of the big rock. Trade with the Hacker, you will now have 9 minutes to make it to the fixer 

The Hacker though calls in reinforcements, and you are surrounded by cyborgs very quickly get out of your yalm and grid out as fast as you can hopefully saving the calmer. 

Now back to your safe spot, jump back into the fixer grid go back to the middle Southern Artery Valley exit (7th floor), jump in your yalm and fly to the waterfall (2740, 1260). Give the grid fixer the last crystal, and you will get your team grid crystal as a reward. 

Now all kinds of chaos insues - three cyborg boss mobs spawn, unless you have serious back-up run away quickly with your new prize! I am unaware of anyone having tried to kill these mobs, and they appear to be on a timer as they do despawn. 
 ";

$fgrid2_txt = $this->makeLink("Guide to Fixer Grid Part 2 aka Team FGrid", $fgrid2_txt); 
if($type == "msg") 
$this->send($fgrid2_txt, $sender); 
elseif($type == "all") 
$this->send($fgrid2_txt); 
else 
$this->send($fgrid2_txt, "guild"); 
?>