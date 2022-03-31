<html>
    <head>
        <link rel='stylesheet' type='text/css' href='style.php' />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Find the Head Tenant For Each Roommate</h2>
        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="tenantRoommateJoinRequest" name="tenantRoommateJoinRequest">
            <input type="submit" value="Find" name="tenantRoommateJoinSubmit">
        </form>

        <h2>Evict Tenant</h2>
        <form method="POST" action="milestone-4.php">
            <input type="hidden" id="deleteTenantQueryRequest" name="deleteTenantQueryRequest">
            Tenant ID: <input type="text" name="tenantId">
            <input type="submit" value="Evict" name="deleteTenantSubmit">
        </form>

        <h2>Get Landlord Contact Information</h2>
        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getLandlordContactInfoRequest" name="getLandlordContactInfoRequest">
            Tenant ID: <input type="text" name="tenantIdKeyword">
            <input type="submit" value="Show" name="getLandlordContactInfoSubmit">
        </form>

        <h2>Find the Property Type With the Lowest Average Rent</h2>
        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getLowestPricedUnitsRequest" name="getLowestPricedUnitsRequest">
            <input type="submit" value="Show" name="getLowestPricedUnitsSubmit">
        </form>

        <h2>Find Services</h2>
        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getServiceQueryRequest" name="getServiceQueryRequest">
            Service Contains keyword: <input type="text" name="serviceKeyword">
            Service rate is equal or less than: <input type="float" name="serviceMaxPrice">
            <input type="submit" value="Submit" name="getServiceQuerySubmit">
        </form>

        <h2>Get Lease Field</h2>
        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getLeaseField" name="getLeaseFieldRequest">
            <input type="radio" name="leaseFieldKeyword" value="startDate" checked> Start Date
            <input type="radio" name="leaseFieldKeyword" value="leaseLength">Lease Length
            <input type="radio" name="leaseFieldKeyword" value="rentCost">Rent Cost
            <input type="radio" name="leaseFieldKeyword" value="deposit">Deposit
            <input type="radio" name="leaseFieldKeyword" value="userId"> Property Manager ID
            <input type="radio" name="leaseFieldKeyword" value="address">Address
            <input type="radio" name="leaseFieldKeyword" value="tenantId">Tenant ID
            <input type="submit" value="Submit" name="getLeaseFieldSubmit">
        </form>

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            // echo $r;
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("", "", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleDeleteRequest() {
            global $db_conn;

            $tenantId = $_POST['tenantId'];
            $roommateName = $_POST['roommateName'];

            executePlainSQL("DELETE FROM Roommates_With_Tenant WHERE tenantId='" . $tenantId . "' AND roommateName='" . $roommateName . "'");
            
            OCICommit($db_conn);

            getRoommateData();
        }

        function handleTenantDeleteRequest() {
            global $db_conn;

            $tenantId = $_POST['tenantId'];

            executePlainSQL("DELETE FROM Tenant WHERE tenantId='" . $tenantId . "'");
            
            OCICommit($db_conn);
        }

        function getRoommateData() {
            global $db_conn;
            $result = executePlainSQL("SELECT * FROM Roommates_With_Tenant");printRoommateDataResult($result);
        }

        function printRoommateDataResult($result) {
            echo "<br>Retrieved data from Roommates_With_Tenant table:<br>";
            echo "<table>";
            echo "<tr><th>Tenant ID</th><th>Roommate Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        function getTenantData() {
            global $db_conn;
            $result = executePlainSQL("SELECT * FROM Tenant");
            printTenantDataResult($result);
        }

        function getLeaseField() {
            global $db_conn;

            $leaseField = $_GET['leaseFieldKeyword'];

            $fieldName;
            switch ($leaseField) {
                case 'startDate':
                    $fieldName = 'Start Date';
                    break;
                case 'leaseLength':
                    $fieldName = 'Lease Length (in months)';
                    break;
                case 'rentCost':
                    $fieldName = 'Rent Cost (in dollars per month)';
                    break;
                case 'deposit':
                    $fieldName = 'Deposit (in dollars)';
                    break;
                case 'userId':
                    $fieldName = 'Property Manager ID';
                    break;
                case 'address':
                    $fieldName = 'Address';
                    break;
                case 'tenantId':
                    $fieldName = 'Tenant ID';
                    break;
            }

            $result = executePlainSQL("SELECT leaseId, $leaseField FROM Lease");
            printLeaseFieldResult($result, $fieldName);
        }

        function printLeaseFieldResult($result, $fieldName) {
            echo "<br>Retrieved data from Lease table:<br>";
            echo "<table>";
            echo "<tr><th>Lease ID</th><th>$fieldName</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        function getLowestPricedUnitsData() {
            global $db_conn;
            $result = executePlainSQL("WITH Temp(propertyType, avgRent) AS (SELECT propertyType, AVG(rentCost) FROM Property, Lease WHERE Property.address = Lease.address GROUP BY propertyType), TempMinAvg(minAvgRent) AS (SELECT MIN(avgRent) FROM Temp) SELECT Temp.propertyType, Temp.avgRent FROM Temp, TempMinAvg WHERE Temp.avgRent = TempMinAvg.minAvgRent");
            printLowestPricedUnitsResult($result);
        }


        function printLowestPricedUnitsResult($result) {
            echo "<br>Retrieved data from Property and Lease table:<br>";
            echo "<table>";
            echo "<tr><th>Property Type</th><th>Rent, in Dollars</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . ($row[0] == 0 ? "Room" : "Unit") . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        function getTenantAndRoommatesJoin() {
            global $db_conn;

            $result = executePlainSQL("SELECT Tenant.tenantId, Tenant.tenantEmail, Tenant.tenantName, Roommates_With_Tenant.roommateName FROM Tenant, Roommates_With_Tenant WHERE Tenant.tenantId = Roommates_With_Tenant.tenantId");

            printTenantAndRoommatesJoin($result);
        }

        function printTenantAndRoommatesJoin($result) {
            echo "<br>Retrieved data from the Tenant and Roommates_With_Tenant tables:<br>";
            echo "<table>";
            echo "<tr><th>Tenant ID</th><th>Tenant Name</th><th>Tenant Email</th><th>Roommate Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] . "</td><td>" . $row[1] . "</td><td>" . $row[3] . "</td></tr>";
            }

            echo "</table>";
        }

        function getServiceQueryData() {
            global $db_conn;

            $rate = $_GET['serviceMaxPrice'];
            $service = $_GET['serviceKeyword'];

            $result = executePlainSQL("SELECT Service_Details.serviceType, rate, serviceWorkerName, serviceWorkerEmail FROM Service_Details, Service_Worker WHERE Service_Details.serviceType = Service_Worker.serviceType AND $rate >= rate AND (LOWER(Service_Details.serviceType) LIKE LOWER('%$service%'))");

            printServiceQueryResult($result);
        }

        function getLandlordContactInfo() {
            global $db_conn;

            $tenantID = $_GET['tenantIdKeyword'];

            $result = executePlainSQL("SELECT Landlord.landlordEmail FROM Landlord, Property, Lease WHERE Landlord.customerId = Property.customerId AND Property.address = Lease.address AND tenantId='" . $tenantID . "'");

            printLandlordContactInfoResult($result);
        }

        function printLandlordContactInfoResult($result) {
            echo "<br>Retrieved data from Landlord, Property, and Lease tables:<br>";
            echo "<table>";
            echo "<tr><th>Landlord Email</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }

        function printServiceQueryResult($result) {
            echo "<br>Retrieved data from Service_Details table:<br>";
            echo "<table>";
            echo "<tr><th>Service Type</th><th>Rate ($/hr)</th><th>Contact Name</th><th>Contact Email</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>";
            }

            echo "</table>";
        }

        function printTenantDataResult($result) {
            echo "<br>Retrieved data from Tenant table:<br>";
            echo "<table>";
            echo "<tr><th>Tenant ID</th><th>Tenant Name</th><th>Tenant Email</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] , "</td></tr>";
            }

            echo "</table>";
        }

        function getTenantAndRoommatesData() {
            getTenantData();
            getRoommateData();
        }

        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('getRoommateData', $_GET)) {
                    getRoommateData();
                } else if (array_key_exists('getTenantAndRoommatesData', $_GET)) {
                    getTenantAndRoommatesData();
                } else if (array_key_exists('getLowestPricedUnitsRequest', $_GET)) {
                    getLowestPricedUnitsData();
                } else if (array_key_exists('getServiceQueryRequest', $_GET)) {
                    getServiceQueryData();
                } else if (array_key_exists('getLeaseFieldSubmit', $_GET)) {
                    getLeaseField();
                } else if (array_key_exists('tenantRoommateJoinRequest', $_GET)) {
                    getTenantAndRoommatesJoin();
                } 
                else if (array_key_exists('getLandlordContactInfoSubmit', $_GET)) {
                    getLandlordContactInfo();
                }

                disconnectFromDB();
            }
        }

        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('deleteQueryRequest', $_POST)) {
                    handleDeleteRequest();
                } 
                else if (array_key_exists('deleteTenantQueryRequest', $_POST)) {
                    handleTenantDeleteRequest();
                }

                disconnectFromDB();
            }
        }

        if (isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_POST['deleteTenantSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['getRoommateDataRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['getTenantAndRoommatesRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['getLowestPricedUnitsSubmit'])) {
            handleGETRequest();
        } else if (isset($_GET['getServiceQueryRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['getLeaseFieldRequest'])) {
            handleGETRequest();
        } else if (isset($_GET['tenantRoommateJoinSubmit'])) {
            handleGETRequest();
        } 
        else if (isset($_GET['getLandlordContactInfoRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
