<?php

$date = new DateTime();
$time = time() - $date->getOffset();
$time_format = "dS M, H:i";

$timezone["CST"]["name"] = "Central Standard Time (GMT-6)";
$timezone["CST"]["time"] = date($time_format, $time - (3600*6));
$timezone["CDT"]["name"] = "Central Daylight Time (GMT-5)";
$timezone["CDT"]["time"] = date($time_format, $time - (3600*5));
$timezone["MST"]["name"] = "Mountain Standard Time (GMT-7)";
$timezone["MST"]["time"] = date($time_format, $time - (3600*7));
$timezone["MDT"]["name"] = "Mountain Daylight Time (GMT-6)";
$timezone["MDT"]["time"] = date($time_format, $time - (3600*6));
$timezone["PST"]["name"] = "Pacific Standard Time (GMT-8)";
$timezone["PST"]["time"] = date($time_format, $time - (3600*8));
$timezone["PDT"]["name"] = "Pacific Daylight Time (GMT-7)";
$timezone["PDT"]["time"] = date($time_format, $time - (3600*7));
$timezone["AKST"]["name"] = "Alaska Standard Time (GMT-9)";
$timezone["AKST"]["time"] = date($time_format, $time - (3600*9));
$timezone["AKDT"]["name"] = "Alaska Daylight Time (GMT-8)";
$timezone["AKDT"]["time"] = date($time_format, $time - (3600*8));
$timezone["EST"]["name"] = "Eastern Standard Time (GMT-5)";
$timezone["EST"]["time"] = date($time_format, $time - (3600*5));
$timezone["EDT"]["name"] = "Eastern Daylight Time (GMT-4)";
$timezone["EDT"]["time"] = date($time_format, $time - (3600*4));
$timezone["NST"]["name"] = "Newfoundland Standard Time (GMT-3:30)";
$timezone["NST"]["time"] = date($time_format, $time - (3600*3.5));
$timezone["NDT"]["name"] = "Newfoundland Daylight Time (GMT-2:30)";
$timezone["NDT"]["time"] = date($time_format, $time - (3600*2.5));
$timezone["GMT"]["name"] = "Greenwich Mean Time (GMT / AO)";
$timezone["GMT"]["time"] = date($time_format, $time);
$timezone["CET"]["name"] = "Central European Time (GMT+1)";
$timezone["CET"]["time"] = date($time_format, $time + 3600);
$timezone["CEST"]["name"] = "Central European Summer Time (GMT+2)";
$timezone["CEST"]["time"] = date($time_format, $time + (3600*2));
$timezone["EET"]["name"] = "Eastern European Time (GMT+2)";
$timezone["EET"]["time"] = date($time_format, $time + (3600*2));
$timezone["EEST"]["name"] = "Eastern European Summer Time (GMT+3)";
$timezone["EEST"]["time"] = date($time_format, $time + (3600*3));
$timezone["EEDT"]["name"] = "Eastern European Daylight Time (GMT+3)";
$timezone["EEDT"]["time"] = date($time_format, $time + (3600*3));
$timezone["MSK"]["name"] = "Moscow Time (GMT+3)";
$timezone["MSK"]["time"] = date($time_format, $time + (3600*3));
$timezone["MSD"]["name"] = "Moscow Daylight Time (GMT+4)";
$timezone["MSD"]["time"] = date($time_format, $time + (3600*4));
$timezone["IRT"]["name"] = "Iran Time (GMT+3:30)";
$timezone["IRT"]["time"] = date($time_format, $time + (3600*3.5));
$timezone["IST"]["name"] = "Indian Standard Time (GMT+5:30)";
$timezone["IST"]["time"] = date($time_format, $time + (3600*5.5));
$timezone["ICT"]["name"] = "Indochina Time (GMT+7)";
$timezone["ICT"]["time"] = date($time_format, $time + (3600*7));
$timezone["CCST"]["name"] = "China Standard Time (GMT+8)";
$timezone["CCST"]["time"] = date($time_format, $time + (3600*8));
$timezone["JST"]["name"] = "Japan Standard Time (GMT+9)";
$timezone["JST"]["time"] = date($time_format, $time + (3600*9));
$timezone["ACST"]["name"] = "Australian Central Standard Time (GMT+9:30)";
$timezone["ACST"]["time"] = date($time_format, $time + (3600*9.5));
$timezone["ACDT"]["name"] = "Australian Central Daylight Time (GMT+10:30)";
$timezone["ACDT"]["time"] = date($time_format, $time + (3600*10.5));
$timezone["AEST"]["name"] = "Australian Eastern Standard Time (GMT+10)";
$timezone["AEST"]["time"] = date($time_format, $time + (3600*10));
$timezone["AEDT"]["name"] = "Australian Eastern Daylight Time (GMT+11)";
$timezone["AEDT"]["time"] = date($time_format, $time + (3600*11));


if (preg_match("/^time$/i", $message)) {
	$link = "The following includes most of the timezones that exists but notice that this list doesn't show all countrys within the timezones and also that some country's have 2 timezones. \nTo see the time in a special timezone use time 'timezone' for example time CET\n\n";
	$link .= "<u>Australia</u>\n";
	$link .= "<tab><highlight>Northern Territory/South Australia<end>\n";
	$link .= "<tab><tab>Standard Time (ACST = GMT+9:30): {$timezone["ACST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (ACDT = GMT+10:30): {$timezone["ACDT"]["time"]}\n";
	$link .= "<tab><highlight>Quensland/Victory/Tasmanien<end>\n";
	$link .= "<tab><tab>Standard Time (AEST = GMT+10): {$timezone["AEST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (AEDT = GMT+11): {$timezone["AEDT"]["time"]}\n\n";

	$link .= "<u>Asia</u>\n";
	$link .= "<tab><highlight>Thailand/Vietnam/Kambodscha (ICT = GMT+7)<end>: {$timezone["ICT"]["time"]}\n";
	$link .= "<tab><highlight>China/Malaysia/Singapur/Indonesien (CST = GMT+8)<end>: {$timezone["CCST"]["time"]}\n";
	$link .= "<tab><highlight>Japan/Korea (JST = GMT+9)<end>: {$timezone["JST"]["time"]}\n\n";

	$link .= "<u>Europe</u>\n";
	$link .= "<tab><highlight>England (GMT)<end>: {$timezone["GMT"]["time"]}\n";
	$link .= "<tab><highlight>Germany/France/Netherlands/Italy/Austria<end>\n";
	$link .= "<tab><tab>Standard Time (CET = GMT+1): {$timezone["CET"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (CEST = GMT+2): {$timezone["CEST"]["time"]}\n";
	$link .= "<tab><highlight>Ägypten/Bulgarien/Finnland/Griechenland<end>\n";
	$link .= "<tab><tab>Standard Time (EET = GMT+2): {$timezone["EET"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (EEST/EEDT = GMT+3): {$timezone["EEST"]["time"]}\n";
	$link .= "<tab><highlight>Bahrain/Irak/Russland/Saudi Arabien<end>\n";
	$link .= "<tab><tab>Standard Time (MSK = GMT+3): {$timezone["MSK"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (MSD = GMT+4): {$timezone["MSD"]["time"]}\n\n";
	$link .= "<highlight>Indien (GMT+5:30)<end>: {$timezone["IST"]["time"]}\n\n";
	$link .= "<highlight>Iran (GMT+3:30)<end>: {$timezone["IRT"]["time"]}\n\n";

	$link .= "<u>Canada</u>\n";
	$link .= "<tab>Standard Time (NST = GMT-3:30): {$timezone["NST"]["time"]}\n";
	$link .= "<tab>Summer Time (NDT = GMT-2:30): {$timezone["NDT"]["time"]}\n\n";

	$link .= "<u>USA</u>\n";
	$link .= "<tab><highlight>Florida/Indiana/New York/Maine/New Jersey/Washington D.C.<end>\n";
	$link .= "<tab><tab>Standard Time (EST = GMT-5): {$timezone["EST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (EDT = GMT-4): {$timezone["EDT"]["time"]}\n";
	$link .= "<tab><highlight>Alaska<end>\n";
	$link .= "<tab><tab>Standard Time (AKST = GMT-9): {$timezone["AKST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (AKDT = GMT-8): {$timezone["AKDT"]["time"]}\n";
	$link .= "<tab><highlight>California/Nevada/Washington<end>\n";
	$link .= "<tab><tab>Standard Time (PST = GMT-8): {$timezone["PST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (PDT = GMT-7): {$timezone["PDT"]["time"]}\n";
	$link .= "<tab><highlight>Colorado/Montana/New Mexico/Utah<end>\n";
	$link .= "<tab><tab>Standard Time (MST = GMT-7): {$timezone["MST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (MDT = GMT-6): {$timezone["MDT"]["time"]}\n";
	$link .= "<tab><highlight>Alabama/Illinois/Iowa/Michigan/Minnesota/Oklahoma<end>\n";
	$link .= "<tab><tab>Standard Time (CST = GMT-6): {$timezone["CST"]["time"]}\n";
	$link .= "<tab><tab>Summer Time (CDT = GMT-5): {$timezone["CDT"]["time"]}\n";

	$msg = "<highlight>".date(Util::DATETIME, time())."<end>";
	$msg .= " ".Text::make_blob("All Timezones", $link);
    $sendto->reply($msg);
} else if (preg_match("/^time (.+)$/i", $message, $arr)) {
	$zone = strtoupper($arr[1]);
	if ($timezone[$zone]["name"]) {
		$msg = $timezone[$zone]["name"]." is <highlight>".$timezone[$zone]["time"]."<end>";
	} else {
		$msg = "This timezone doesn't exist or isn't known by this bot.";
	}

    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
