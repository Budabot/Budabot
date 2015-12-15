DROP TABLE IF EXISTS perk;
CREATE TABLE perk (id INT NOT NULL PRIMARY KEY, name VARCHAR(25) NOT NULL);

DROP TABLE IF EXISTS perk_level;
CREATE TABLE perk_level (id INT NOT NULL PRIMARY KEY, perk_id INT NOT NULL, number INT NOT NULL, min_level INT NOT NULL);

DROP TABLE IF EXISTS perk_level_prof;
CREATE TABLE perk_level_prof (perk_level_id INT NOT NULL, profession VARCHAR(25) NOT NULL);

DROP TABLE IF EXISTS perk_level_buffs;
CREATE TABLE perk_level_buffs (perk_level_id INT NOT NULL, skill VARCHAR(50) NOT NULL, amount INT NOT NULL);