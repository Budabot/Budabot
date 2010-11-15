<?php

if (preg_match("/^altsadmin add (.+) (.+)$/i", $message, $names))
{
	if ($names[1] == '' || $names[2] == '')
	{
		$syntax_error = true;
		return;
	}

	$name_alt = ucfirst(strtolower($names[1]));
	$name_main = ucfirst(strtolower($names[2]));
	$uid_alt = AoChat::get_uid($name_alt);
	$uid_main = AoChat::get_uid($name_main);
	if (!$uid_alt)
	{
		$msg = "Player <highlight>$name_alt<end> does not exist.";
		$this->send($msg, $sendto);
		return;
	}
	if (!$uid_main)
	{
		$msg = " Player <highlight>$name_main<end> does not exist.";
		$this->send($msg, $sendto);
		return;
	}

	$db->query("SELECT * FROM alts WHERE `alt` = '$name_alt'");
	$row = $db->fObject();
	if ($row->alt == $name_alt)
	{
		$msg = "Player <highlight>$name_alt<end> is already registered as alt from <highlight>$row->main<end>.";
	}
	else
	{
		$db->query("SELECT * FROM alts WHERE `main` = '$name_alt'");
		if ($db->numrows() != 0)
			$msg = "Player <highlight>$name_alt<end> is already registered as main from someone.";
		else
		{
			$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name_alt', '$name_main')");
			$msg = "<highlight>$name_alt<end> has been registered as an alt of $name_main.";
		}

	}
	$this->send($msg, $sendto);
}
elseif (preg_match("/^altsadmin rem (.+) (.+)$/i", $message, $names))
{
	if ($names[1] == '' || $names[2] == '')
	{
		$syntax_error = true;
		return;
	}

	$name_alt = ucfirst(strtolower($names[1]));
	$name_main = ucfirst(strtolower($names[2]));

	$db->query("SELECT * FROM alts WHERE alt = '$name_alt' AND main = '$name_main'");
	if ($db->numrows() != 0)
	{
		Alts::rem_alt($name_main, $name_alt);
		$msg = "<highlight>$name_alt<end> has been deleted from the alt list of <highlight>$name_main.<end>";
	}
	else
	{
		$msg = "Player <highlight>$name_alt<end> not listed as an alt of Player <highlight>$name_main<end>.  Please check the player's !alts listings.";
	}
	$this->send($msg, $sendto);
}
elseif (preg_match("/^altsadmin export (.+)$/i", $message, $arr))
{
	/* the file may only be stored under the current directory */
	$file_name = "./".basename($arr[1]);
	/* do not overwrite existing files */
	if (file_exists($file_name))
	{
		$msg = "<highlight>File already exists, please specify another one.<end>";
		$this->send($msg, $sendto);
		return;
	}

	/* get the complete alts list */
	$db->query("SELECT * FROM alts");
	$alts_table = $db->fObject("all");

	/* write it to a file */
	$file = fopen($file_name, 'w');
	fwrite($file, "alt main\n");
	foreach ($alts_table as $row)
	{
		fwrite($file, $row->alt.' '.$row->main."\n");
	}
	fclose($file);

	$msg = "Export completed into '$file_name'";
	$this->send($msg, $sendto);
	return;
}

elseif (preg_match("/^altsadmin import (.+)/i", $message, $arr))
{
	/* the file may only be stored under the current directory */
	$file_name = "./".basename($arr[1]);
	/* check for existing file */
	if (!file_exists($file_name))
	{
		$msg = "<highlight>File '$file_name' does not exist.<end>";
		$this->send($msg, $sendto);
		return;
	}

	/* open the file */
	$file = fopen($file_name, 'r');

	/* get first line and check for "alt main" */
	$firstline = fgets($file);
	if (stripos($firstline, "alt main") === false)
	{
		$msg = "File didn't start with expected 'alt main', aborting import.";
		$this->send($msg, $sendto);
		return;
	}
	
	$altcounter = 0;
	while (!feof($file))
	{
		$line = fgets($file);
		$explodeline = explode(' ', $line);
		$name_alt = $explodeline[0];
		$name_main = $explodeline[1];
		$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name_alt', '$name_main')");
		++$altcounter;
	}
	$msg = "Succesfully added $altcounter entries into the alts table.";
	$this->send($msg, $sendto);
	return;
}

else
{
	$syntax_error = true;
}

?>