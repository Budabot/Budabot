<?php 
   /*
   ** Author: Sebuda, Derroylo (RK2)
   ** Description: AO xml abstaction layer for guild info, whois, player history and server status.
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 16.01.2007
   ** 
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann and J. Gracik
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

//class provide some basic function to splice XML Files or getting an XML file from a URL
class xml {
	//Extracts one entry of the XML file 	
	public function spliceData($sourcefile, $start, $end){
	 	$data = explode($start, $sourcefile, 2);
	 	if(!$data || (is_array($data) && count($data) < 2))
	 		return "";
	 	$data = $data[1];
	 	$data = explode($end, $data, 2);
	 	if(!$data || (is_array($data) && count($data) < 2))
	 		return "";
		return $data[0]; 
	}
     
	//Extracts more then one entry of the XML file       
    public function spliceMultiData($sourcefile, $start, $end){
		$targetdata = array();
		$sourcedata = explode($start, $sourcefile);
		array_shift($sourcedata);
        foreach ($sourcedata as $indsplit) {
        	$target = explode($end, $indsplit, 2);
            $targetdata[] = $target[0];
        }
		return $targetdata; 
    }
    
	//Trys to download a file from a URL   
	public function getUrl($url, $timeout = '10') {
	 	$url = strtolower($url);
	 	
		//Remove any http tags
		$url = str_replace("http://", "", $url);
		//Put an / at the end of the url if not there
		if(!strstr($url, '/'))
			$url .= '/';
				
		eregi("^(.+)(\.de|\.biz|\.com|\.org|\.info)/(.*)$", $url, $tmp);
		$host = $tmp[1].$tmp[2];
		$uri = "/".$tmp[3];
		$fp = @fsockopen($host, 80, $errno, $errstr, 10);
		@stream_set_timeout($fp, $timeout);
		if($fp) {
			@fputs($fp, "GET $uri HTTP/1.0\nHost: $host\r\n\r\n");				
			while($indata = fread($fp,1024))
				$data .= $indata;  
	
			fclose($fp);
			return $data;
		} else
			return NULL;
	}
} //end class xml

//the whois class is downloading/caching/verifying an player XML file
class whois extends xml{
	public $firstname;        
    public $name;             
    public $lastname;        
    public $level;             
    public $breed;            
    public $gender;            
    public $faction;            
    public $prof;    
    public $prof_title;    
    public $ai_rank;    
    public $ai_level;    
    public $organization_id;
    public $org;
    public $rank;                
    public $rank_id;
	public $between;
	public $errorInfo;
	public $errorCode = 0;
	
	//construktor of the class
	function __construct($name, $rk_num = 0, $cache = 0){
		//if no server number is specified use the one on which the bot is logged in
		if($rk_num == 0) {
		  	global $vars;
			$rk_num = $vars["dimension"];
		}

		//if no specific cachefolder is defined use the one from config.php
		if($cache == 0) {
		  	global $vars;
			$cache = $vars["cachefolder"];  
		}

		//Making sure that the cache folder exists
        if(!dir($cache))
	        mkdir($cache, 0777);

		//Character lookup        		
        $this->lookup($name, $rk_num, $cache);                
    }

	//the player lookup itself
	function lookup($name, $rk_num, $cache) {
	 	$data_found = false;
		$data_save = false;
		$name = ucfirst(strtolower($name));
		
		//Check if a xml file of the person exists, that it isn´t older then 24hrs and correct
		if(file_exists("$cache/$name.$rk_num.xml")) {
	        $mins = (time() - filemtime("$cache/$name.$rk_num.xml")) / 60;
            $hours = floor($mins/60);
            if($hours < 24 && $fp = fopen("$cache/$name.$rk_num.xml", "r")) {
				while(!feof ($fp))
					$playerbio .= fgets ($fp, 4096);
				fclose($fp);
				if(xml::spliceData($playerbio, '<nick>', '</nick>') == $name) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($playerbio);
					@unlink("$cache/$name.$rk_num.xml");
				}
			}
        }
        
        //If no file was found or it is outdated try to update it from anarchyonline.com
        if(!$data_found) {
			$playerbio = xml::getUrl("www.anarchyonline.com/character/bio/d/$rk_num/name/$name/bio.xml");
			if(xml::spliceData($playerbio, '<nick>', '</nick>') == $name) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($playerbio);
			}
		}
		
		//If ao.com was too slow to respond or got wrong data back try to update it from auno.org
		if(!$data_found) {
			$playerbio = xml::getUrl("http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name");
			if(xml::spliceData($playerbio, '<nick>', '</nick>') == $name) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($playerbio);
			}
		}
		
		//If both site were not responding or the data was invalid and a xml file exists get that one
		if(!$data_found && file_exists("$cache/$name.$rk_num.xml")) {
			if ($fp = fopen("$cache/$name.$rk_num.xml", "r")) {
				while(!feof ($fp))
					$playerbio .= fgets ($fp, 4096);
				fclose($fp);

				if(xml::spliceData($playerbio, '<nickname>', '</nickname>') == $name)
					$data_found = true;
				else {
					$data_found = false;
					unset($playerbio);
					@unlink("$cache/$name.$rk_num.xml");
				}
			}
		}
		
		//if there is still no valid data available give an error back
		if(!$data_found) {
   		  	$this->firstname = "";
		  	$this->lastname = "";
		  	$this->rank_id = 6;
		  	$this->rank = "Applicant";
	  		$this->level = "1";
		  	$this->prof = "Unknown";
		  	$this->gender = "Unknown";
		  	$this->breed = "Unknown";
           	$this->errorCode = 1;
           	$this->errorInfo = "Couldn´t get Character infos for $name";
           	return;
		}

		//parsing of the player data		
        $this->firstname	= xml::spliceData($playerbio, '<firstname>', '</firstname>');
        $this->name         = xml::spliceData($playerbio, '<nick>', '</nick>');
        $this->lastname     = xml::spliceData($playerbio, '<lastname>', '</lastname>');
        $this->level        = xml::spliceData($playerbio, '<level>', '</level>');
        $this->breed        = xml::spliceData($playerbio, '<breed>', '</breed>');
        $this->gender       = xml::spliceData($playerbio, '<gender>', '</gender>');
        $this->faction      = xml::spliceData($playerbio, '<faction>', '</faction>');
        $this->prof         = xml::spliceData($playerbio, '<profession>', '</profession>');
        $this->prof_title   = xml::spliceData($playerbio, '<profession_title>', '</profession_title>');
        $this->ai_rank      = xml::spliceData($playerbio, '<defender_rank>', '</defender_rank>');
        $this->ai_level     = xml::spliceData($playerbio, '<defender_rank_id>', '</defender_rank_id>');
        $this->org_id       = xml::spliceData($playerbio, '<organization_id>', '</organization_id>');
        $this->org          = xml::spliceData($playerbio, '<organization_name>', '</organization_name>');
        $this->rank         = xml::spliceData($playerbio, '<rank>', '</rank>');
        $this->rank_id      = xml::spliceData($playerbio, '<rank_id>', '</rank_id>');

		//if a new xml file is downloaded save it		
		if($data_save) {
	        $fp = fopen("$cache/$name.$rk_num.xml", "w");
	        fwrite($fp, $playerbio);
	        fclose($fp);
	    }
    }
} //end of whois

//the org class is downloading/caching/verifying an org XML file
class org extends xml {
    public $members;
    public $member;
    public $errorCode = 0;
    public $errorInfo;

	//contructor of the class
	function __construct($organization_id = 0, $rk_num = 0, $cache = 0, $force_update = false){ 
		//if no server number is specified use the one on which the bot is logged in
		if($rk_num == 0) {
		  	global $vars;
			$rk_num = $vars["dimension"];
		}

		//if no specific cachefolder is defined use the one from config.php
		if($cache == 0) {
		  	global $vars;
			$cache = $vars["cachefolder"];  
		}

		//Making sure that the cache folder exists
        if(!dir($cache))
	        @mkdir($cache, 0777);
		
		//organisation lookup
        $this->lookup($organization_id, $rk_num, $cache, $force_update);            
	} //end of contructor
    
    //the organisation lookup function
	function lookup($organization_id, $rk_num, $cache, $force_update) {
	 	global $vars;
		$data_found = false;
		$data_save = false;
		
		//Check if a xml file of the person exists and if it is uptodate
		if(!force_update && file_exists("$cache/$organization_id.$rk_num.xml")) {
	        $mins = (time() - filemtime("$cache/$organization_id.$rk_num.xml")) / 60;
            $hours = floor($mins/60);
            //if the file is not older then 24hrs and it is not the roster of the bot guild then use the cache one, when it the xml file from the org bot guild and not older then 6hrs use it
            if(($hours < 24 && $vars["my guild id"] != $organization_id) || ($hours < 6 && $vars["my guild id"] == $organization_id)) {
             	$fp = fopen("$cache/$organization_id.$rk_num.xml", "r"); 
				while(!feof($fp))
					$orgxml .= fgets($fp, 4096);
				fclose($fp);
				if(xml::spliceData($orgxml, '<id>', '</id>') == $organization_id) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($orgxml);
					@unlink("$cache/$organization_id.$rk_num.xml");
				}
			}
        }
        
        //If no file was found or it is outdated try to update it from anarchyonline.com
        if(!$data_found) {
			$orgxml = xml::getUrl("http://www.anarchy-online.com/org/stats/d/$rk_num/name/$organization_id/basicstats.xml", 30);
			if(xml::spliceData($orgxml, '<id>', '</id>') == $organization_id) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($orgxml);
			}
		}
		
		//If the site was not responding or the data was invalid and a xml file exists get that one
		if(!$data_found && file_exists("$cache/$organization_id.$rk_num.xml")) {
			if($fp = fopen("$cache/$organization_id.$rk_num.xml", "r")) {
				while(!feof($fp))
					$orgxml .= fgets($fp, 4096);
				fclose($fp);
				if(xml::spliceData($orgxml, '<id>', '</id>') == $name) {
					$data_found = true;
				} else {
					$data_found = false;
					unset($orgxml);
					@unlink("$cache/$organization_id.$rk_num.xml");
				}
			}
		}
		//if there is still no valid data available give an error back
		if(!$data_found) {
           	$this->errorCode = 1;
           	$this->errorInfo = "Couldn´t get infos for the organization";
           	return;
		}

		//parsing of the memberdata
		$members = xml::splicemultidata($orgxml, "<member>", "</member>");
        $this->orgname	= xml::spliceData($orgxml, "<name>", "</name>");
        $this->orgside	= xml::spliceData($orgxml, "<side>", "</side");

        global $chatBot;
        foreach($members as $amember) {
			$name								= xml::splicedata($amember,"<nickname>", "</nickname>");
            $this->member[]						= $name;                    
            $this->members[$name]["firstname"]	= xml::spliceData($amember, '<firstname>', '</firstname>');
            $this->members[$name]["firstname"]	= xml::spliceData($amember, '<firstname>', '</firstname>');
            $this->members[$name]["name"] 		= xml::spliceData($amember, '<nickname>', '</nickname>');
            $this->members[$name]["lastname"]	= xml::spliceData($amember, '<lastname>', '</lastname>');
            $this->members[$name]["level"]		= xml::spliceData($amember, '<level>', '</level>');
            $this->members[$name]["breed"]		= xml::spliceData($amember, '<breed>', '</breed>');
            $this->members[$name]["gender"]		= xml::spliceData($amember, '<gender>', '</gender>');
            $this->members[$name]["faction"]	= $this -> orgside;
            $this->members[$name]["profession"]	= xml::spliceData($amember, '<profession>', '</profession>');
            $this->members[$name]["ai_rank"]	= xml::spliceData($amember, '<defender_rank>', '</defender_rank>');
            $this->members[$name]["ai_level"]	= xml::spliceData($amember, '<defender_rank_id>', '</defender_rank_id>');
            $this->members[$name]["rank"]		= xml::spliceData($amember, '<rank_name>', '</rank_name>');
            $this->members[$name]["rank_id"]	= xml::spliceData($amember, '<rank>', '</rank>');					
			$this->members[$name]["id"]			= $chatBot->get_uid($name);
		}

		//if a new xml file was downloaded, save it
		if($data_save) {
	        $fp = fopen("$cache/$organization_id.$rk_num.xml", "w");
	        fwrite($fp, $orgxml);
	        fclose($fp);
	    }	 
	} //end lookup
} //end class org

//the history class is downloading/caching/verifying an player history XML file
class history extends xml{     
	public $name;             
	public $data;
	public $errorInfo;
	public $errorCode = 0;

	//constructor of the class
    function __construct($name, $rk_num = 0, $cache = 0){
		//if no server number is specified use the one on which the bot is logged in
		if($rk_num == 0) {
		  	global $vars;
			$rk_num = $vars["dimension"];
		}

		//if no specific cachefolder is defined use the one from config.php
		if($cache == 0) {
		  	global $vars;
			$cache = $vars["cachefolder"];  
		}

		//Making sure that the cache folder exists
        if(!dir($cache))
	        mkdir($cache, 0777);

        $this->lookup($name, $rk_num, $cache);                
    } //end constructor

	//the lookup function
    function lookup($name, $rk_num, $cache) {
  	 	$data_found = false;
		$data_save = false;
		$name = ucfirst(strtolower($name));
		
		//Check if a xml file of the person exists and if it is uptodate
		if(file_exists("$cache/$name.$rk_num.history.xml")) {
	        $mins = (time() - filemtime("$cache/$name.$rk_num.history.xml")) / 60;
            $hours = floor($mins/60);
            if($hours < 24 && $fp = fopen("$cache/$name.$rk_num.history.xml", "r")) {
				while(!feof ($fp))
					$playerhistory .= fgets ($fp, 4096);
				fclose($fp);
				if(xml::spliceData($playerhistory, '<nick>', '</nick>') == $name)
					$data_found = true;
				else {
					$data_found = false;
					unset($playerhistory);
					@unlink("$cache/$name.$rk_num.history.xml");
				}
			}
        }
        		
		//If no old history file was found or it was invalid try to update it from auno.org
		if(!$data_found) {
			$playerhistory = xml::getUrl("http://auno.org/ao/char.php?output=xml&dimension=$rk_num&name=$name", 20);
			if(xml::spliceData($playerhistory, '<nick>', '</nick>') == $name) {
				$data_found = true;
				$data_save = true;
			} else {
				$data_found = false;
				unset($playerhistory);
			}
		}
		
		//If the site was not responding or the data was invalid and a xml file exists get that one
		if(!$data_found && file_exists("$cache/$name.$rk_num.history.xml")) {
			if ($fp = fopen("$cache/$name.$rk_num.history.xml", "r")) {
				while(!feof($fp))
					$playerhistory .= fgets($fp, 4096);
				fclose($fp);

				if(xml::spliceData($playerhistory, '<nick>', '</nick>') == $name)
					$data_found = true;
				else {
					$data_found = false;
					unset($playerhistory);
					@unlink("$cache/$name.$rk_num.history.xml");
				}
			}
		}
		
		//if there is still no valid data available give an error back
		if(!$data_found) {
           	$this->errorCode = 1;
           	$this->errorInfo = "Couldn´t get History of $name";
           	return;
		}

		//parsing of the xml file		
		$data = xml::spliceData($playerhistory, "<history>", "</history>");
		$data = xml::splicemultidata($data, "<entry", "/>");
		foreach($data as $hdata) {
			eregi("date=\"(.+)\" level=\"(.+)\" ailevel=\"(.*)\" faction=\"(.+)\" guild=\"(.*)\" rank=\"(.*)\"", $hdata, $arr);
			$this->data[$arr[1]]["level"] = $arr[2];
			$this->data[$arr[1]]["ailevel"] = $arr[3];
			$this->data[$arr[1]]["faction"] = $arr[4];
			$this->data[$arr[1]]["guild"] = $arr[5];
			$this->data[$arr[1]]["rank"] = $arr[6];																				
		}
		
		//if he downloaded a new xml file save it in the cache folder
		if($data_save) {
	        $fp = fopen("$cache/$name.$rk_num.history.xml", "w");
	        fwrite($fp, $playerbio);
	        fclose($fp);
	    }    	
	} //end of lookup    
} //end of history class

//class to get and parse the server statistics
class server extends xml{     
	public $data;
	public $servermanager;
	public $clientmanager;
	public $chatserver;
	public $locked;
	public $omni;
	public $neutral;
	public $clan;
	public $name;
	public $errorInfo;
	public $errorCode = 0;

	//the constructor
    function __construct($rk_num = 0){
		//if no server was specified use the one where the bot is logged in
		if($rk_num == 0) {
		  	global $vars;
			$rk_num = $vars["dimension"];
		}

		//get the server status
        $this->lookup($rk_num);
    }

    function lookup($rk_num) {
	  	$serverstat = xml::getUrl("probes.funcom.com/ao.xml", 30);
			         
        if($serverstat == NULL) {
          	$this->errorCode = 1;
           	$this->errorInfo = "Couldn´t get Serverstatus for Dimension $rk_num";
			return;
        }

       	if($rk_num == 3)
       		$rk_num = "g";
       	elseif($rk_num == 4)
       		$rk_num = "t";

       	$data = xml::spliceData($serverstat, "<dimension name=\"d$rk_num", "</dimension>");
		eregi("locked=\"(0|1)\"", $data, $tmp);
		$this->locked = $tmp[1];

		eregi("<omni percent=\"([0-9.]+)\"/>", $data, $tmp);
		$this->omni = $tmp[1];
		eregi("<neutral percent=\"([0-9.]+)\"/>", $data, $tmp);
	    $this->neutral = $tmp[1];
		eregi("<clan percent=\"([0-9.]+)\"/>", $data, $tmp);
	    $this->clan = $tmp[1];

		eregi("<servermanager status=\"([0-9]+)\"/>", $data, $tmp);
	    $this->servermanager = $tmp[1];
		eregi("<clientmanager status=\"([0-9]+)\"/>", $data, $tmp);
	    $this->clientmanager = $tmp[1];
		eregi("<chatserver status=\"([0-9]+)\"/>", $data, $tmp);
	    $this->chatserver = $tmp[1];
              	
		eregi("display-name=\"(.+)\" loadmax", $data, $tmp);
	    $this->name = $tmp[1];

		$data = xml::spliceMultiData($data, "<playfield", "/>");			
		foreach($data as $hdata) {
			if(eregi("id=\"(.+)\" name=\"(.+)\" status=\"(.+)\" load=\"(.+)\" players=\"(.+)\"", $hdata, $arr)) {
				$this->data[$arr[2]]["status"] = $arr[3];
				$this->data[$arr[2]]["load"] = $arr[4];
				$this->data[$arr[2]]["players"] = $arr[5];
			}				
		}
    } //end lookup function
} //end server class
?>
