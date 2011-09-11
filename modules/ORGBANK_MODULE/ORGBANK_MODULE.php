<?php
      /*
   ** Author: Elimeta of Team_Eli (RK2)
   ** Description: Org Bank
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Many thanks to Lucier (RK1), without who's bank module, Org Bank
   ** would not exist.
   **
   ** Date(created): 27.04.2011
   ** Date(last modified): 20.04.2011
   **
   ** See end of file for changelog. 
   */

$MODULE_NAME = "ORGBANK_MODULE";

// If you have made a save of the orgbank from a previous version, you can 
// reload it from here. Make sure the file is called orgbank.sql
// Be sure to // it after you have done it once, or you will repeat-fill the bank. 
// DB::loadSQLFile($MODULE_NAME, "orgbank");
//
// I believe you can also use !loadsql ORGBANK_MODULE orgbank to do the same thing, though 
// you might have to create the table first by running the module first.

DB::loadSQLFile($MODULE_NAME, "orgbank");

// Basic commands to get to your bank
Command::register($MODULE_NAME, "", "bankstart.php", "bank", "all", "Visit your bank home page");  
Command::register($MODULE_NAME, "", "bankstart.php", "orgbank", "all", "Visit your bank home page");  

// non-Admin delete of own bank.
Command::register($MODULE_NAME, "", "bankmake.php", "bankkill", "all", "Manual bank delete command");

// Admin only bank-killer...
Command::register($MODULE_NAME, "", "bankadmin.php", "bankadmin", "admin", "Admin-only Bank remover");
Command::register($MODULE_NAME, "", "adminkill.php", "adminkill", "admin", "Admin-only Bank remover");

// Commands to add & manually delete items from/to the Bank
Command::register($MODULE_NAME, "", "bankaddtab.php", "bankadd", "all", "Add items to a Bank");
Command::register($MODULE_NAME, "", "bankdel.php", "bankdel", "all", "Manual delete of item in Bank");

// Command for changing tab names in Tabbed Bank. 
Command::register($MODULE_NAME, "", "banktabname.php", "banktabname1", "all", "Change the name of Tab1");
Command::register($MODULE_NAME, "", "banktabname.php", "banktabname2", "all", "Change the name of Tab2");
Command::register($MODULE_NAME, "", "banktabname.php", "banktabname3", "all", "Change the name of Tab3");
Command::register($MODULE_NAME, "", "banktabname.php", "banktabname4", "all", "Change the name of Tab4");
Command::register($MODULE_NAME, "", "banktabname.php", "banktabname5", "all", "Change the name of Tab5");

// Buttons for editing existing item quanities in Banks.
Command::register($MODULE_NAME, "", "bankbuttons.php", "bankM", "all", "[-] button in Home Page list");
Command::register($MODULE_NAME, "", "bankbuttons.php", "bankD", "all", "[DELETE] button in Home Page list");
Command::register($MODULE_NAME, "", "bankbuttons.php", "bankP", "all", "[+] button in Home Page list");

// Various search commands for users. 
Command::register($MODULE_NAME, "", "banksearch.php", "banksearch", "all", "Goto Players Bank");
Command::register($MODULE_NAME, "", "bankitem.php", "bankitem", "all", "Search Bank by Item name");
Command::register($MODULE_NAME, "", "banklist.php", "banklist", "all", "List all Players with Banks");

// The Bank menu open and close commands. 
// Bankmenu left as a legacy command. 
Command::register($MODULE_NAME, "", "bankmenu.php", "bankmenu", "all", "Manually open/close settings.");

// Changing your banks title.
Command::register($MODULE_NAME, "", "banktitle.php", "banktitle", "all", "Change the title of your Bank");	
Command::register($MODULE_NAME, "", "bankcomment.php", "bankcomment", "all", "Change the comment of your Bank");	

//Aliases. Lots of them. Very convenient
CommandAlias::register($MODULE_NAME, "bankdel", "bd");
CommandAlias::register($MODULE_NAME, "bankadd", "ba");
CommandAlias::register($MODULE_NAME, "bankkill", "bk");
CommandAlias::register($MODULE_NAME, "banktitle", "bt");
CommandAlias::register($MODULE_NAME, "bankcomment", "bc");
CommandAlias::register($MODULE_NAME, "banksearch", "bs");
CommandAlias::register($MODULE_NAME, "bankitem", "bi");
CommandAlias::register($MODULE_NAME, "banklist", "bl");
CommandAlias::register($MODULE_NAME, "banksearch", "bs");
CommandAlias::register($MODULE_NAME, "bankmenu", "bm");
CommandAlias::register($MODULE_NAME, "banktabname1", "btn1");
CommandAlias::register($MODULE_NAME, "banktabname2", "btn2");
CommandAlias::register($MODULE_NAME, "banktabname3", "btn3");
CommandAlias::register($MODULE_NAME, "banktabname4", "btn4");
CommandAlias::register($MODULE_NAME, "banktabname5", "btn5");

// And a few help files. 
Help::register($MODULE_NAME, "bank", "bank.txt", "all", "How to get to your bank");
Help::register($MODULE_NAME, "banktabname", "banktabname.txt", "all", "Change tab names in your Bank");
Help::register($MODULE_NAME, "bankaddtab", "bankaddtab.txt", "all", "How to add items to your Bank");
Help::register($MODULE_NAME, "bankdel", "bankdel.txt", "all", "How to delete items from your bank");
Help::register($MODULE_NAME, "bankitem", "bankitem.txt", "all", "Finding bank items by searching");
Help::register($MODULE_NAME, "banksearch", "banksearch.txt", "all", "Finding bank owners by searching");
Help::register($MODULE_NAME, "banktitle", "banktitle.txt", "all", "Setting a title for your bank");
Help::register($MODULE_NAME, "bankcomment", "bankcomment.txt", "all", "Setting a comment for your bank");
Help::register($MODULE_NAME, "banklist", "banklist.txt", "all", "Listing all players with Banks.");
Help::register($MODULE_NAME, "bankcommands", "bankcommands.txt", "all", "List all bank commands");
	
?>
