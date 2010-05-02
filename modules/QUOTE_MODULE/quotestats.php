<?php
   /*
   ** Author: Lucier (RK1)
   ** Description: Build a stats message for Quotes
   ** Version: 1.3
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 16.03.2007
   ** Date(last modified): 14.06.2007
   */
   
   $top = $this->settings["quote_stat_count"];

   $db->query("SELECT * FROM quote");
   $count = $db->numrows();
   
   //$quoters = setup a list of who quoted the most
   $db->query("SELECT * FROM quote ORDER BY `Who`");
   $quoters = array();
   while($row = $db->fObject()) {
	   if ($row->Who != "") {$quoters[$row->Who]++;}   
   }
   arsort($quoters);

   //$victims = setup a list of who was quoted the most
   $db->query("SELECT * FROM quote ORDER BY `OfWho`");
   $victims = array();
   while($row = $db->fObject()) {
	   if ($row->Who != "") {$victims[$row->OfWho]++;}
   }
   arsort($victims);
   
   $msg = "<header>::::: Quote Stats :::::<end>\n\n<highlight>Top $top Quoters:<end> (".count($quoters)." total)\n";
   $listnum = 0;
   foreach ($quoters as $key => $val) {
	   $listnum++;
	   $msg .= "<tab>$listnum) ".
	   "<a href='chatcmd:///tell ".$this->vars["name"]." quote search $key>$key</a>".
	   ": <highlight>$val<end> ".number_format((100*($val/$count)),0)."%\n";
	   if ($listnum >= $top)
	   	break;
   }

   $msg .= "<br><highlight>Top $top Quoted:<end> (".count($victims)." total)\n";
   $listnum = 0;
   foreach ($victims as $key => $val) {
	   $listnum++;
	   $msg .= "<tab>$listnum) ".
	   "<a href='chatcmd:///tell ".$this->vars["name"]." quote search $key>$key</a>".
	   ": <highlight>$val<end> ".number_format((100*($val/$count)),0)."%\n";
	   if ($listnum >= $top)
	   	break;
   }
   
   $msg = bot::makeLink("Quote stats from (".date("F j, Y, g:i a").")", $msg);
   
   $this->vars["quotestats"]= $msg;
?>
