# Dumped using Vhab's Magical MMDB Tool 1.2
# Dumped on: 3/26/2010 at 6:55:47 PM

DROP TABLE IF EXISTS mmdb_data;
CREATE TABLE mmdb_data (category INT(10) NOT NULL, entry INT(10) NOT NULL, message TEXT NOT NULL);
INSERT INTO mmdb_data VALUES (501, 181448347, '%s kicked from organization (alignment changed).');
INSERT INTO mmdb_data VALUES (501, 192568104, 'WARNING: City upkeep (%d credits) is due in %d hours but the org bank only contains %d credits. If the bank does not contain enough credits by the due date, your city will be demolished.');
INSERT INTO mmdb_data VALUES (501, 193456776, 'The organization has a tax of %u #1{1: credit | credits}.');
INSERT INTO mmdb_data VALUES (501, 220373365, 'The organization tax has been changed to %u #1{1: credit. | credits. }  Do you wish to leave your organization?');

INSERT INTO mmdb_data VALUES (506, 12753364, 'The %s organization %s just entered a state of war! %s attacked the %s organization %s''s tower in %s at location (%d,%d).');
INSERT INTO mmdb_data VALUES (506, 24174231, '%s just initiated an attack on playfield %i at location (%d,%d). That area is controlled by %s. All districts controlled by your organization are open to attack! You are in a state of war. Leader chat informed.');
INSERT INTO mmdb_data VALUES (506, 94492169, 'The tower %s in %s was just reduced to %d %% health by %s from the %s organization!');
INSERT INTO mmdb_data VALUES (506, 118352306, '%s just initiated an attack in %s at location (%d,%d). That area is controlled by %s. All districts controlled by your organization are open to attack! You are in a state of war. Leader chat informed.');
INSERT INTO mmdb_data VALUES (506, 147506468, 'Notum Wars Update: The %s organization %s lost their base in %s.');
INSERT INTO mmdb_data VALUES (506, 224009576, 'The tower %s in %s was just reduced to %d %% health by %s!');
INSERT INTO mmdb_data VALUES (506, 265658836, 'The tower %s in %s was just reduced to %d %% health!');

INSERT INTO mmdb_data VALUES (508, 17467336, 'Your controller tower in %s in %s has had its defense shield disabled by %s (%s).');
INSERT INTO mmdb_data VALUES (508, 20908201, '%s removed inactive character %s from your organization.');
INSERT INTO mmdb_data VALUES (508, 37093479, '%s kicked %s from your organization.');
INSERT INTO mmdb_data VALUES (508, 45978487, '%s just left your organization.');
INSERT INTO mmdb_data VALUES (508, 134643352, 'Governing form changed to ''%s''.');
INSERT INTO mmdb_data VALUES (508, 138965334, '%s changed governing form to ''%s''.');
INSERT INTO mmdb_data VALUES (508, 147071208, 'GM removed character %s from your organization.');
INSERT INTO mmdb_data VALUES (508, 173558247, '%s invited %s to your organization.');
INSERT INTO mmdb_data VALUES (508, 176308692, 'Blammo! %s has launched an orbital attack!');
INSERT INTO mmdb_data VALUES (508, 192652356, '%d credits have been deducted from the organization bank for the upkeep of your city. Next payment is due in %d days.');
INSERT INTO mmdb_data VALUES (508, 196585349, 'Your city upkeep payment of %d credits is due in %d hour(s). Please make sure the full upkeep amount is available in the organization bank or you will lose your city.');
INSERT INTO mmdb_data VALUES (508, 241047288, 'Leadership has been given to %s.');

INSERT INTO mmdb_data VALUES (1001, 1, '%s turned the cloaking device in your city %s.');
INSERT INTO mmdb_data VALUES (1001, 2, 'Your radar station is picking up alien activity in the area surrounding your city.');
INSERT INTO mmdb_data VALUES (1001, 3, 'Your city in %s has been targeted by hostile forces.');
INSERT INTO mmdb_data VALUES (1001, 4, '%s removed the organization headquarters in %s!  All personal belongings or houses in the city were instantly destroyed!');
INSERT INTO mmdb_data VALUES (1001, 5, '%s has initiated removal of a %s in %s!  The house and all belongings in the house will be destroyed in 2 minutes.');
INSERT INTO mmdb_data VALUES (1001, 6, '%s removed a %s in %s!  All benefits from this house and all belongings in the house were instantly destroyed!');
INSERT INTO mmdb_data VALUES (1001, 7, '%s has initiated removal of the HQ in %s!  The house and all belongings in the house will be destroyed in 2 minutes. This will also cause the other houses in the city to be deleted!');
