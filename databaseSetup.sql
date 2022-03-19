CREATE TABLE Account ( 
    accountId: char(10), 
    balance: int,
    customerId: char(10), 
    PRIMARY KEY (accountId),
    FOREIGN KEY (customerId) REFERENCES Landlord 
)

CREATE TABLE Property_Manager ( 
    userId: char(10), 
    password: char(20),
    propertyManagerName: char(30), 
    propertyManagerEmail: char(30), 
    PRIMARY KEY (userId), 
    UNIQUE(propertyManagerEmail)
)

CREATE TABLE Service_Worker ( 
    serviceWorkerEmail: char(30), 
    serviceWorkerName: char(30), 
    serviceType: char(20), 
    PRIMARY KEY (email)
)

CREATE TABLE Service_Details ( 
    serviceType: char(20), 
    rate: int NOT NULL,
    PRIMARY KEY (serviceType)
    FOREIGN KEY (serviceType) REFERENCES Service_Worker,
)

CREATE TABLE Services (
    serviceWorkerEmail: char(30),
    address: char(30),
    PRIMARY KEY (serviceWorkerEmail, address),
    FOREIGN KEY (serviceWorkerEmail) REFERENCES Service_Worker,
    FOREIGN KEY (address) REFERENCES Property
)

CREATE TABLE Property (
    address: char(30),
    customerId: char(10) NOT NULL,
    propertyType: int,
    PRIMARY KEY (address),
    FOREIGN KEY (customerId) REFERENCES Landlord
)

CREATE TABLE Landlord (
    customerId: char(10),
    landlordEmail: char(30),
    userId: char(10) NOT NULL,
    PRIMARY KEY (customerId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    UNIQUE (landlordEmail)
)

CREATE TABLE Roommates_With_Tenant (
    tenantId: char(10),
    roommateName: char(30),
    PRIMARY KEY (tenantId, roommateName),
    FOREIGN KEY (tenantId) REFERENCES Tenant
    ON DELETE CASCADE
)

CREATE TABLE Tenant (
    tenantId: char(10),
    tenantName: char(30),
    tenantEmail: char(30),
    PRIMARY KEY (tenantId)
)

CREATE TABLE Lease (
    leaseId: char(10),
    startDate: date NOT NULL,
    leaseLength: int,
    rentCost: int NOT NULL,
    deposit: int,
    userId: char(10) NOT NULL,
    address: char(30) NOT NULL,
    tenantId: char(10) NOT NULL,
    PRIMARY KEY (leaseId),
    FOREIGN KEY (userId) REFERENCES Property_Manager,
    FOREIGN KEY (address) REFERENCES Property,
    FOREIGN KEY (tenantId) REFERENCES Tenant, 
    FOREIGN KEY (address, rentCost) REFERENCES Lease_Increase
)

CREATE TABLE Lease_Increase ( 
    rentCost: int, 
    address: char(30),
    maxAnnualRentIncrease: int NOT NULL, 
    PRIMARY KEY (rentCost, address), 
    FOREIGN KEY (address) REFERENCES Property
)