
--
-- This file creates the tables for the property management project
-- and populates them with at least 5 tuples each.

--drops all tables from last time the database was used
drop table Lawyer cascade constraints;
drop table Buyer cascade constraints;
drop table Seller cascade constraints;
drop table Agent cascade constraints;
drop table PropertyAddress cascade constraints;
drop table Property cascade constraints;
drop table Tenant cascade constraints;
drop table Disclosures cascade constraints;
drop table BuildingManager cascade constraints;
drop table Apartment2 cascade constraints;
drop table Apartment1 cascade constraints;
drop table House cascade constraints;
drop table Owns cascade constraints;

drop sequence pid_sequence;

-- table creations
CREATE TABLE Lawyer(
	name    VARCHAR(40), -- all varchars need a default specified length but the length can vary
	firm    VARCHAR(40),
	email   VARCHAR(40) primary key);

grant select on Lawyer to public;

CREATE TABLE Buyer(
	email	VARCHAR(40) PRIMARY KEY,
	name	VARCHAR(40),
	L_email	VARCHAR(40),
    FOREIGN KEY(L_email) REFERENCES Lawyer(email) ON DELETE SET NULL);

CREATE TABLE Seller(
	email	VARCHAR(40) PRIMARY KEY,
	name 	VARCHAR(40),
	L_email	VARCHAR(40),
	FOREIGN KEY(L_email) REFERENCES Lawyer(email) ON DELETE SET NULL);

CREATE TABLE Agent(
	email	            VARCHAR(40) PRIMARY KEY,
	name	            VARCHAR(40),
	password			VARCHAR(40),
	licenseRenewalDate	DATE);

CREATE TABLE PropertyAddress(
	postalCode		CHAR(7) PRIMARY KEY,
	city			VARCHAR(40) NOT NULL,
	province		VARCHAR(40) NOT NULL);

CREATE TABLE Property(
	pID			    INT PRIMARY KEY,
	numBed		    INT NOT NULL,
	numBath		    FLOAT NOT NULL,
	govtValuation	DECIMAL(19,2),
	sqft			FLOAT NOT NULL,
	postalCode		CHAR(7) NOT NULL,
	streetNum		VARCHAR(40) NOT NULL,
	salePrice		DECIMAL(19,2),
	dateOfSale		DATE,
	listDate		DATE NOT NULL,
	listPrice		DECIMAL(19,2) NOT NULL,
	B_email		    VARCHAR(40),
	A_email		    VARCHAR (40) NOT NULL,
	FOREIGN KEY (postalCode) REFERENCES PropertyAddress,
	FOREIGN KEY (B_email) REFERENCES Buyer ON DELETE SET NULL,
	FOREIGN KEY (A_email) REFERENCES Agent ON DELETE SET NULL);

CREATE TABLE Owns(
	pID				INT,
	S_email			VARCHAR(40),
	boughtPrice		DECIMAL(19,2),
	dateOfPurchase	DATE,
	p_status		VARCHAR(40),
	PRIMARY KEY (pID, S_email),
	FOREIGN KEY (pID) REFERENCES Property ON DELETE CASCADE,
	FOREIGN KEY (S_email) REFERENCES Seller(email));

CREATE TABLE Tenant(
	email			VARCHAR(40) PRIMARY KEY,
	name			VARCHAR(40),
	pID			    INT UNIQUE NOT NULL,
	monthlyRent		DECIMAL (19,2),
	leaseType		VARCHAR(40),
	leaseSignDate	DATE,
	leaseLength		INT,
	FOREIGN KEY(pID) REFERENCES Property);

CREATE TABLE Disclosures(
	dID			INT,
	type		INT,
	location	VARCHAR(40),
	dateLogged	DATE,
	resolved	INT, --wouldn't accept boolean; may try to find better type later
	pID			INT,
	PRIMARY KEY(dID, pID),
	FOREIGN KEY (pID) REFERENCES Property ON DELETE CASCADE);

CREATE TABLE BuildingManager(
	email       VARCHAR(40) PRIMARY KEY,
	companyName	VARCHAR(40));

CREATE TABLE Apartment2(
	aptNum		INT PRIMARY KEY,
	floorNum	INT NOT NULL);

CREATE TABLE Apartment1(
	pID			    INT PRIMARY KEY,
	aptNum		    INT NOT NULL,
	maintenanceFee	DECIMAL(19,2),
	BM_email		VARCHAR(40) NOT NULL,
	FOREIGN KEY (pID) 	REFERENCES Property,
	FOREIGN KEY (BM_email) REFERENCES BuildingManager(email),
    FOREIGN KEY (aptNum) REFERENCES Apartment2);

CREATE TABLE House(
	pID			    INT PRIMARY KEY,
	yardSize		FLOAT,
	FOREIGN KEY (pID) REFERENCES Property);

-- sequence for property inserting next pID
CREATE SEQUENCE pid_sequence
INCREMENT BY 2 -- number added every time
START WITH 100 -- number starting
NOMAXVALUE -- no max value
NOCYCLE -- adding continuously, no loop
CACHE 10;

-- insert statements to populate tables

-- lawyer insertions
INSERT INTO Lawyer
VALUES('John Smith', 'Baker Mckenzie', 'johnsmith1@bakermckenzie.com');
INSERT INTO Lawyer
VALUES('Sara Smith', 'Baker McKenzie', 'sarasmith@bakermckenzie.com');
INSERT INTO Lawyer
VALUES('John Smith', 'johnsmith@gmail.com', 'johnsmith2@bakermckenzie.com');
INSERT INTO Lawyer
VALUES('Donald Trump', 'Temple Chambers', 'donaldtrump@templechambers.com');
INSERT INTO Lawyer
VALUES('Donald Duck', 'Mickey Mouse and Son', 'donaldduck@mickeymouse.com');

-- buyer insertions
INSERT INTO Buyer
VALUES('johnsmith@gmail.com', 'John Smith', 'johnsmith2@bakermckenzie.com');
INSERT INTO Buyer
VALUES('joeforte@gmail.com', 'Joe Forte', 'johnsmith2@bakermckenzie.com');
INSERT INTO Buyer
VALUES('johnsmith42@gmail.com', 'John Smith', 'sarasmith@bakermckenzie.com');
INSERT INTO Buyer
VALUES('donalduck@gmail.com', 'Donald Duck', 'donaldduck@mickeymouse.com');
INSERT INTO Buyer
VALUES('alicebaker@gmail.com', 'Alice Baker', 'johnsmith1@bakermckenzie.com');

-- seller insertions
INSERT INTO Seller
VALUES('alicesmith@gmail.com', 'Alice Smith', 'johnsmith1@bakermckenzie.com');
INSERT INTO Seller
VALUES('bobshaw@gmail.com', 'Bob Shaw', 'johnsmith2@bakermckenzie.com');
INSERT INTO Seller
VALUES('xialong@baidu.com', 'Cherry Li', 'sarasmith@bakermckenzie.com');
INSERT INTO Seller
VALUES('donalduck@gmail.com', 'Donald Duck', 'donaldduck@mickeymouse.com');
INSERT INTO Seller
VALUES('emmalau@outlook.com', 'Emma Lau', NULL);

-- agent insertions
INSERT INTO Agent
VALUES('augustusjulius@manderburnsleo.com', 'Augustus Julius', 'secretpass', date '2022-01-01');
INSERT INTO Agent
VALUES('tiberiusclaudius@manderburnsleo.com', 'Tiberius Claudius', 'password', date '2023-01-01');
INSERT INTO Agent
VALUES('domitianusflavius@manderburnsleo.com', 'Domitianus Flavius', 'flavius00', date '2024-01-01');
INSERT INTO Agent
VALUES('genghiskhan@manderburnsleo.com', 'Genghis Khan', 'mongoliarulestheworld', date '2024-01-01');
INSERT INTO Agent
VALUES('commodusaurelius@manderburnsleo.com', 'Commodus Aurelius', 'romeismine', date '2020-01-01');

-- propertyAddress insertions
INSERT INTO PropertyAddress
VALUES('V6T 1Z4', 'Vancouver', 'BC');
INSERT INTO PropertyAddress
VALUES('V5J 0A3', 'Vancouver', 'BC');
INSERT INTO PropertyAddress
VALUES('V5K 0BT', 'Vancouver', 'BC');
INSERT INTO PropertyAddress
VALUES('V5K 004', 'Vancouver', 'BC');
INSERT INTO PropertyAddress
VALUES('S0K 0Y0', 'Saskatoon', 'SK');
INSERT INTO PropertyAddress
VALUES('V6T 2H2', 'Vancouver', 'BC');
INSERT INTO PropertyAddress
VALUES('V3S 0H2', 'Surrey', 'BC');
INSERT INTO PropertyAddress
VALUES('V3Z 04T', 'Surrey', 'BC');
INSERT INTO PropertyAddress
VALUES('H1A 0A1', 'Montreal', 'QC');
INSERT INTO PropertyAddress
VALUES('M4C 1B5', 'Toronto', 'ON');

-- property insertions
INSERT INTO Property
VALUES(pid_sequence.nextval, 2, 2, 220000, 800, 'V6T 1Z4', '105 W 10th', NULL, NULL, date '2019-05-03', 270000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 1, 1.5, 120000, 650, 'V5J 0A3', '#11 Dunbar', NULL, NULL, date '2024-06-06', 200000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 2, 2.5, 400000, 1024, 'V5K 0BT', '#3 W 49', NULL, NULL, date '2023-12-08', 270000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 2, 1, 620000, 900, 'V6T 2H2', '#28 EB 13th', NULL, NULL, date '2021-02-23', 720000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 3, 2, 780000, 1300, 'S0K 0Y0', '#2 Sunny Rd',  NULL, NULL, date '2018-11-02', 850000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 1, 2, 230000, 1200, 'V5K 004', '#123 Main St', 300000, date '2024-07-21', date '2024-01-02', 330000, 'johnsmith@gmail.com', 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 3, 3, 1000000, 1500, 'V3S 0H2', '34 52nd Ave', NULL, NULL, date '2023-07-11', 1200000, NULL, 'genghiskhan@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 4, 3.5, 900000, 1350, 'V3Z 04T', '2842 32nd Ave', NULL, NULL, date '2024-04-01', 925000, NULL, 'commodusaurelius@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 4, 4, 1800000, 2120, 'H1A 0A1', '#123 W 4th', NULL, NULL, date '2022-07-31', 2000000, NULL, 'augustusjulius@manderburnsleo.com');
INSERT INTO Property
VALUES(pid_sequence.nextval, 4, 4.5, 2800000, 3140, 'M4C 1B5', '#54 193 Ave', 3000000, date '2024-02-06', date '2024-05-16', 3200000, 'joeforte@gmail.com', 'augustusjulius@manderburnsleo.com');

-- owns insertions
INSERT INTO Owns
VALUES (100, 'alicesmith@gmail.com', 150000, date '2017-09-02', 'Current Owner');
INSERT INTO Owns
VALUES (102, 'donalduck@gmail.com', 120000, date '2024-05-03', 'Current Owner');
INSERT INTO Owns
VALUES (104, 'donalduck@gmail.com', 325000, date '2021-01-09', 'Current Owner');
INSERT INTO Owns
VALUES (106, 'emmalau@outlook.com', 450000, date '2018-05-03', 'Current Owner');
INSERT INTO Owns
VALUES (108, 'bobshaw@gmail.com', 660000, date '2017-03-05', 'Current Owner');
INSERT INTO Owns
VALUES (110, 'xialong@baidu.com', 150000, date '2020-05-03', 'Former Owner');
INSERT INTO Owns
VALUES (112, 'donalduck@gmail.com', 400000, date '2006-03-05', 'Current Owner');
INSERT INTO Owns
VALUES (114,'emmalau@outlook.com', 825000, date '2022-10-09', 'Current Owner');
INSERT INTO Owns
VALUES (116, 'donalduck@gmail.com', 1000000, date '2013-09-10', 'Current Owner');
INSERT INTO Owns
VALUES (118, 'bobshaw@gmail.com', 1100000, date '2010-02-06', 'Former Owner');


-- tenant insertions
INSERT INTO Tenant
VALUES('adamjames@hotmail.com', 'Adam James', 100, 5040.12, 'Monthly', date '2019-06-03', 14);
INSERT INTO Tenant
VALUES('bethanylau@gmail.com', 'Bethany Lau', 102, 6000.00, 'Annually', date '2024-06-10', 24);
INSERT INTO Tenant
VALUES('cassiebones@yahoo.com', 'Cassandra Bones', 104, 8000, 'Monthly', date '2023-12-25', 6);
INSERT INTO Tenant
VALUES('lepetitdominique@hotmail.com', 'Dominique Rousseau', 106, 5040.12, 'Annually', date '2020-06-28', 36);
INSERT INTO Tenant
VALUES('thegreatbanana@gmail.com', 'Esther James', 108, 1234.56, 'Monthly', date '2019-02-03', 2);

-- disclosures insertions
INSERT INTO Disclosures
VALUES(1, 1001, 'bathroom #1', date '2019-05-03', 1, 100);
INSERT INTO Disclosures
VALUES(2, 1002, 'kitchen', date '2019-05-03', 0, 100);
INSERT INTO Disclosures
VALUES(3, 1001, 'bathroom #2', date '2019-05-03', 1, 100);
INSERT INTO Disclosures
VALUES(4, 1004, 'bathroom #3', date '2019-05-03', 1, 100);
INSERT INTO Disclosures
VALUES(5, 1005, 'bathroom #3', date '2019-05-03', 1, 100);
INSERT INTO Disclosures
VALUES(1, 1005, 'master bedroom', date '2024-06-06', 0, 102);
INSERT INTO Disclosures
VALUES(2, 1004, 'yard', date '2024-06-06', 0, 102);

-- buildingManager insertions
INSERT INTO BuildingManager
VALUES('johnsmith@bluemanagement.com', 'Blue Management');
INSERT INTO BuildingManager
VALUES('michaelbay@bluemanagement.com', 'Blue Management');
INSERT INTO BuildingManager
VALUES('cassius_clay@hamptonplace.com', 'Hampton Place');
INSERT INTO BuildingManager
VALUES('drexler_clyde@trailblazer.mgmt.com', 'Trailblazer Management');
INSERT INTO BuildingManager
VALUES('clark.caitlin@atira.com', 'Atira Property Management');

-- apartment2 insertions
INSERT INTO Apartment2
VALUES(302, 3);
INSERT INTO Apartment2
VALUES(006, 0);
INSERT INTO Apartment2
VALUES(808, 8);
INSERT INTO Apartment2
VALUES(100, 1);
INSERT INTO Apartment2
VALUES(501, 5);

-- apartment1 insertions
INSERT INTO Apartment1
VALUES(100, 302, 49.99, 'johnsmith@bluemanagement.com');
INSERT INTO Apartment1
VALUES(102, 006, 35.50, 'michaelbay@bluemanagement.com');
INSERT INTO Apartment1
VALUES(104, 808, 72.00, 'cassius_clay@hamptonplace.com');
INSERT INTO Apartment1
VALUES(110, 100, 27.25, 'drexler_clyde@trailblazer.mgmt.com');
INSERT INTO Apartment1
VALUES(112, 501, 49.99, 'clark.caitlin@atira.com');

-- house insertions
INSERT INTO House
VALUES(106, 80);
INSERT INTO House
VALUES(108, 112);
INSERT INTO House
VALUES(114, 55);
INSERT INTO House
VALUES(116, 200);
INSERT INTO House
VALUES(118, 344);

COMMIT;