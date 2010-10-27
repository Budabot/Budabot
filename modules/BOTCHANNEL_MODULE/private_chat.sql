CREATE TABLE IF NOT EXISTS members_<myname> (`name` VARCHAR(25) NOT NULL PRIMARY KEY, `autoinv` INT DEFAULT '0');
DROP TABLE IF EXISTS priv_chatlist_<myname>;
CREATE TABLE priv_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `faction` CHAR(10), `profession` CHAR(20), `guild` CHAR(255), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0');