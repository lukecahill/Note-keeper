CREATE DATABASE notes;

USE notes;

CREATE TABLE note_users (
    UserId VARCHAR(60) PRIMARY KEY,
    UserEmail VARCHAR(255),
    UserPassword VARCHAR(512),
    TagColor VARCHAR(6) DEFAULT 'red'
);

CREATE TABLE note (
    NoteId INT(11) AUTO_INCREMENT PRIMARY KEY,
    NoteText VARCHAR(1024),
    NoteTags VARCHAR(1024),
	NoteTitle VARCHAR(256),
	NoteComplete TINYINT NULL DEFAULT '0',
	UserId VARCHAR(60),
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);