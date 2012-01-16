<?php

   /*
   ** Developed by Sicarius Legion of Amra, a Age of Conan Guild on the Hyrkania server
   ** Converted to Budabot by Tyrence (RK2)
   */
   
$dwight = array(
	"I am faster than 80% of all snakes.",
	"Reject a woman, and she will never let it go. One of the many defects of their kind. Also, weak arms.",
	"The eyes are the groin of the head.",
	"My feelings regenerate at twice the speed of a normal man.",
	"Before I do anything I ask myself \"Would an idiot do that?\" And if the answer is yes, I do not do that thing.",
	"Dolphins get a lot of good publicity for the drowning swimmers they push back to shore, but what you don't hear about is the many people they push farther out to sea.",
	"There are 3 things you never turn your back on: bears, men you have wronged and dominant male turkey during mating season.",
	"How would I describe myself? Three words: hard working, alpha male, jackhammer; merciless; insatiable;",
	"I am fast. To give you a reference point I am somewhere between a snake and a mongoose; and a panther.",
	"I grew up on a farm. I have seen animals having sex in every position imaginable. Goat on chicken, chicken on goat, couple of chickens doing a goat, couple of pigs watching.",
	"My maternal grandfather was the toughest guy I ever knew. World War Two veteran, killed twenty men and spent the rest of the war in an Allied prison camp.",
	"With the electricity we are using to keep Meredith alive we could power a small fan for two days. You tell me what's unethical.",
	"In the wild, there is no healthcare. Healthcare is \"Oh, I broke my leg!\" A lion comes and eats you, your dead.",
	"In an ideal world I would have all ten fingers on my left hand and the right one would just be left for punching.",
	"I never smile if I can help it; Showing one's teeth is a submission signal in primates. When someone smiles at me, all I see is a chimpanzee begging for its life.",
	"I train my major blood vessels to retract into my body on command. Also, I can retract my penis up into itself.",
	"Women are like wolves. If you want one you must trap it. Snare it. Tame it. Feed it.",
	"I don't have a lot of experience with vampires, but I have hunted werewolves. I shot one once, but by the time I got to it, it had turned back into my neighbor's dog.",
	"As of this morning, we are completely wireless here on Schrute Farms. So as soon as I find out where Mose hid all the wires, we can have power back on.",
	"Listen up kid! I don't like you. But because some town in Switzerland says so, you have rights.",
	"It appears that the website has become alive. This happens to computers and robots sometimes.",
	"No, don't call me a hero. Do you know who the real heroes are? The guys who wake up every morning and go into their normal jobs, and get a distress call from the commissioner, and take off their glasses and change into capes, and fly around fighting crime. Those are the real heroes.",
	"I saw \"Wedding Crashers\" accidentally. I bought a ticket for \"Grizzly Man\" and went into the wrong theater. After an hour, I figured I was in the wrong theater, but I kept waiting. Cause that's the thing about bear attacks, they come when you least expect it.",
	"The Schrutes have their own traditions. We usually marry standing in our own graves. Makes the funerals very romantic, but the weddings are a bleak affair.",
	"I overslept. Damn rooster didn't crow.",
	"Here's my card. It's got my cell number, my pager number, my home number, and my other pager number. I never take vacations, I never get sick, and I don't celebrate any major holidays.",
	"[Bringing in a dead goose] I accidentally ran over it. It's a Christmas miracle!",
	"I know everything about film. I've seen over 240 of them.",
	"When you are ready to see the sales office, the sales office will present itself to you.",
	"Just as you have planted your seed into the ground, I will plant my seed into you.",
	"Michael always says \"K-I-S-S. Keep it simple, stupid.\" Great advice. Hurts my feelings every time.",
	"[About the tux he's wearing] It belonged to my grandfather. He was buried in it, so... family heirloom.",
	"Oscar went to Mexico when he was five to attend his great-grandmother's funeral. What does that mean to an United States law enforcement officer ? He's a potential drug mule.",
	"I was the youngest pilot in Pan Am history. When I was four, the pilot let me ride in the cockpit and fly the plane with him. And I was four, and I was great and I would have landed it, but my dad wanted us to go back to our seats.",
	"My grandfather left me a 60-acre working beet farm. I run it with my cousin Mose. We sell beets to the local stores and restaurants. It's a nice little farm... Sometimes teenagers use it for sex.",
	"I live in a 9-bedroom farmhouse. I have my own crossbow range. It's the perfect situation for me. Although the two bathrooms would have been nice. We just have the one... and it's under the porch.",
	"The purse girl hits everything on my checklist: creamy skin, straight teeth, curly hair, amazing breasts. Not for me, for my children. The Schrutes produce very thirsty babies.",
	"Through concentration, I can raise and lower my cholesterol at will."
);

if (preg_match("/^dwight/i", $message)) {
	$sendto->reply(Util::rand_array_value($dwight));
} else {
	$syntax_error = true;
}

?>