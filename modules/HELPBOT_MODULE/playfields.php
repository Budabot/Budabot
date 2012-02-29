<?php

if (preg_match("/^playfields (.+)$/i", $message, $arr)) {
  $search = strtolower(trim($arr[1]));
  $data = $db->query("SELECT * FROM playfields WHERE lower(long_name) LIKE ? OR lower(short_name) LIKE ?", '%' . $search . '%', '%' . $search . '%');

  $count = count($data);

  if ($count > 1) {
     $blob = "Result of Playfield Search for '$search'\n\n";
     $blob .= "There are $count matches to your query.\n\n";
     forEach ($data as $row) {
       $blob .= "<green>$row->long_name<end> has ID <yellow>$row->id<end>\n\n";
     }
      
    $msg = Text::make_blob("Playfields ($count)", $blob);
  } else if ($count == 1) {
    $row = $data[0];
    $msg = "<green>$row->long_name<end> has ID <yellow>$row->id<end>";
  } else {
    $msg = "There were no matches for your search.";
  }
  $sendto->reply($msg);
   
}else if (preg_match("/^playfields$/i", $message)) {
	$blob = '';
	
	$sql = "SELECT * FROM playfields ORDER BY long_name";
	$data = $db->query($sql);
	forEach ($data as $row) {
		$blob .= "{$row->id}   <green>{$row->long_name}<end>   <cyan>({$row->short_name})<end>\n";
	}
	
	$msg = Text::make_blob("Playfields", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>