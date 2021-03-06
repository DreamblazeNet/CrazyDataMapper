CREATE TABLE IF NOT EXISTS "accounts" (
  "id" int(11) PRIMARY KEY,
  "name" varchar(40) NOT NULL DEFAULT '',
  "password" varchar(255) NOT NULL DEFAULT '',
  "last_login" datetime DEFAULT NULL
);
CREATE TABLE IF NOT EXISTS "characters" (
  "id" INTEGER PRIMARY KEY,
  "account_id" int(11)  NOT NULL,
  "name" varchar(40) NOT NULL DEFAULT '',
  "level" int(11) NOT NULL DEFAULT '1',
  "money" int(11) NOT NULL DEFAULT '0'
);
CREATE TABLE IF NOT EXISTS "guild_members" (
  "guild_id" int(11) NOT NULL,
  "character_id" int(11)  NOT NULL,
  "rank" int(11) DEFAULT NULL,
  PRIMARY KEY ("guild_id","character_id")
);
CREATE TABLE IF NOT EXISTS "guilds" (
  "id" INTEGER PRIMARY KEY,
  "leader_id" int(10)  DEFAULT NULL,
  "name" varchar(40) NOT NULL DEFAULT '',
  "money" int(11) NOT NULL DEFAULT '0'
);