CREATE TABLE users
(
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  username     TEXT NOT NULL,
  email        TEXT,
  salt         TEXT NOT NULL,
  password     TEXT NOT NULL,
  access_level INT                 DEFAULT 0 NOT NULL,
  create_time  TEXT,
  update_time  TEXT,
  lastlogin    TEXT,
  blocked      INT                 DEFAULT 0 NOT NULL
);
CREATE UNIQUE INDEX users_username_uindex ON users (username);
CREATE TABLE passwords
(
  id                INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id           INT  NOT NULL,
  folder_id         INT,
  username          TEXT NOT NULL,
  password          TEXT NOT NULL,
  site_name         TEXT,
  site_description  TEXT,
  site_url          TEXT,
  position          INT                 DEFAULT 0 NOT NULL,
  favorite          INT                 DEFAULT 0 NOT NULL,
  create_time       TEXT,
  update_time       TEXT,
  user_recovery     TEXT,
  password_recovery TEXT
);
CREATE TABLE notes
(
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id       INT  NOT NULL,
  folder_id     INT,
  title         TEXT NOT NULL,
  note_text     TEXT,
  position      INT                 DEFAULT 0,
  favorite      INT                 DEFAULT 0,
  create_time   TEXT,
  update_time   TEXT,
  text_recovery TEXT
);
CREATE TABLE folders
(
  id                  INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id             INTEGER NOT NULL,
  image_id            INTEGER             DEFAULT NULL,
  name                TEXT    NOT NULL,
  description         TEXT,
  position            INT                 DEFAULT 0,
  shared              INT                 DEFAULT 0,
  shared_key_recovery TEXT                DEFAULT NULL
);
CREATE TABLE users_has_folders
(
  id                  INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id             INT,
  folder_id           INT,
  shared_key_for_User TEXT                DEFAULT NULL
);
CREATE TABLE logs
(
  id        INTEGER PRIMARY KEY AUTOINCREMENT,
  level     INT  NOT NULL,
  entry     TEXT NOT NULL,
  timestamp TEXT NOT NULL
);
CREATE TABLE invites
(
  id              INTEGER PRIMARY KEY AUTOINCREMENT,
  owner_user_id   INT  NOT NULL,
  guest_user_id   INT  NOT NULL,
  folder_id       INT  NOT NULL,
  folder_password TEXT NOT NULL,
  invite_key      TEXT NOT NULL,
  create_time     TEXT NOT NULL
);
CREATE TABLE tags
(
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  name         TEXT,
  parenttag_id INT
);
CREATE TABLE user_has_tags
(
  id      INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INT,
  tag_id  INT
);

CREATE TABLE password_has_tags
(
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  password_id INT,
  tag_id      INT
);

CREATE TABLE images
(
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id     INT,
  server_path TEXT NOT NULL,
  name        TEXT NOT NULL,
  public      INT                 DEFAULT 0
);

/* Run this create Statement to migrate the database from 3.0 to 3.2 */
CREATE TABLE favorites
(
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id     INT,
  password_id INT
);