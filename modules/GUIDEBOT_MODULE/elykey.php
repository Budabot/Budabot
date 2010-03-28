<? 
$elykey_txt = "<header>::::: Elysium Shere Garden Key Quest :::::<end>\n\n"; 
$elykey_txt = "Elysium: Shere Garden Key Quest


Beginning this guide I'll assume you're in the Elysium Shere garden. Exit to Nero and head south along the rock wall cliff. You'll see a ramp in the temple area, go up it and you'll find Prophet Nar Shere (coords 922, 397) at the top. 

Shere Insignias needed: 7 to 8 

NOTE: It really helps to upload each new mission's coords to the map so you can find where to go more easily. 

1. Get quest from Prophet Nar Shere (922, 397) 

2. Then talk to Follower Man-Wox Shere who is standing right next to the right of the prophet if you're looking down. 

3. The 3rd one you need, Visionist Jorr-Dom Shere (900, 420) is standing at the beginning of the ramp you went up to see Prophet Nar Shere. 

4. Go back up the ramp and stand behind the Prophet Nar Shere, and you'll find the Hypnagogic Xum-Ixi Shere (926, 391) standing on a platform to the left. 
Hypnagogic Xum-Ixi Shere is a pain to talk to because he'll end the conversation if you choose the wrong option in the dialogue. 
******Dialogue As Follows:

Select the first option that says <font color = yellow>'I apologize for disturbing you, Xum-Ixi.'</font> 
Next, say <font color = yellow>'May I speak with you?' </font>
Tell him who you are with the first option <font color = yellow>'I am <name>'. </font>
Xum-Ixi will speak some more and tell him that <font color = yellow>'Prophet Nar Shere sent me.' </font>
Next say <font color = yellow>'You know what I need, why are we wasting time?' </font>
Next <font color = yellow>'There is still a need to learn from the past...' </font>
Then <font color = yellow>'...to maks sure we do not fail in our search...' </font>
And lastly Xum-Ixi will ask you what you search for, and say <font color = yellow>'Divinity.' </font>
He'll ask for the sealed letter, hand it to him and he'll speak some more then your quest is updated. 

5. Go back to the garden and exit at Stormshelter. Turn right and go to the front of the temple you came out at. Then on the right side of the temple on a platform you'll find the Follower Yutt-Ixi Shere (696, 559). 

6. Next you should talk to Visionist Dom-Xum Shere who is standing right next to the one you just previously talked to. 

7. Go back to the garden and exit to Remnans, talk to Fortuitous Jorr-Fes Shere who is close by. 

8. Go back to the garden and exit at The Fallen Forest. Follow the road to the south, then a bit south-east following the road, then when you see on the map where there is an intersection that is where you can find Ardent Pi Shere (927, 1481). I've found him standing here every time I've done this quest (3-4 times). 

9. Go back to the garden once your quest is updated and return the letter back to the Prophet Nar Shere at Nero. 

10. The Prophet Nar Shere will update your quest yet again and tell you to look for someone in the garden. 

11. Go back into the garden, and once in there go north into the maze of bushes almost to the back. Inside the maze you'll find Garboil Chi Shere (445, 520) who will take the letter and update your quest. Garboil Chi Shere will give you an Ancient Tracking Device that you are supposed to target each of the following Redeemed mobs that will be listed in your mission. 

12. The first one to target is Watcher Enel Ulma-Thar. Exit out of the garden at The Jagged Coast and go northwest to the Redeemed temple grounds you can see in the distance. Enter into the white 'hut' and you'll find Watcher Enel Ulma-Thar (540, 1778), select him and your mission will update. 

13 The next two mobs to target are Sipius Enel Lux-Mara and Devoted Enel Ilad-Ulma who are in the same white 'hut' with Watcher Enel Ulma-Thar. 

14. Go back to the garden the same way you came out previously and exit at Spade. Head northwest around the mountain then head north to coords (873, 1226). Select Enel Hume-yeol, Sipius Enel Gil-Gil, Diviner Enel Thar-Thar, and Devoted Enel Cama-Lux who are all near each other in the same area. 

15. Head back to the garden and exit to Shunpike and select Watcher Enel Mara-Cama which usually wanders on the bridge at (760, 936). 

16. Next select Devoted Enel Thar-Ilad who is slightly southeast at coords (819, 824). Your mission will update again and tell you to return to Garboil Chi Shere. 

17. Now head back to the garden and return the Ancient Tracking Device to Garboil Chi Shere and he'll give your coveted 'The Key to the Garden of Shere.' 
 ";

$elykey_txt = bot::makeLink("Elysium Shere Garden Key Quest", $elykey_txt); 
if($type == "msg") 
bot::send($elykey_txt, $sender); 
elseif($type == "all") 
bot::send($elykey_txt); 
else 
bot::send($elykey_txt, "guild"); 
?>