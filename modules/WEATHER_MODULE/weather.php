<?php

//  getweatherdata("api.wunderground.com", 80, $url);
if (!function_exists(getweatherdata)){
	function getweatherdata ($host, $port, $url) {
		
		$ip = gethostbyname($host); $port = 80;
		if ($ip == $host) {
			return -1;
		} // Failed to get host
		
		if (!($server = @fsockopen($ip, $port, $errno, $errstr, 5))) { // we connect?
			return -2;
		}
		
		$request = "GET $url HTTP/1.1\r\nHost: $host\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)\r\nAccept: text/xml,application/xml,application/xhtml+xml,text/html\r\nAccept-Language: en-us,en;q=0.5\r\nAccept-Encoding: gzip,deflate\r\nKeep-Alive: 300\r\nConnection: Keep-Alive\r\nCache-Control: max-age=0\r\n\r\n";
		fputs($server, $request);
		while (!feof($server)) { 
			$stream .= fread($server, 8192);
		}
		fclose($server);
		
		$stream = substr($stream, strpos( $stream, "\r\n\r\n") +4); //remove packet info
		
		while(True) {
			$pos = @strpos($stream, "\r\n", 0);
			if(!($pos === false) && $size === NULL) {
				// we found CRLF, get len from hex
				$size = hexdec(substr($stream, 0, $pos));		
				// get the actual chunk-len, reset $response, $size
				$temp .= substr($stream, $pos+2, $size);
				$stream = substr($stream, ($pos+2) + $size);
				$size = NULL;
			} else {
				break;
			}
		}
		
		return $temp;
	}
	
}

if (!function_exists(fix_num_space)){
	function fix_num_space($number, $digits=7) {

		if (strlen($number) > $digits) {
			return "ERR";
		}

		$digits--; $number = strrev($number);
		for ($pos=$digits; $pos>=0; $pos--) {

			$setpoint = substr($number,$pos,1);

			if ($setpoint == "" && $pos == $digits) {$retval = "<black>_"; $inblack = true;}
			elseif ($setpoint == "") {$retval .= "_";}
			elseif ($setpoint != "" && $inblack == true) {$retval .= "<end>$setpoint"; $inblack = false;}
			elseif ($setpoint != "") {$retval .= $setpoint;}

			if ((($pos/3) == intval($pos/3)) && $pos != 0) {$retval .= ",";}

		}
		return $retval;
	}
}

if  (preg_match("/^weather (.+)$/i", $message, $arr)) {
	
	$host      = "api.wunderground.com";
	$geolookup = "/auto/wui/geo/GeoLookupXML/index.xml?query=".urlencode($arr[1]);
	$current   = "/auto/wui/geo/WXCurrentObXML/index.xml?query=".urlencode($arr[1]);
	$forecast  = "/auto/wui/geo/ForecastXML/index.xml?query=".urlencode($arr[1]);
	$alerts    = "/auto/wui/geo/AlertsXML/index.xml?query=".urlencode($arr[1]);
	
	$geolookup = getweatherdata("api.wunderground.com", 80, $geolookup);

// Geolookup
	
	if (xml::spliceData($geolookup, "<wui_error>", "</wui_error>") != ""){
		$this->send("No information is available for <highlight>".$arr[1]."<end>.", $sendto);
		return;
	}
	
	$locations = xml::spliceMultiData($geolookup, "<name>", "</name>");
	if (count($locations) > 1){
		foreach ($locations as $spot) {
			$blob .= $this->makeLink($spot, "/tell <myname> weather $spot", "chatcmd")."\n";
		}

		$msg = $this->makeLink('Multiple hits for '.$arr[1].'.', $blob);
		$this->send($msg, $sendto);
		return;
	}
	
	$this->send("Collecting data for <highlight>".$arr[1]."<end>.", $sendto);
	
	$current   = getweatherdata("api.wunderground.com", 80, $current);
	$forecast  = getweatherdata("api.wunderground.com", 80, $forecast);
	$alerts    = getweatherdata("api.wunderground.com", 80, $alerts);
	
// CURRENT
	
	$updated = xml::spliceData($current, "<observation_time_rfc822>", "</observation_time_rfc822>");

	if ($updated == ", :: GMT") {
		$this->send("No information is available for <highlight>".$arr[1]."<end>.", $sendto);
		return;
	}
	
	$credit = xml::spliceData($current, "<credit>", "</credit>");
	$crediturl = xml::spliceData($current, "<credit_URL>", "</credit_URL>");
	$observeLoc = xml::spliceData($current, "<observation_location>", "</observation_location>");
	$fullLoc = xml::spliceData($observeLoc, "<full>", "</full>");
	$country = xml::spliceData($observeLoc, "<country>", "</country>");
	$lat = xml::spliceData($observeLoc, "<latitude>", "</latitude>");
	$lon = xml::spliceData($observeLoc, "<longitude>", "</longitude>");
	$elevation = xml::spliceData($observeLoc, "<elevation>", "</elevation>");
	$weather = xml::spliceData($current, "<weather>", "</weather>");
	$tempstr = xml::spliceData($current, "<temperature_string>", "</temperature_string>");
	$humidity = xml::spliceData($current, "<relative_humidity>", "</relative_humidity>");
	$windstr = xml::spliceData($current, "<wind_string>", "</wind_string>");
	$windgust = xml::spliceData($current, "<wind_gust_mph>", "</wind_gust_mph>");
	$pressurestr = str_replace('"', "&quot;", xml::spliceData($current, "<pressure_string>", "</pressure_string>"));
	$dewstr = xml::spliceData($current, "<dewpoint_string>", "</dewpoint_string>");
	$heatstr = xml::spliceData($current, "<heat_index_string>", "</heat_index_string>");
	$windchillstr = xml::spliceData($current, "<windchill_string>", "</windchill_string>");
	$visibilitymi = xml::spliceData($current, "<visibility_mi>", "</visibility_mi>");
	$visibilitykm = xml::spliceData($current, "<visibility_km>", "</visibility_km>");
	
	$latlonstr  = number_format(abs($lat), 1);
	if (abs($lat) == $lat) {
		$latlonstr .= "N ";
	} else {
		$latlonstr .= "S ";
	}
	$latlonstr .= number_format(abs($lon), 1);
	if (abs($lon) == $lon) {
		$latlonstr .= "E ";
	} else {
		$latlonstr .= "W ";
	}
	$latlonstr .= $this->makeLink("Google Map", "/start http://maps.google.com/maps?q=$lat,$lon", "chatcmd")." ";
	$latlonstr .= $this->makeLink("Wunder Map", "/start http://www.wunderground.com/wundermap/?lat=$lat&lon=$lon&zoom=10", "chatcmd")."\n\n";
	$blob  = "<highlight>Credit:<end> ".$this->makeLink($credit, "/start $crediturl", "chatcmd")."\n";
	$blob .= "<highlight>Last Updated:<end> $updated\n\n";
	$blob .= "<highlight>Location:<end> $fullLoc, $country\n";
	$blob .= "<highlight>Lat/Lon:<end> $latlonstr";
	
	$blob .= "<highlight>Currently:<end> $tempstr, $weather\n";
	$blob .= "<highlight>Humidity:<end> $humidity\n";
	$blob .= "<highlight>Dew Point:<end> $dewstr\n";
	$blob .= "<highlight>Wind:<end> $windstr";
	if ($windgust) {
		$blob .= " (Gust:$windgust mph)\n";
	} else {
		$blob .= "\n";
	}
	if ($heatstr != "NA") {
		$blob .= "<highlight>Heat Index:<end> $heatstr\n";
	}
	if ($windchillstr != "NA") {
		$blob .= "<highlight>Windchill:<end> $windchillstr\n";
	}
	$blob .= "<highlight>Pressure:<end> $pressurestr\n";
	$blob .= "<highlight>Visibility:<end> $visibilitymi miles, $visibilitykm km\n";
	$blob .= "<highlight>Elevation:<end> $elevation\n";
	
// ALERTS

	$alertitems = xml::spliceMultiData($alerts, "<AlertItem>", "</AlertItem>");

	if (count($alertitems) == 0) {
		$blob .= "\n<header>Alerts:<end> None reported.\n";
	} else {
		forEach ($alertitems as $thisalert) {

			$blob .= "\n<header>Alert: ".xml::spliceData($thisalert, "<description>", "</description>")."<end>\n\n";
			// gotta find date/expire manually.
			$start = strpos($thisalert, ">", strpos($thisalert, "<date epoch="))+1;
			$end = strpos($thisalert, "<", $start);
			$blob .= "<highlight>Issued:<end>".substr($thisalert,$start,$end-$start)."\n";
			
			$start = strpos($thisalert, ">", strpos($thisalert, "<expires epoch="))+1;
			$end = strpos($thisalert, "<", $start);
			$blob .= "<highlight>Expires:<end>".substr($thisalert,$start,$end-$start)."\n";
			$blob .= xml::spliceData($thisalert, "<message>", "</message>")."";
		}
	}
	
// FORECAST

	$simpleforecast = xml::spliceData($forecast, "<simpleforecast>", "</simpleforecast>");
	$forecastday = xml::spliceMultiData($simpleforecast, "<forecastday>", "</forecastday>");
	if (count($forecastday)>0) {
		$blob .= "\n<header>Forecast:<end>\n\n";
		forEach ($forecastday as $day) {
			
			if (!($condition = xml::spliceData($day, "<conditions>", "</conditions>"))) {
				break;
			}
			
			$low[0] = xml::spliceData($day, "<low>", "</low>");
			$low[1] = xml::spliceData($low[0], "<fahrenheit>", "</fahrenheit");
			$low[2] = xml::spliceData($low[0], "<celsius>", "</celsius");
			$high[0] = xml::spliceData($day, "<high>", "</high>");
			$high[1] = xml::spliceData($high[0], "<fahrenheit>", "</fahrenheit");
			$high[2] = xml::spliceData($high[0], "<celsius>", "</celsius");
			$pop = xml::spliceData($day, "<pop>", "</pop>");
			
			$blob .= "<highlight>".xml::spliceData($day, "<weekday>", "</weekday>").":<end> $condition";
			if (0 == $pop) {
				$blob .= "\n";
			} else {
				$blob .= " ($pop% Precip)\n";
			}
			
			$blob .= "<highlight>High:<end> ";
			$blob .= fix_num_space($high[1],3)."F";
			$blob .= fix_num_space($high[2],3)."C    ";
			
			$blob .= "<highlight>Low:<end> ".fix_num_space($low[1],3)."F";
			$blob .= fix_num_space($low[2],3)."C\n\n";
			
		}
	}

	$msg = $this->makeLink('Weather: '.$arr[1].'.', $blob);
	
	$this->send($msg, $sendto);

}
?>