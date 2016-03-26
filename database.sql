CREATE DATABASE notes;

CREATE TABLE note (
    NoteId INT(11) AUTO_INCREMENT PRIMARY KEY,
    NoteText VARCHAR(255),
    NoteTags VARCHAR(512),
	NoteTitle VARCHAR(50),
	NoteComplete TINYINT
);

CREATE TABLE note_users (
    UserId INT(11) AUTO_INCREMENT PRIMARY KEY,
    UserEmail VARCHAR(255),
    UserPassword VARCHAR(512)
);