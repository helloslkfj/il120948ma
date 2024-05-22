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