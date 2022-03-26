drop table Property_Manager cascade constraints;
drop table Service_Worker cascade constraints;
drop table Landlord cascade constraints;
drop table Account cascade constraints;
drop table Property cascade constraints;
drop table Services cascade constraints;
drop table Service_Details cascade constraints;
drop table Tenant cascade constraints;
drop table Roommates_With_Tenant cascade constraints;
drop table Lease_Increase cascade constraints;
drop table Lease cascade constraints;

CREATE TABLE Property_Manager ( 
    userId CHAR(10), 
    password VARCHAR(20),
    propertyManagerName VARCHAR(30), 
    propertyManagerEmail VARCHAR(30), 
    PRIMARY KEY (userId), 
    UNIQUE(propertyManagerEmail)
);

CREATE TABLE Service_Worker ( 
    serviceWorkerEmail VARCHAR(30), 
    serviceWorkerName VARCHAR(30), 
    serviceType VARCHAR(50), 
    PRIMARY KEY (serviceWorkerEmail)
);

CREATE TABLE Landlord (
    customerId CHAR(10),
    landlordEmail VARCHAR(30),
    userId CHAR(10) NOT NULL,
    PRIMARY KEY (customerId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    UNIQUE (landlordEmail)
);

CREATE TABLE Account (
    accountId CHAR(10),
    balance INT,
    customerId CHAR(10),
    PRIMARY KEY (accountId),
    FOREIGN KEY (customerId) REFERENCES Landlord
);

CREATE TABLE Property (
    address VARCHAR(50),
    customerId CHAR(10) NOT NULL,
    propertyType INT,
    PRIMARY KEY (address),
    FOREIGN KEY (customerId) REFERENCES Landlord
);

CREATE TABLE Services (
    serviceWorkerEmail VARCHAR(30),
    address VARCHAR(50),
    PRIMARY KEY (serviceWorkerEmail, address),
    FOREIGN KEY (serviceWorkerEmail) REFERENCES Service_Worker,
    FOREIGN KEY (address) REFERENCES Property
);

CREATE TABLE Service_Details ( 
    serviceType VARCHAR(50), 
    rate INT NOT NULL,
    PRIMARY KEY (serviceType)
);

CREATE TABLE Tenant (
    tenantId CHAR(10),
    tenantName VARCHAR(30),
    tenantEmail VARCHAR(30),
    PRIMARY KEY (tenantId)
);

CREATE TABLE Roommates_With_Tenant (
    tenantId CHAR(10),
    roommateName VARCHAR(30),
    PRIMARY KEY (tenantId, roommateName),
    FOREIGN KEY (tenantId) REFERENCES Tenant ON DELETE CASCADE
);

CREATE TABLE Lease_Increase ( 
    rentCost INT, 
    address VARCHAR(50),
    maxAnnualRentIncrease INT NOT NULL, 
    PRIMARY KEY (address, rentCost), 
    FOREIGN KEY (address) REFERENCES Property
);

CREATE TABLE Lease (
    leaseId CHAR(10),
    startDate date NOT NULL,
    leaseLength INT,
    rentCost INT NOT NULL,
    deposit INT,
    userId CHAR(10) NOT NULL,
    address VARCHAR(50) NOT NULL,
    tenantId CHAR(10) NOT NULL,
    PRIMARY KEY (leaseId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    FOREIGN KEY (address) REFERENCES Property,
    FOREIGN KEY (tenantId) REFERENCES Tenant ON DELETE CASCADE, 
    FOREIGN KEY (address, rentCost) REFERENCES Lease_Increase
);

INSERT INTO Property_Manager VALUES ('cdefghijkl', 'kXf84udN@gL:j', 'Richard', 'richard@propertymanagement.ca');
INSERT INTO Property_Manager VALUES ('defghijklc', '`=pghQpR)e<!3L_8', 'Peter', 'peter@propertymanagement.ca');
INSERT INTO Property_Manager VALUES ('efghijklcd', '$*-5\4pv-jRc4]E@', 'William', 'william@propertymanagement.ca');
INSERT INTO Property_Manager VALUES ('fghijklcde', 'K_,EW%`X;^Z6cHZ7', 'Brian', 'brian@property');
INSERT INTO Property_Manager VALUES ('ghijklcdef', 'eH57mEu7tA3Ug*2\', 'Daniel', 'daniel@propertymanagement.ca');

INSERT INTO Service_Worker VALUES ('joe@abcconstruction.org', 'Joe', 'Miscellaneous repairs');
INSERT INTO Service_Worker VALUES ('jack@plumbingareus.com', 'Jack', 'Plumbing');
INSERT INTO Service_Worker VALUES ('jake@competitiveplumbing.ca', 'Jake', 'Misc');
INSERT INTO Service_Worker VALUES ('jill@electricity.com', 'Jill', 'Electrical work');
INSERT INTO Service_Worker VALUES ('jane@furnaceexpertscalgary.ca', 'Jane', 'Furnace repair and installation');

INSERT INTO Landlord VALUES ('bcdefghijk', 'john@property.com', 'cdefghijkl');
INSERT INTO Landlord VALUES ('cdefghijkb', 'david@realestate.ca', 'defghijklc');
INSERT INTO Landlord VALUES ('defghijkbc', 'robert@gmail.com', 'efghijklcd');
INSERT INTO Landlord VALUES ('efghijkbcd', 'michael@realestateinvestor.org', 'fghijklcde');
INSERT INTO Landlord VALUES ('fghijkbcde', 'landlordpaul@aol.ca', 'ghijklcdef');

INSERT INTO Account VALUES ('abcdefghij', -5000, 'bcdefghijk');
INSERT INTO Account VALUES ('bcdefghija', 300, 'cdefghijkb');
INSERT INTO Account VALUES ('cdefghijab', 150000, 'defghijkbc');
INSERT INTO Account VALUES ('defghijabc', 11000, 'efghijkbcd');
INSERT INTO Account VALUES ('efghijabcd', 55000, 'fghijkbcde');

INSERT INTO Property VALUES ('1220 Homer St, Vancouver, BC V6B 2Y5', 'bcdefghijk', 1);
INSERT INTO Property VALUES ('1666 W 75th Ave, Vancouver, BC V6P 6G2', 'cdefghijkb', 1);
INSERT INTO Property VALUES ('375 W 5th Ave, Vancouver, BC V5Y 1J6', 'defghijkbc', 0);
INSERT INTO Property VALUES ('3515 26 St NE, Calgary, AB T1Y 7E3', 'efghijkbcd', 0);
INSERT INTO Property VALUES ('2335 Pegasus Rd NE, Calgary, AB T2E 8C3', 'fghijkbcde', 1);

INSERT INTO Services VALUES ('joe@abcconstruction.org', '1220 Homer St, Vancouver, BC V6B 2Y5');
INSERT INTO Services VALUES ('joe@abcconstruction.org', '1666 W 75th Ave, Vancouver, BC V6P 6G2');
INSERT INTO Services VALUES ('jack@plumbingareus.com', '375 W 5th Ave, Vancouver, BC V5Y 1J6');
INSERT INTO Services VALUES ('jill@electricity.com', '3515 26 St NE, Calgary, AB T1Y 7E3');
INSERT INTO Services VALUES ('jane@furnaceexpertscalgary.ca', '2335 Pegasus Rd NE, Calgary, AB T2E 8C3');

INSERT INTO Service_Details VALUES ('Miscellaneous repairs', 50);
INSERT INTO Service_Details VALUES ('Plumbing', 80);
INSERT INTO Service_Details VALUES ('Misc', 70);
INSERT INTO Service_Details VALUES ('Electrical work', 90);
INSERT INTO Service_Details VALUES ('Furnace repair and installation', 75);

INSERT INTO Tenant VALUES ('mnopqrstuv', 'Christian', 'christian@gmail.com');
INSERT INTO Tenant VALUES ('nopqrstuvm', 'Caleb', 'caleb@yahoo.ca');
INSERT INTO Tenant VALUES ('opqrstuvmn', 'Cornelius', 'cornelius@gmail.com');
INSERT INTO Tenant VALUES ('pqrstuvmno', 'Kristaps', 'kristaps@gmail.com');
INSERT INTO Tenant VALUES ('qrstuvmnop', 'Clayton', 'clayton@hotmail.com');

INSERT INTO Roommates_With_Tenant VALUES ('mnopqrstuv', 'Brandon');
INSERT INTO Roommates_With_Tenant VALUES ('mnopqrstuv', 'Broderick');
INSERT INTO Roommates_With_Tenant VALUES ('mnopqrstuv', 'Blake');
INSERT INTO Roommates_With_Tenant VALUES ('pqrstuvmno', 'Bartholemew');
INSERT INTO Roommates_With_Tenant VALUES ('qrstuvmnop', 'Betty');

INSERT INTO Lease_Increase VALUES(2200, '1220 Homer St, Vancouver, BC V6B 2Y5', 200);
INSERT INTO Lease_Increase VALUES(2100, '1666 W 75th Ave, Vancouver, BC V6P 6G2', 100);
INSERT INTO Lease_Increase VALUES(800, '375 W 5th Ave, Vancouver, BC V5Y 1J6', 50);
INSERT INTO Lease_Increase VALUES(900, '3515 26 St NE, Calgary, AB T1Y 7E3', 75);
INSERT INTO Lease_Increase VALUES(1900, '2335 Pegasus Rd NE, Calgary, AB T2E 8C3', 20);

INSERT INTO Lease VALUES ('qrstuvwxyz', TO_DATE('01/01/2022', 'DD/MM/YYYY'), 12, 2200, 2200, 'cdefghijkl', '1220 Homer St, Vancouver, BC V6B 2Y5', 'mnopqrstuv');
INSERT INTO Lease VALUES ('rstuvwxyzq', TO_DATE('07/05/2021', 'DD/MM/YYYY'), 12, 2100, 4200, 'defghijklc', '1666 W 75th Ave, Vancouver, BC V6P 6G2', 'nopqrstuvm');
INSERT INTO Lease VALUES ('stuvwxyzqr', TO_DATE('04/01/2021', 'DD/MM/YYYY'), 12, 800, 1600, 'efghijklcd', '375 W 5th Ave, Vancouver, BC V5Y 1J6', 'opqrstuvmn');
INSERT INTO Lease VALUES ('tuvwxyzqrs', TO_DATE('30/01/2022', 'DD/MM/YYYY'), 1, 900, 900, 'fghijklcde', '3515 26 St NE, Calgary, AB T1Y 7E3', 'pqrstuvmno');
INSERT INTO Lease VALUES ('uvwxyzqrst', TO_DATE('01/02/2022', 'DD/MM/YYYY'), 12, 1900, 3800, 'ghijklcdef', '2335 Pegasus Rd NE, Calgary, AB T2E 8C3', 'qrstuvmnop');