DROP TABLE IF EXISTS `clumpref`;
CREATE TABLE `clumpref` ( `lid` int(11) NOT NULL default '0', `hid` int(11) NOT NULL default '0', `name` varchar(255) NOT NULL default '', `specials` varchar(255) NOT NULL default '', `type` int(11) NOT NULL default '0', `spec` tinyint(4) NOT NULL default '0');
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265321, 265322, 'Kyr''Ozch Bio-Material - Type 18', 'Mongoose (1he), Wolf (2he) and Viper (piercing)', 18, 2);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265323, 265324, 'Kyr''Ozch Bio-Material - Type 34', 'Panther (1hb) and Bear (2hb)', 34, 2);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265325, 265326, 'Kyr''Ozch Bio-Material - Type 812', 'Peregrine (pistol), Hawk (smg) and Tiger (crossbow)', 812, 2);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265327, 265328, 'Kyr''Ozch Bio-Material - Type 687', 'Cobra (rifle), Shark (assault rifle) and Silverback (shotgun)', 687, 2);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265329, 265330, 'Kyr''Ozch Bio-Material - Type 295', 'Adventurers, Enforcers, Martial Artists and Soldiers', 295, 3);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265331, 265332, 'Kyr''Ozch Bio-Material - Type 935', 'Agents, Fixers and Shades', 935, 3);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265333, 265334, 'Kyr''Ozch Bio-Material - Type 64', 'Doctors, Engineers, Keepers and Meta-physicists', 64, 3);
INSERT INTO `clumpref` (`lid`, `hid`, `name`, `specials`, `type`, `spec`) VALUES (265335, 265336, 'Kyr''Ozch Bio-Material - Type 468', 'Bureaucrats, Nano-technicians and Traders', 468, 3);

DROP TABLE IF EXISTS `clumpweapon`;
CREATE TABLE `clumpweapon` ( `type` int(11) NOT NULL default '0', `name` varchar(255) NOT NULL default '', `0` int(11) NOT NULL default '0', `99` int(11) NOT NULL default '0', `100` int(11
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (18, 'Ofab Mongoose Mk', 265152, 264907, 264914, 264921, 264928, 264935, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (18, 'Ofab Viper Mk', 264984, 264991, 264998, 265005, 265012, 265019, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (18, 'Ofab Wolf Mk', 265201, 265208, 265215, 265222, 265229, 265236, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (34, 'Ofab Bear Mk', 265243, 265250, 265257, 265264, 265271, 265278, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (34, 'Ofab Panther Mk', 264942, 264949, 264956, 264963, 264970, 264977, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (687, 'Ofab Cobra Mk', 265285, 265292, 265299, 265306, 265313, 265320, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (687, 'Ofab Shark Mk', 265068, 265075, 265082, 265089, 265096, 265103, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (687, 'Ofab Silverback Mk', 264859, 264870, 264877, 264884, 264891, 264898, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (812, 'Ofab Hawk Mk', 265159, 265166, 265173, 265180, 265187, 265194, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (812, 'Ofab Peregrine Mk', 265026, 265033, 265040, 265047, 265054, 265061, 300);
INSERT INTO `clumpweapon` (`type`, `name`, `0`, `99`, `100`, `199`, `200`, `299`, `300`) VALUES (812, 'Ofab Tiger Mk', 265110, 265117, 265124, 265131, 265138, 265145, 300);
