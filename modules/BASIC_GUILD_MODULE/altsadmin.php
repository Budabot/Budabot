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
	return;
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
	$uid_main = $this->get_uid($name_main);
	if (!$uid_main)
	{
		$msg = " Player <highlight>$name_main<end> does not exist.";
		$this->send($msg, $sendto);
		return;
	}

	$db->query("SELECT * FROM alts WHERE alt = '$name_alt' AND main = '$name_main'");
	if ($db->numrows() != 0)
	{
		$db->query("DELETE FROM alts WHERE main = '$name_main' AND alt = '$name_alt'");
		$msg = "<highlight>$name_alt<end> has been deleted from the alt list of <highlight>$name_main.<end>";
	}
	else
	{
		$msg = "Player <highlight>$name_alt<end> not listed as an alt of Player <highlight>$name_main<end>.  Please check the player's !alts listings.";
	}
	$this->send($msg, $sendto);
	return;

}

else
{
	$syntax_error = true;
}

?>