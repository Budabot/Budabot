DROP TABLE IF EXISTS bbin_chatlist_<myname>;
CREATE TABLE bbin_chatlist_<myname> (`name` CHAR(25), `faction` CHAR(10), `profession` CHAR(20), `guild` CHAR(255), `breed` CHAR(25), `level` INT, `ai_level` INT, `afk` VARCHAR(255) DEFAULT '0', `guest` INT DEFAULT '0', `dimension` INT DEFAULT '0', `ircrelay` CHAR(25), PRIMARY KEY (name, dimension));
