<?
   /*
   ** Author: Healnjoo (RK2)
   ** Description: Publish the bots Online list to a webpage
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03/12/2007
   ** Date(last modified): 03/12/2007
   ** 
   ** Licence Infos: 
   ** Same as Budabot.
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

/////// REQUIRED FIELDS ///////
$dimension=2; // The dimension your orgbot is on.

// The file to save the whos online data to.  This file will need write permissions.
$file="online.txt";  

/////// OPTIONS ///////

// Folder you want to use to cache data files.  The folder will need write permissions. Leave blank if you dont want to use it. I suggest using it, otherwise the page has to connect to anarchy-online.com each time to download each persons data, which can be slow at times.
$cache = "./cache/";

// How many days to keep the cached data.
$keepcache = 1; //days

//Display headshot.
$showimages = true;

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($_GET)
	{
	if($_GET['upload'])
		{
		$upload=rawurldecode($_GET['upload']);

		if($file)
			{
			$fp = @fopen($file,"w");
			$fw = @fwrite($fp, $upload);
			@fclose($fp);
			echo "done";
			}	
		else
			echo "Unable to write to file.";
		}

	if($_GET['clearcache'])
		{
		if(is_dir($cache))
			{
			$dh  = opendir($cache);
			while (false !== ($filename = readdir($dh))) 
				$files[] = $filename;

			sort($files);
			foreach ($files as $key => $val)
				{
				if($key > 1)
					unlink($cache.$val);
				}

			echo "Cache cleared.";
			}
		else
			echo "Cache is not enabled or path does not exist.";
		}
	}
else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Currently Online </TITLE>
</HEAD>
<BODY>
<?
if(file_exists($file))
	{
	$list=file_get_contents(stripslashes($file));
	$list=explode("\r\n",trim($list));
	sort($list);

	foreach($list as $name)
		{
		if(is_array($list))
			{
			$afk = "";
			if(strpos($name, "|"))
				{
				$arr = explode("|", $name);
				$name = $arr[0];
				$afk = $arr[1];
				}
			
			echo '<table border="0" cellpadding="0" cellspacing="0" width="400" id="table1" align="center">';
			
			$insideitem = false;
			$nick = "";
			$rank = "";
			$level = "";
			$profession = "";
			$org = "";
			$picurl = "";
			$ailevel = "";

			$xml_parser = xml_parser_create(); 
			xml_set_element_handler($xml_parser, "startElement", "endElement"); 
			xml_set_character_data_handler($xml_parser, "characterData");
			
			// Try cache before requesting from anarchy-online.com
			$filename = $cache.$name.".xml";
				
			if(file_exists($filename))
				{
				$overwritecache = mktime(0,0,0,date("m"), date("d")-$keepcache, date("y"));
				$lastmod = filemtime($filename);
				if($lastmod > $overwritecache) 
					{
					// use cache
					xml_parse($xml_parser, file_get_contents($filename)); 
					}
				else{
					//cache is oudated, renew.
					$data = getfrom_ao($xml_parser, $dimension, $name);											
					xml_parse($xml_parser, $data); 
					}
				}
			else{
				// read from anarchy-online.cm
				$data = getfrom_ao($xml_parser, $dimension, $name);
				xml_parse($xml_parser, $data); 

				}
			xml_parser_free($xml_parser);
			echo "</TABLE></BODY></HTML></CENTER>";
			}	
		}
	}
?>

</BODY>
</HTML>
<?
}
function getfrom_ao($xml_parser, $dimension, $name)
	{
	global $cache;
	$filename = $cache.$name.".xml";

	$fp = fopen('http://anarchy-online.com/character/bio/d/'.$dimension.'/name/'.strtolower($name).'/bio.xml',"r") or die("Error reading data.");

	while ($data = fread($fp, 4096))
		$writedata .= $data;

	fclose($fp);

	if(!is_dir($cache) && ($cache))
		mkdir($cache);

	if(is_dir($cache) && ($cache))
		{
		$handle = fopen($filename, 'w');
		fwrite($handle, $writedata);
		fclose($handle);
		}

	return $writedata;
	}

function startElement($parser, $tagName, $attrs)
	{
	global $insideitem, $tag;
	if ($insideitem) 
		$tag = $tagName;
	elseif ($tagName == "CHARACTER")
		$insideitem = true;
	}


function characterData($parser, $data)
	{
	global $insideitem, $tag, $nick, $rank, $level, $profession, $org, $picurl, $ailevel;

	if ($insideitem)
		{
        switch ($tag)
			{
			case "NICK":
				$nick .= $data;
				break;
			case "RANK":
				$rank .= $data;
				break;
			case "LEVEL":
				$level .= $data;
				break;
			case "PROFESSION":
				$profession .= $data;
				break;
			case "ORGANIZATION_NAME":
				$org .= $data;
				break;
			case "SMALLPICTUREURL":
				$picurl .= $data;
				break;
			case "DEFENDER_RANK_ID":
				$ailevel .= $data;
				break;
			}
		}

	}

function endElement($parser, $tagName)
	{
	global $showimages,$insideitem, $tag, $onlinetime, $dimension, $nick, $rank, $level, $profession, $org, $picurl, $ailevel, $afk;
	if ($tagName == "CHARACTER")
		{
		?>
		<tr>
			<td width="62">
			<? if($showimages){ ?><img border="1" src="<?=$picurl;?>"><?}?>
			</td>
			<td valign="top" width="17">&nbsp;</td>
			<td valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" id="table2">
				<tr>
					<td width="198"><b><font face="Verdana" size="1"><a href="http://www.anarchy-online.com/character/bio/d/<?=$dimension;?>/name/<?=$nick;?>" target="_blank"><?=$nick;?></a><? if($afk) echo"| AFK";?></font></b></td>
					<td align="right"><b><font face="Verdana" size="1">online</font></b></td>
				</tr>
				<tr>
					<td width="198"><font face="Verdana" size="1"><?=rtrim($level);?>(<?=rtrim($ailevel);?>) <?=$profession;?></font></td>
					<td align="right"><font face="Verdana" size="1"><?=$onlinetime;?></font></td>
				</tr>
				<tr>
					<td colspan="2"><font face="Verdana" size="1"><?=$rank;?> of <?=$org;?></font></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr noshade color="#000000"></td>
		</tr>
		<tr>
			<td width="62">&nbsp;</td>
			<td width="17">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>		
		<?
		$nick = "";
		$rank = "";
		$level = "";
		$profession = "";
		$org = "";
		$picurl = "";
		$ailevel = "";
		$insideitem = false;
		}
	}

?>
