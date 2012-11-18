<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'time', 
 *		accessLevel = 'all', 
 *		description = 'Show the time in the different timezones', 
 *		help        = 'time.txt'
 *	)
 */
class TimeController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $util;

	/** @Inject */
	public $text;

	/**
	 * @HandlesCommand("time")
	 * @Matches("/^time$/i")
	 */
	public function timeListCommand($message, $channel, $sender, $sendto, $args) {
		$link = "The following includes most of the timezones that exist but notice that this list doesn't show all countries within the timezones and also that some countries have 2 timezones.\n\nTo see the time in a specific timezone use <symbol>time 'timezone'.\n\nFor example <symbol>time CET\n\n";
		$link .= "<header2>Australia<end>\n";
		$link .= "<tab><highlight>Northern Territory/South Australia<end>\n";
		$link .= "<tab><tab>Standard Time (ACST = GMT+9:30): " . $this->getTimezone("ACST")->time . "\n";
		$link .= "<tab><tab>Summer Time (ACDT = GMT+10:30): " . $this->getTimezone("ACDT")->time . "\n";
		$link .= "<tab><highlight>Quensland/Victory/Tasmanien<end>\n";
		$link .= "<tab><tab>Standard Time (AEST = GMT+10): " . $this->getTimezone("AEST")->time . "\n";
		$link .= "<tab><tab>Summer Time (AEDT = GMT+11): " . $this->getTimezone("AEDT")->time . "\n\n";

		$link .= "<header2>Asia<end>\n";
		$link .= "<tab><highlight>Thailand/Vietnam/Kambodscha (ICT = GMT+7)<end>: " . $this->getTimezone("ICT")->time . "\n";
		$link .= "<tab><highlight>China/Malaysia/Singapur/Indonesien (CST = GMT+8)<end>: " . $this->getTimezone("CCST")->time . "\n";
		$link .= "<tab><highlight>Japan/Korea (JST = GMT+9)<end>: " . $this->getTimezone("JST")->time . "\n\n";

		$link .= "<header2>Europe<end>\n";
		$link .= "<tab><highlight>England (GMT)<end>: " . $this->getTimezone("GMT")->time . "\n";
		$link .= "<tab><highlight>Germany/France/Netherlands/Italy/Austria<end>\n";
		$link .= "<tab><tab>Standard Time (CET = GMT+1): " . $this->getTimezone("CET")->time . "\n";
		$link .= "<tab><tab>Summer Time (CEST = GMT+2): " . $this->getTimezone("CEST")->time . "\n";
		$link .= "<tab><highlight>Ägypten/Bulgarien/Finnland/Griechenland<end>\n";
		$link .= "<tab><tab>Standard Time (EET = GMT+2): " . $this->getTimezone("EET")->time . "\n";
		$link .= "<tab><tab>Summer Time (EEST/EEDT = GMT+3): " . $this->getTimezone("EEST")->time . "\n";
		$link .= "<tab><highlight>Bahrain/Irak/Russland/Saudi Arabien<end>\n";
		$link .= "<tab><tab>Standard Time (MSK = GMT+3): " . $this->getTimezone("MSK")->time . "\n";
		$link .= "<tab><tab>Summer Time (MSD = GMT+4): " . $this->getTimezone("MSD")->time . "\n\n";
		$link .= "<highlight>Indien (GMT+5:30)<end>: " . $this->getTimezone("IST")->time . "\n\n";
		$link .= "<highlight>Iran (GMT+3:30)<end>: " . $this->getTimezone("IRT")->time . "\n\n";

		$link .= "<header2>Canada<end>\n";
		$link .= "<tab>Standard Time (NST = GMT-3:30): " . $this->getTimezone("NST")->time . "\n";
		$link .= "<tab>Summer Time (NDT = GMT-2:30): " . $this->getTimezone("NDT")->time . "\n\n";

		$link .= "<header2>USA<end>\n";
		$link .= "<tab><highlight>Florida/Indiana/New York/Maine/New Jersey/Washington D.C.<end>\n";
		$link .= "<tab><tab>Standard Time (EST = GMT-5): " . $this->getTimezone("EST")->time . "\n";
		$link .= "<tab><tab>Summer Time (EDT = GMT-4): " . $this->getTimezone("EDT")->time . "\n";
		$link .= "<tab><highlight>Alaska<end>\n";
		$link .= "<tab><tab>Standard Time (AKST = GMT-9): " . $this->getTimezone("AKST")->time . "\n";
		$link .= "<tab><tab>Summer Time (AKDT = GMT-8): " . $this->getTimezone("AKDT")->time . "\n";
		$link .= "<tab><highlight>California/Nevada/Washington<end>\n";
		$link .= "<tab><tab>Standard Time (PST = GMT-8): " . $this->getTimezone("PST")->time . "\n";
		$link .= "<tab><tab>Summer Time (PDT = GMT-7): " . $this->getTimezone("PDT")->time . "\n";
		$link .= "<tab><highlight>Colorado/Montana/New Mexico/Utah<end>\n";
		$link .= "<tab><tab>Standard Time (MST = GMT-7): " . $this->getTimezone("MST")->time . "\n";
		$link .= "<tab><tab>Summer Time (MDT = GMT-6): " . $this->getTimezone("MDT")->time . "\n";
		$link .= "<tab><highlight>Alabama/Illinois/Iowa/Michigan/Minnesota/Oklahoma<end>\n";
		$link .= "<tab><tab>Standard Time (CST = GMT-6): " . $this->getTimezone("CST")->time . "\n";
		$link .= "<tab><tab>Summer Time (CDT = GMT-5): " . $this->getTimezone("CDT")->time . "\n";

		$msg = "<highlight>".$this->util->date(time())."<end>";
		$msg .= " " . $this->text->make_blob("All Timezones", $link);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("time")
	 * @Matches("/^time (.+)$/i")
	 */
	public function timeShowCommand($message, $channel, $sender, $sendto, $args) {
		$zone = strtoupper($args[1]);
		$timezone = $this->getTimezone($zone);
		if ($timezone !== null) {
			$msg = $timezone->name." is <highlight>".$timezone->time."<end>";
		} else {
			$msg = "Unknown timezone.";
		}

		$sendto->reply($msg);
	}
	
	public function getTimezone($tz) {
		$date = new DateTime();
		$time = time() - $date->getOffset();
		$time_format = "dS M, H:i";

		switch ($tz) {
			case "CST":
				$name = "Central Standard Time (GMT-6)";
				$offset = -(3600*6);
				break;
			case "CDT":
				$name = "Central Daylight Time (GMT-5)";
				$offset = -(3600*5);
				break;
			case "MST":
				$name = "Mountain Standard Time (GMT-7)";
				$offset = -(3600*7);
				break;
			case "MDT":
				$name = "Mountain Daylight Time (GMT-6)";
				$offset = -(3600*6);
				break;
			case "PST":
				$name = "Pacific Standard Time (GMT-8)";
				$offset = -(3600*8);
				break;
			case "PDT":
				$name = "Pacific Daylight Time (GMT-7)";
				$offset = -(3600*7);
				break;
			case "AKST":
				$name = "Alaska Standard Time (GMT-9)";
				$offset = -(3600*9);
				break;
			case "AKDT":
				$name = "Alaska Daylight Time (GMT-8)";
				$offset = -(3600*8);
				break;
			case "EST":
				$name = "Eastern Standard Time (GMT-5)";
				$offset = -(3600*5);
				break;
			case "EDT":
				$name = "Eastern Daylight Time (GMT-4)";
				$offset = -(3600*4);
				break;
			case "NST":
				$name = "Newfoundland Standard Time (GMT-3:30)";
				$offset = -(3600*3.5);
				break;
			case "NDT":
				$name = "Newfoundland Daylight Time (GMT-2:30)";
				$offset = -(3600*2.5);
				break;
			case "UTC":
			case "GMT":
				$name = "Greenwich Mean Time (GMT / AO)";
				$offset = 0;
				break;
			case "CET":
				$name = "Central European Time (GMT+1)";
				$offset = 3600;
				break;
			case "CEST":
				$name = "Central European Summer Time (GMT+2)";
				$offset = 3600*2;
				break;
			case "EET":
				$name = "Eastern European Time (GMT+2)";
				$offset = 3600*2;
				break;
			case "EEST":
				$name = "Eastern European Summer Time (GMT+3)";
				$offset = 3600*3;
				break;
			case "EEDT":
				$name = "Eastern European Daylight Time (GMT+3)";
				$offset = 3600*3;
				break;
			case "MSK":
				$name = "Moscow Time (GMT+3)";
				$offset = 3600*3;
				break;
			case "MSD":
				$name = "Moscow Daylight Time (GMT+4)";
				$offset = 3600*4;
				break;
			case "IRT":
				$name = "Iran Time (GMT+3:30)";
				$offset = 3600*3.5;
				break;
			case "IST":
				$name = "Indian Standard Time (GMT+5:30)";
				$offset = 3600*5.5;
				break;
			case "ICT":
				$name = "Indochina Time (GMT+7)";
				$offset = 3600*7;
				break;
			case "CCST":
				$name = "China Standard Time (GMT+8)";
				$offset = 3600*8;
				break;
			case "JST":
				$name = "Japan Standard Time (GMT+9)";
				$offset = 3600*9;
				break;
			case "ACST":
				$name = "Australian Central Standard Time (GMT+9:30)";
				$offset = 3600*9.5;
				break;
			case "ACDT":
				$name = "Australian Central Daylight Time (GMT+10:30)";
				$offset = 3600*10.5;
				break;
			case "AEST":
				$name = "Australian Eastern Standard Time (GMT+10)";
				$offset = 3600*10;
				break;
			case "AEDT":
				$name = "Australian Eastern Daylight Time (GMT+11)";
				$offset = 3600*11;
				break;
			default:
				return null;
		}

		$obj = new stdClass;
		$obj->name = $name;
		$obj->offset = $offset;
		$obj->time = date($time_format, $time + $offset);
		return $obj;
	}
}
