CREATE DATABASE note_keeper;

USE notes;

CREATE TABLE note_announcements (
	NotificationId INT(4) PRIMARY KEY,
	AnnouncementText VARCHAR(1024),
	ShowUntil DATE NOT NULL
);

CREATE TABLE note_users (
    UserId CHAR(32) PRIMARY KEY,
    UserEmail VARCHAR(255),
    UserPassword VARCHAR(512),
    EmailConfirmation CHAR(32),
	Active TINYINT DEFAULT 0,
	RecentIps VARCHAR(255),
	UserLatitude VARCHAR(64),
	UserLongitude VARCHAR(64),
	JsonAuthentication VARCHAR(128),
	PasswordAttempts TINYINT DEFAULT 0
);

CREATE TABLE note_user_preferences (
	PreferenceId INT(4) PRIMARY KEY AUTO_INCREMENT,
	UserId VARCHAR(60),
    TagColor VARCHAR(6) DEFAULT 'red',
	Pagination TINYINT DEFAULT 0,
	NoteOrder VARCHAR(15) DEFAULT 'oldest',
	SearchOptions VARCHAR(80) DEFAULT 'a:3:{i:0;s:4:"true";i:1;s:4:"true";i:2;s:5:"false";}',
	ColorTheme VARCHAR(16) DEFAULT 'light',
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);

CREATE TABLE notes (
    NoteId INT(11) AUTO_INCREMENT PRIMARY KEY,
    NoteText VARCHAR(1024),
    NoteTags VARCHAR(1024),
	NoteTitle VARCHAR(256),
	NoteComplete TINYINT NULL DEFAULT 0,
	UserId VARCHAR(60),
	NoteLastEdited TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);