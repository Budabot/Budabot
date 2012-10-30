CREATE TABLE IF NOT EXISTS tracked_users_<myname> (`uid` BIGINT NOT NULL PRIMARY KEY, `name` VARCHAR(25) NOT NULL, `added_by` VARCHAR(25) NOT NULL, `added_dt` INT NOT NULL);
