<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Items DB update
   ** Version: 0.7
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.03.2006
   ** Date(last modified): 28.07.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

//If no entry was found in the settings table start with the basic db
if($this->settings["aodb_version"] == 0) {
    echo "Starting Items Database creation. \nDepending on the Database you are using this process can take a few mins.\n";
	$db_version = 0;
} else {
   	eregi("([0-9]+).([0-9]+).([0-9]+)", $this->settings["aodb_version"], $temp);
   	$db_version = $temp[1].$temp[2].$temp[3];
}

if($d = dir("./sql")) {
	while(false !== ($entry = $d->read())) {
    	if(is_file("./sql/".$entry) && eregi("aodb([0-9]+).([0-9]+).([0-9]+).sql", $entry, $temp)) {
			if(strlen($temp[0]) < 2) $temp[0] = "0".$temp[0];
			if(strlen($temp[1]) < 2) $temp[1] = "0".$temp[1];
			if(strlen($temp[2]) < 2) $temp[2] = "0".$temp[2];
			
	      	$file_version = $temp[1].$temp[2].$temp[3];
	       	if($file_version > $db_version)
           		$files[] = $entry;
        }
    }
}

if(count($files) >= 1) {
   	//Sort the found files after their version
   	$num_files = count($files);
   	if($num_files > 1) {
   		$sort = 0;
   		do {
       		$sort = 1;
      		for($i = 0; $i < ($num_files - 1); $i++) {
	   			eregi("aodb([0-9.]+).sql", $files[$i], $temp);
				$version1 = explode(".", $temp[1]);
				if(strlen($version1[0]) < 2) $version1[0] = "0".$version1[0];
				if(strlen($version1[1]) < 2) $version1[1] = "0".$version1[1];
				if(strlen($version1[2]) < 2) $version1[2] = "0".$version1[2];
								
	   			eregi("aodb([0-9.]+).sql", $files[$i + 1], $temp);
				$version2 = explode(".", $temp[1]);

				if(strlen($version2[0]) < 2) $version2[0] = "0".$version2[0];
				if(strlen($version2[1]) < 2) $version2[1] = "0".$version2[1];
				if(strlen($version2[2]) < 2) $version2[2] = "0".$version2[2];
				
				$v1 = $version1[0].$version1[1].$version1[2];
				$v2 = $version2[0].$version2[1].$version2[2];
				
              	if($v1 < $v2) {
                   	$temp_files = $files[$i+1];
                   	$files[$i+1] = $files[$i];
                   	$files[$i] = $temp_files;
                   	$sort = 0;
               	}
            }
   		} while(!$sort);
   	}

   	//Upload them to the local db
	$filearray = file("./sql/{$files[0]}");
	$items = count($filearray);
	if($items > 2) {
	   	eregi("aodb([0-9.]+).sql", $files[0], $temp);
		$version = explode(".", $temp[1]);
		if(strlen($version[0]) < 2) $version[0] = "0".$version[0];
		if(strlen($version[1]) < 2) $version[1] = "0".$version[1];
		if(strlen($version[2]) < 2) $version[2] = "0".$version[2];

	   	$db_version = $version[0].".".$version[1].".".$version[2];
	   	
		echo "$items Items needs to be added/changed(Version: $db_version)....";
		$db->query("SELECT * FROM aodb");
		if($db->numrows() != 0)
			$db->query("DELETE FROM aodb");
			
		$db->beginTransaction();
		foreach($filearray as $num => $line)
			$db->query(rtrim($line));
	    $db->Commit();			
		echo "Done.\n";
	
	   	bot::savesetting("aodb_version", $db_version);
	    echo "Database Update to version {$temp[1]} completed.\n";
	} else
		echo "Invalid Databasefile found\n";
} else
	echo "No Update found for the itemsdatabase.\n";  
?>