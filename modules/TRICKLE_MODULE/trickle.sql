CREATE TABLE IF NOT EXISTS trickle ( groupName VARCHAR(20) NOT NULL, name VARCHAR(30) NOT NULL, amountAgi DECIMAL(3,1) NOT NULL, amountInt DECIMAL(3,1) NOT NULL, amountPsy DECIMAL(3,1) NOT NULL, amountSta DECIMAL(3,1) NOT NULL, amountStr DECIMAL(3,1) NOT NULL, amountSen DECIMAL(3,1) NOT NULL );
DELETE FROM trickle;

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Body dev', 0, 0, 0, 1, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Nano pool', 0, .1, .7, .1, 0, .1);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Martial arts', .5, 0, .3, 0, .2, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Brawling', 0, 0, 0, .4, .6, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Riposte', .5, 0, 0, 0, 0, .5);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Dimach', 0, 0, .2, 0, 0, .8);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Adventuring', .5, 0, 0, .3, .2, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Body', 'Swimming', .2, 0, 0, .6, .2, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', '1h Blunt', .1, 0, 0, .4, .5, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', '2h Blunt', 0, 0, 0, .5, .5, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', '1h Edged', .4, 0, 0, .3, .3, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', '2h Edged', 0, 0, 0, .4, .6, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Piercing', .5, 0, 0, .3, .2, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Melee Energy', 0, .5, 0, .5, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Parry', .2, 0, 0, 0, .5, .3);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Sneak attack', 0, 0, .2, 0, 0, .8);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Fast attack', .6, 0, 0, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Melee', 'Multi melee', .6, 0, 0, .1, .3, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Misc weapons', 'Sharp objects', .6, 0, 0, 0, .2, .2);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Misc weapons', 'Grenade', .4, .2, 0, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Misc weapons', 'Heavy weapons', .6, 0, 0, 0, .4, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Bow', .4, 0, 0, 0, .2, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Pistol', .6, 0, 0, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Assault rifle', .3, 0, 0, .4, .1, .2);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'MG/SMG', .3, 0, 0, .3, .3, .1);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Shotgun', .6, 0, 0, 0, .4, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Rifle', .6, 0, 0, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Ranged energy', 0, .2, .4, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Fling shot', 1, 0, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Aimed shot', 0, 0, 0, 0, 0, 1);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Burst', .5, 0, 0, .2, .3, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Full auto', 0, 0, 0, .4, .6, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Bow special', .5, 0, 0, 0, .1, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Ranged', 'Multi ranged', .6, .4, 0, 0, 0, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Melee. Init.', .1, .1, .2, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Ranged. Init.', .1, .1, .2, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Physic. Init.', .1, .1, .2, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'NanoC. Init.', .4, 0, 0, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Dodge-Rng', .5, .2, 0, 0, 0, .3);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Evade-ClsC', .5, .2, 0, 0, 0, .3);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Duck-Exp', .5, .2, 0, 0, 0, .3);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Nano Resist', 0, .2, .8, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Speed', 'Run Speed', .4, 0, 0, .4, .2, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Mech engi', .5, .5, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Electric engi', .3, .5, 0, .2, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Quantum FT', 0, .5, .5, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Weapon smith', 0, .5, 0, 0, .5, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Pharma tech', .2, .8, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Nano prog', 0, 1, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Comp lit', 0, 1, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Psychology', 0, .5, 0, 0, 0, .5);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Chemistry', 0, .5, 0, .5, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Trade & Repair', 'Tutoring', 0, .7, .1, 0, 0, .2);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'TS', .2, .8, 0, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'MC', 0, .8, 0, .2, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'BioMet', 0, .8, .2, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'MatMet', 0, .8, .2, 0, 0, 0);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'PsyMod', 0, .8, 0, 0, 0, .2);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Nano skills', 'SI', 0, .8, 0, 0, .2, 0);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Aiding', 'First aid', .3, .3, 0, 0, 0, .4);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Aiding', 'Treatment', .3, .5, 0, 0, 0, .2);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Spying', 'Concealment', .3, 0, 0, 0, 0, .7);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Spying', 'Break & enter', .4, 0, .3, 0, 0, .3);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Spying', 'Trap disarm', .2, .2, 0, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Spying', 'Perception', 0, .3, 0, 0, 0, .7);

INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Navigation', 'Vehicle air', .2, .2, 0, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Navigation', 'Vehicle ground', .2, .2, 0, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Navigation', 'Vehicle water', .2, .2, 0, 0, 0, .6);
INSERT INTO trickle (groupName, name, amountAgi, amountInt, amountPsy, amountSta, amountStr, amountSen) VALUES('Navigation', 'Map navigation', 0, .4, .1, 0, 0, .5);
