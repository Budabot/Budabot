<?php
  /*
   * aypwip.php - A Social Worrrrrld Domination! Module
   *
   * Developed by Mastura (RK2/Rimor) from Shadow Ops
   * from Anarchy Online.
   * converted to Budabot by Tyrence (RK2)
   */

$aypwip = array(
	"Brain: It must be inordinately taxing to be such a boob.<br> Pinky:  You have no idea.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so, Brain, but where will we find a duck and a hose at this hour?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, but where will we find an open tattoo parlor at this time of night?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Wuh, I think so, Brain, but if we didn't have ears, we'd look like weasels.<br>",
	"Brain: Are you thinking what I'm thinking, Pinky?<br> Pinky: Uh... yeah, Brain, but where are we going to find rubber pants our size?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so, Brain, but balancing a family and a career ...  oooh, it's all too much for me.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Wuh, I think so, Brain, but isn't Regis Philbin already married?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so, Brain, but burlap chafes me so.<br>",
	"Brain: Are you pondering what I'm pondering, Pinky?<br> Pinky: Sure, Brain, but how are we going to find chaps our size?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so, Brain, but we'll never get a monkey to use dental floss.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so, Brain, but this time, you wear the tutu.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, Brain, but culottes have a tendency to ride up so.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, Brain, but if they called them sad meals, kids wouldn't buy them.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, Brain, but me and Pippi Longstocking... I mean, what would the children look like?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, Brain, but this time, you put the trousers on the chimp.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Well, I think so, Brain, but I can't memorize a whole opera in Yiddish.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but there's already a bug stuck in here from last time. (pointing between teeth)<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but I get all clammy inside the tent. <br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but I don't think Kay Ballard is in the union. <br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Yes I am.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but the Rockettes, it's mostly girls, isn't it?<br>",
	"Brain: Are you pondering what I'm pondering?<br> Pinky: I think so Brain, but pants with horizontal stripes make me look chubby.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but pantyhose are so uncomfortable in the summer time.<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but it's a miracle that this one grew back (holding left leg).<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so Brain, but you'd have to take that whole bridge apart then, wouldn't you?<br>",
	"Brain: We are France, and soon, we are the world!<br> Pinky: Are we the children Brain?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so Brain, but how are we gonna teach a goat to dance with flippers on?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but apply north to what?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so Brain, but Snowball for windows?<br>",
	"Brain: Are you pondering what I'm pondering Pinky?<br> Pinky: Uh, I think so Brain, no, no, it's too stupid. <br>",
	"Brain: We will disguise ourselves as a cow.<br> Pinky: Narf! That was it exactly!<br>",
	"Brain: Sancho Pinky, are you pondering what I'm pondering?<br> Pinky: Uh, I think so Don Cerebro, but why would Sophia Loren do a musical?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so, but what if the chicken won't wear the nylons?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: I think so Brain, but isn't that why they invented tube socks?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Well, I think so, Brain, but if Clark Kent wore contact lenses, wouldn't he look just like Superman?<br>",
	"Brain: Pinky, are you pondering what I'm pondering?<br> Pinky: Well, I think so Brain, but if Mulder wore the skirt, wouldn't Scully have to walk around naked?<br>",
);

if (preg_match("/^brain/i", $message)) {
	$randval = rand(1, sizeof($aypwip) - 1);
	$msg = $aypwip[$randval];
	$chatBot->send($msg, $sendto);
}

?>