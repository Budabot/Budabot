<?
$MODULE_NAME = "BANK_MODULE";
$PLUGIN_VERSION = 0.1;

// Bank browse
bot::command("guild", "$MODULE_NAME/bankbrowse.php", "bank", "all", "Browse the Org Bank.");
bot::command("priv", "$MODULE_NAME/bankbrowse.php", "bank", "all", "Browse the Org Bank.");
bot::command("msg", "$MODULE_NAME/bankbrowse.php", "bank", "mod", "Browse the Org Bank.");
// Backpack browse
bot::command("guild", "$MODULE_NAME/backpackbrowse.php", "pack", "all", "Browse an Org Bank backpack.");
bot::command("priv", "$MODULE_NAME/backpackbrowse.php", "pack", "all", "Browse an Org Bank backpack.");
bot::command("msg", "$MODULE_NAME/backpackbrowse.php", "pack", "all", "Browse an Org Bank backpack.");
// Bank lookup
bot::command("guild", "$MODULE_NAME/banklookup.php", "id", "all", "Look up an item.");
bot::command("priv", "$MODULE_NAME/banklookup.php", "id", "all", "Look up an item.");
bot::command("msg", "$MODULE_NAME/banklookup.php", "id", "all", "Look up an item.");
// Bank search
bot::command("guild", "$MODULE_NAME/banksearch.php", "find", "all", "Search the Org Bank for an item you need.");
bot::command("priv", "$MODULE_NAME/banksearch.php", "find", "all", "Search the Org Bank for an item you need.");
bot::command("msg", "$MODULE_NAME/banksearch.php", "find", "all", "Search the Org Bank for an item you need.");
// Help
bot::help("bank", "$MODULE_NAME/bank.txt", "all", "How to search for an item.", "Bank Search Module"); 


// Thanks to Xyphos (RK1) for helping me bugfix
?>