<?php

if (preg_match("/^beer/i", $message)) {
	$beer = array();
	$beer[] = "*name* are you buying?";
	$beer[] = "Beer! lets get this party started *name*!!";
	$beer[] = "WTF!? ... why you trying to steel my beer!";
	$beer[] = "A round of beer coming up courtesy of *name*.";
	$beer[] = "/me sneaks up and smashes a bottle over *name*'s head doing *dmg* points of melee damage! w00t drunken PvP!";
	$beer[] = "Have one on the house *name* and tell me all your problems";
	$beer[] = "Sure, I would love to drink a few. Your place or mine?";
	$beer[] = "Import or Domestic hun?";
	$beer[] = "Sorry, I just ran out hun. Would you like some Rising Sun Sake instead?";
	$beer[] = "Umm, I dont think you are of age, lets see some ID.";
	$beer[] = "Well, by the looks of it. I think you have reached your limit for the night *name*.";
	$beer[] = "NO!! First pay off your bar tab. You still owe me *creds* credits *name*!";
	$beer[] = "YEAH! Let's start gathering for a Pub raid!!";
	$beer[] = "Only Leet's are drinking beer! We need harder stuff like Wodka!";
	$beer[] = "I am a firm believer in the people. If given the truth, they can be depended upon to meet any national crisis. The great point is to bring them the real facts, and beer. - Abraham Lincoln";
	$beer[] = "24 hours in a day, 24 beers in a case. Coincidence? - Stephen Wright";
	$beer[] = "One of the hallmarks of the baby boomer generation is that it doesn't live like the previous generation. It hasn't yet given up jeans and T-shirts or beer. - Ron Klugman, SVP, Coors Brewing";
	$beer[] = "Beer is proof that God loves us and wants us to be happy. - Benjamin Franklin";
	$beer[] = "The roots and herbs beaten and put into new ale or beer and daily drunk, cleareth, strengthen and quicken the sight of the eyes. - Nicholas Culpeper";
	$beer[] = "Without question, the greatest invention in the history of mankind is beer. Oh, I grant you the wheel was also a fine invention, but the wheel does not go nearly as well with pizza. - Dave Barry";
	$beer[] = "[I recommend]...bread, meat, vegetables and beer. - Sophocles' philosophy of a moderate diet";
	$beer[] = "Alright brain, I don't like you and you don't like me, so just get me through this exam so I can go back to killing you slowly with beer. - Homer Simpson";
	$beer[] = "Oh, lager beer! It makes good cheer, And proves the poor man's worth; It cools the body through and through, and regulates the health. - Anonymous";
	$beer[] = "Sometimes when I reflect back on all the beer I drink I feel ashamed. Then I look into the glass and think about the workers in the brewery and all of their hopes and dreams. If I didn't drink this beer, they might be out of work and their dreams would be shattered. Then I say to myself, 'It is better that I drink this beer and let their dreams come true than be selfish and worry about my liver.' - Jack Handy";
	$beer[] = "Not all chemicals are bad. Without chemicals such as hydrogen and oxygen, for example, there would be no way to make water, a vital ingredient in beer. - Dave Barry";
	$beer[] = "I would give all my fame for a pot of ale and safety. - Shakespeare, Henry V";
	$beer[] = "Make sure that the beer - four pints a week - goes to the troops under fire before any of the parties in the rear get a drop. - Winston Churchill to his Secretary of War, 1944";
	$beer[] = "We old folks have to find our cushions and pillows in our tankards. Strong beer is the milk of the old. - Martin Luther";
	$beer[] = "Beer will always have a definite role in the diet of an individual and can be considered a cog in the wheel of nutritional foods. - Bruce Carlton";
	$beer[] = "No soldier can fight unless he is properly fed on beef and beer. - John Churchill, First Duke of Marlborough";
	$beer[] = "An oppressive government is more to be feared than a tiger, or a beer. - Confucius";
	$beer[] = "If God had intended us to drink beer, He would have given us stomachs. - David Daye";
	$beer[] = "He was a wise man who invented beer. - Plato";
	$beer[] = "This is grain, which any fool can eat, but for which the Lord intended a more divine means of consumption... Beer! - Robin Hood, Prince of Thieves, Friar Tuck";
	$beer[] = "Beer: So much more than just a breakfast drink. - Whitstran Brewery sign";
	$beer[] = "I feel sorry for people who don't drink. When they wake up in the morning, that's as good as they're going to feel all day. - Frank Sinatra";
	$beer[] = "When I read about the evils of drinking, I gave up reading. - Henny Youngman";
	$beer[] = "When we drink, we get drunk. When we get drunk, we fall asleep. When we fall asleep, we commit no sin. When we commit no sin, we go to heaven. Sooooo, let's all get drunk and go to heaven! - Brian O'Rourke";
	$beer[] = "BEER: HELPING UGLY PEOPLE HAVE SEX SINCE 3000 B.C.! - Unknown";
	$beer[] = "To some it's a six-pack, to me it's a Support Group. Salvation in a can!";
	$beer[] = "The problem with the world is that everyone is a few drinks behind. - Humphrey Bogart";
	$beer[] = "Everybody has to believe in something.....I believe I'll have another drink. - W.C. Fields";
	$beer[] = "You're not drunk if you can lie on the floor without holding on. - Dean Martin";
	$beer[] = "God made pot. Man made beer. Who do you trust? - Restroom in The Irish Times, Washington DC";
	$beer[] = "You can't be a real country unless you have a beer and an airline - it helps if you have some kind of a football team, or some nuclear weapons, but at the very least you need a beer. - Frank Zappa";

	$dmg = rand(100,999);
    $cred = rand(10000,9999999);
	$msg = Util::rand_array_value($beer);
    $msg = str_replace("*name*", $sender, $msg);
    $msg = str_replace("*dmg*", $dmg, $msg);
    $msg = str_replace("*creds*", $cred, $msg);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
