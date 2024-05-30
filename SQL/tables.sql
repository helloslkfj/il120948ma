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

