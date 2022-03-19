CREATE TABLE Account ( 
    accountId: char(10), 
    balance: int,
    customerId: char(10), 
    PRIMARY KEY (accountId),
    FOREIGN KEY (customerId) REFERENCES Landlord 
);

CREATE TABLE Property_Manager ( 
    userId: char(10), 
    password: varchar(20),
    propertyManagerName: varchar(30), 
    propertyManagerEmail: varchar(30), 
    PRIMARY KEY (userId), 
    UNIQUE(propertyManagerEmail)
);

CREATE TABLE Service_Worker ( 
    serviceWorkerEmail: varchar(30), 
    serviceWorkerName: varchar(30), 
    serviceType: varchar(20), 
    PRIMARY KEY (email)
);

CREATE TABLE Service_Details ( 
    serviceType: varchar(20), 
    rate: int NOT NULL,
    PRIMARY KEY (serviceType)
    FOREIGN KEY (serviceType) REFERENCES Service_Worker,
);

CREATE TABLE Services (
    serviceWorkerEmail: varchar(30),
    address: varchar(30),
    PRIMARY KEY (serviceWorkerEmail, address),
    FOREIGN KEY (serviceWorkerEmail) REFERENCES Service_Worker,
    FOREIGN KEY (address) REFERENCES Property
);

CREATE TABLE Property (
    address: varchar(30),
    customerId: char(10) NOT NULL,
    propertyType: int,
    PRIMARY KEY (address),
    FOREIGN KEY (customerId) REFERENCES Landlord
);

CREATE TABLE Landlord (
    customerId: char(10),
    landlordEmail: varchar(30),
    userId: char(10) NOT NULL,
    PRIMARY KEY (customerId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    UNIQUE (landlordEmail)
);

CREATE TABLE Roommates_With_Tenant (
    tenantId: char(10),
    roommateName: varchar(30),
    PRIMARY KEY (tenantId, roommateName),
    FOREIGN KEY (tenantId) REFERENCES Tenant
    ON DELETE CASCADE
);

CREATE TABLE Tenant (
    tenantId: char(10),
    tenantName: varchar(30),
    tenantEmail: varchar(30),
    PRIMARY KEY (tenantId)
);

CREATE TABLE Lease (
    leaseId: char(10),
    startDate: date NOT NULL,
    leaseLength: int,
    rentCost: int NOT NULL,
    deposit: int,
    userId: char(10) NOT NULL,
    address: varchar(30) NOT NULL,
    tenantId: char(10) NOT NULL,
    PRIMARY KEY (leaseId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    FOREIGN KEY (address) REFERENCES Property,
    FOREIGN KEY (tenantId) REFERENCES Tenant, 
    FOREIGN KEY (address, rentCost) REFERENCES Lease_Increase
);

CREATE TABLE Lease_Increase ( 
    rentCost: int, 
    address: varchar(30),
    maxAnnualRentIncrease: int NOT NULL, 
    PRIMARY KEY (rentCost, address), 
    FOREIGN KEY (address) REFERENCES Property
);

-- TODO: populate the tables

INSERT INTO Account VALUES ("abcdefghij", -5000, "bcdefghijk");
INSERT INTO Account VALUES ("bcdefghija", 300, "cdefghijkb");
INSERT INTO Account VALUES ("cdefghijab", 150000, "defghijkbc");
INSERT INTO Account VALUES ("defghijabc", 11000, "efghijkbcd");
INSERT INTO Account VALUES ("efghijabcd", 55000, "fghijkbcde");

INSERT INTO Property_Manager VALUES ("cdefghijkl", "kX/&f84ud&N@gL:j", "Richard", "richard@propertymanagement.ca");
INSERT INTO Property_Manager VALUES ("defghijklc", "`=pghQpR)e<!3L_8", "Peter", "peter@property management.ca");
INSERT INTO Property_Manager VALUES ("efghijklcd", "$*-5'4pv-jRc4]E@", "William", "william@prope rtymanagement.ca");
INSERT INTO Property_Manager VALUES ("fghijklcde", "K_,EW%`X;^Z6cHZ7", "Brian", "brian@property");
INSERT INTO Property_Manager VALUES ("ghijklcdef", "eH57mEu7tA3Ug*2\", "Daniel", "daniel@propertymanagement.ca");

INSERT INTO Service_Worker VALUES ("joe@abcconstruction.org", "Joe", "Miscellaneous repairs");
INSERT INTO Service_Worker VALUES ("jack@plumbingareus.com", "Jack", "Plumbing");
INSERT INTO Service_Worker VALUES ("jake@competitiveplumbing.ca", "Jake", "Plumbing");
INSERT INTO Service_Worker VALUES ("jill@electricity.com", "Jill", "Electrical work");
INSERT INTO Service_Worker VALUES ("jane@furnaceexpertscalgary.ca", "Jane", "Furnace repair and installation");

INSERT INTO Service_Details VALUES ("Miscellaneous repairs", 50);
INSERT INTO Service_Details VALUES ("Plumbing", 80);
INSERT INTO Service_Details VALUES ("Plumbing", 70);
INSERT INTO Service_Details VALUES ("Electrical work", 90);
INSERT INTO Service_Details VALUES ("Furnace repair and installation", 75);

INSERT INTO Services VALUES ("joe@abcconstruction.org", "1220 Homer St, Vancouver, BC V6B 2Y5");
INSERT INTO Services VALUES ("joe@abcconstruction.org", "1666 W 75th Ave, Vancouver, BC V6P 6G2");
INSERT INTO Services VALUES ("jack@plumbingareus.com", "375 W 5th Ave, Vancouver, BC V5Y 1J6");
INSERT INTO Services VALUES ("jill@electricity.com", "3515 26 St NE, Calgary, AB T1Y 7E3");
INSERT INTO Services VALUES ("jane@furnaceexpertscalgary.ca", "2335 Pegasus Rd NE, Calgary, AB T2E 8C3");

INSERT INTO Property VALUES ("1220 Homer St, Vancouver, BC V6B 2Y5", "bcdefghijk", 1;
INSERT INTO Property VALUES ("1666 W 75th Ave, Vancouver, BC V6P 6G2", "cdefghijkb", 1);
INSERT INTO Property VALUES ("375 W 5th Ave, Vancouver, BC V5Y 1J6", "efghijklcd", 0);
INSERT INTO Property VALUES ("3515 26 St NE, Calgary, AB T1Y 7E3", "efghijkbcd", 0);
INSERT INTO Property VALUES ("2335 Pegasus Rd NE, Calgary, AB T2E 8C3", "fghijkbcde", 1);

