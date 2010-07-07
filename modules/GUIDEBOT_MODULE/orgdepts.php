<? 
$orgdepts_txt = "<header>::::: Organizational Departments  :::::<end>\n\n"; 
$orgdepts_txt = "<font color = blue>-= Organization Departments =-</font>

Hello $sender    Last Updated 12-20-2007 

 A meeting was held with the generals and Unit commanders to set some policies for the org, and to establish some roles within the org.
 It was decided to establish an atttendance policy of sorts. If a person does not log in to Anarchy for 30 days, they will be removed from the org. Alts do count, but just please let someone know that you are online. This is to make sure that the org does not have players in it that nobody has seen for a long time. if you cannot be online for whatever reason for an extended period of time, just let a general know.

<font color = blue>Current department structure:</font>

<font color = green>President</font>
Plugsz
 Leader of the Newcomers Alliance
 Webmaster
 Bot Developer and Host

<font color = green>General Ferrell (plus alts)</font>
 Acquistions Dept.

<font color = green>General Ivengar (plus alts) </font>
 Recruiting Dept.


<font color = green>Position Open (Currently Handled by Plugsz and Ferrell</font>
 Guides and Assistance Dept.

<font color = green>General Thenidor (plus alts)</font>
 Events Dept.

<font color = green>Position Open</font>
Raids Dept.

<font color = green>Honorable Mentions, all in the former sense</font>

<font color = green>General Ncryption  Finance Dept.</font>

<font color = green>General Karkuss  Recruiting Dept.</font>
<font color = green>General Msphreak  Events Dept.</font>
<font color = green>President Emeritus Optikz</font>
 Former president of the organization, Founder of the Newcomers Alliance

If you have any questions about these departments, or would like to contribute to one, please let a General or Plugsz know. ";

$orgdepts_txt = bot::makeLink("Organizational Departments", $orgdepts_txt); 
if($type == "msg") 
bot::send($orgdepts_txt, $sender); 
elseif($type == "all") 
bot::send($orgdepts_txt); 
else 
bot::send($orgdepts_txt, "guild"); 
?>