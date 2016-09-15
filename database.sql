CREATE DATABASE notes;

USE notes;

CREATE TABLE note_users (
    UserId CHAR(32) PRIMARY KEY,
    UserEmail VARCHAR(255),
    UserPassword VARCHAR(512),
    EmailConfirmation CHAR(32),
	Active TINYINT DEFAULT '0',
	RecentIps VARCHAR(255),
	JsonAuthentication VARCHAR(128),
	PasswordAttempts TINYINT DEFAULT 0
);

CREATE TABLE user_preferences (
	PreferenceId INT(4) PRIMARY KEY AUTO_INCREMENT,
	UserId VARCHAR(60),
    TagColor VARCHAR(6) DEFAULT 'red',
	Pagination TINYINT DEFAULT '1',
	NoteOrder VARCHAR(15) DEFAULT 'oldest',
	SearchOptions VARCHAR(80) DEFAULT 'a:3:{i:0;s:4:"true";i:1;s:4:"true";i:2;s:5:"false";}',
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);

CREATE TABLE note (
    NoteId INT(11) AUTO_INCREMENT PRIMARY KEY,
    NoteText VARCHAR(1024),
    NoteTags VARCHAR(1024),
	NoteTitle VARCHAR(256),
	NoteComplete TINYINT NULL DEFAULT '0',
	UserId VARCHAR(60),
	NoteLastEdited TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);