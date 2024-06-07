-- Created a database called calliope

--code for creating the users table which stores the user's details like email, password and subscription plan
create table users (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    email varchar(300) not null,
    fname varchar(300) not null,
    pass varchar(300) not null,
    verification varchar(300) not null,
    typofsubscription varchar(300) not null,
    subscriptionstat varchar(300) not null,
    dateandtime varchar(500) not null,
    iv varchar(10000) not null
);

--code for creating the  verification table which stores the verification number for a given user
--it allows the verification system to work
create table verification (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    email varchar(300) not null,
    verificationnum varchar(300) not null,
    attempts varchar(300) not null,
    iv varchar(10000) not null
);

-- code for creating the secrets table which will store all the API keys and our key for encrypting data
create table secrets (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    keyname varchar(300) not null,
    actualkey varchar(300) not null
);

-- code for creating the templates table which which will store the templates of the users
create table templates (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    email varchar(300) not null,
    title varchar(300) not null,
    textt text not null,
    datentimeinteger varchar(300) not null,
    iv varchar(10000) not null
);

-- code for creating the resumes table which will store the templates of the users
create table resumes (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    email varchar(300) not null,
    resumename varchar(1000) not null,
    resumelocation varchar(1000) not null,
    resumetext text not null,
    datentimeinteger varchar(800) not null,
    iv varchar(10000) not null
);

-- code for creating the professor website (used for only research emails) database which will store the websites of the professor (primary links)
create table profwebpages (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    professorname varchar(200) not null,
    linktowebsite varchar(1000) not null,
    notestext text not null, -- contains text of the other pages based on the professor as well,  has synthesized that information for that professor; like it has written notes on his research, autobiography etc
    iv varchar(10000) not null
);

-- code for webpage database (used for both corporate and research emails) (all links --> primary and secondary);  all the publications are in here as well 
create table webpages (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    linktowebsite varchar(1000) not null,
    webtext text not null,
    iv varchar(10000) not null
);