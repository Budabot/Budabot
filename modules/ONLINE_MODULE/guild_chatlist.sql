DROP TABLE IF EXISTS guild_chatlist_<myname>;
CREATE TABLE guild_chatlist_<myname> (`name` CHAR(25) PRIMARY KEY, `afk` VARCHAR(255) DEFAULT '0')