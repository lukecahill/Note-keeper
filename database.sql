CREATE DATABASE notes;

CREATE TABLE note (
    NoteId INT(11) AUTO_INCREMENT PRIMARY KEY,
    NoteText VARCHAR(255),
    NoteTags VARCHAR(512),
	NoteTitle VARCHAR(50),
	NoteComplete TINYINT,
	UserId VARCHAR(60),
	FOREIGN KEY (UserId) REFERENCES note_users(UserId) ON DELETE CASCADE
);

CREATE TABLE note_users (
    UserId VARCHAR(60) PRIMARY KEY,
    UserEmail VARCHAR(255),
    UserPassword VARCHAR(512),
	TagColor VARCHAR(6)
);