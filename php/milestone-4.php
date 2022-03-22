<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

  <html>
    <head>
        <link rel='stylesheet' type='text/css' href='style.php' />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Rubik&display=swap" rel="stylesheet">
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Evict Roommate</h2>
        <form method="POST" action="milestone-4.php">
            <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
            Tenant ID: <input type="text" name="tenantId">
            Roommate Name: <input type="text" name="roommateName">
            <input type="submit" value="Evict" name="deleteSubmit">
        </form>

        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getRoommateDataRequest" name="getRoommateDataRequest">
            <input type="submit" name="getRoommateData" value="Display Roommate Data">
        </form>

        <h2>Evict Tenant</h2>
        <form method="POST" action="milestone-4.php">
            <input type="hidden" id="deleteTenantQueryRequest" name="deleteTenantQueryRequest">
            Tenant ID: <input type="text" name="tenantId">
            <input type="submit" value="Evict" name="deleteTenantSubmit">
        </form>

        <form method="GET" action="milestone-4.php">
            <input type="hidden" id="getTenantAndRoommatesRequest" name="getTenantAndRoommatesRequest">
            <input type="submit" name="getTenantAndRoommatesData" value="Display Tenant And Roommates">
        </form>

        <h2>Get Lowest Priced Units By Housing Type</h2>
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



        <!-- <h2>Reset</h2>
        <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="POST" action="oracle-test.php">
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

        <hr />

        <h2>Insert Values into DemoTable</h2>
        <form method="POST" action="oracle-test.php">
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Number: <input type="text" name="insNo"> <br /><br />
            Name: <input type="text" name="insName"> <br /><br />

            <input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Name in DemoTable</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="oracle-test.php">
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old Name: <input type="text" name="oldName"> <br /><br />
            New Name: <input type="text" name="newName"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Count the Tuples in DemoTable</h2>
        <form method="GET" action="oracle-test.php">
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

        <h2>Display Data</h2>
        <form method="GET" action="oracle-test.php">
            <input type="hidden" id="getDataRequest" name="getDataRequest">
            <input type="submit" name="getData">
        </form> -->



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
            echo $r;
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
            $db_conn = OCILogon("ora_jjsoroka", "a37475143", "dbhost.students.cs.ubc.ca:1522/stu");

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

        // function handleUpdateRequest() {
        //     global $db_conn;

        //     $old_name = $_POST['oldName'];
        //     $new_name = $_POST['newName'];

        //     // you need the wrap the old name and new name values with single quotations
        //     executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
        //     OCICommit($db_conn);
        // }

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

            getTenantAndRoommatesData();

        }

        function handleRequest() {
            global $db_conn;

            $tenantId = $_POST['tenantId'];

            executePlainSQL("DELETE FROM Tenant WHERE tenantId='" . $tenantId . "'");
            
            OCICommit($db_conn);

            getTenantAndRoommatesData();

        }


        function getRoommateData() {
            global $db_conn;
            $result = executePlainSQL("SELECT * FROM Roommates_With_Tenant");printRoommateDataResult($result);
        }

        function printRoommateDataResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from Roommates_With_Tenant table:<br>";
            echo "<table>";
            echo "<tr><th>tenantId</th><th>roommateName</th></tr>";

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

        function getLowestPricedUnitsData() {
            global $db_conn;
            $result = executePlainSQL("SELECT propertyType, MIN(rentCost) FROM Property, Lease WHERE Property.address = Lease.address GROUP BY propertyType");
            printLowestPricedUnitsResult($result);
        }

        function printLowestPricedUnitsResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from Property and Lease table:<br>";
            echo "<table>";
            echo "<tr><th>Property Type (0 is a Room, 1 is a Unit)</th><th>Least Expensive Room, in Dollars</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        function getServiceQueryData() { //prints results from a select statement
            global $db_conn;

            $rate = $_GET['serviceMaxPrice'];
            $service = $_GET['serviceKeyword'];

            $result = executePlainSQL("SELECT Service_Details.serviceType, rate, serviceWorkerName, serviceWorkerEmail FROM Service_Details, Service_Worker WHERE Service_Details.serviceType = Service_worker.serviceType AND $rate >= rate AND (LOWER(Service_Details.serviceType) LIKE LOWER('%$service%'))");

            getServiceQueryResult($result);
        }

        function getServiceQueryResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from Service_Details table:<br>";
            echo "<table>";
            echo "<tr><th>Service Type</th><th>Rate</th><th>Contact Name</th><th>Contact Email</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>";
            }

            echo "</table>";
        }

        function printTenantDataResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from Tenant table:<br>";
            echo "<table>";
            echo "<tr><th>tenantId</th><th>tenantName</th><th>tenantEmail</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] , "</td></tr>";
            }

            echo "</table>";
        }

        function getTenantAndRoommatesData() {
            getTenantData();
            getRoommateData();
        }

        // function handleResetRequest() {
        //     global $db_conn;
        //     // Drop old table
        //     executePlainSQL("DROP TABLE demoTable");

        //     // Create new table
        //     echo "<br> creating new table <br>";
        //     executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
        //     OCICommit($db_conn);
        // }

        // function handleInsertRequest() {
        //     global $db_conn;

        //     //Getting the values from user and insert data into the table
        //     $tuple = array (
        //         ":bind1" => $_POST['insNo'],
        //         ":bind2" => $_POST['insName']
        //     );

        //     $alltuples = array (
        //         $tuple
        //     );

        //     executeBoundSQL("insert into demoTable values (:bind1, :bind2)", $alltuples);
        //     OCICommit($db_conn);
        // }

        // function handleCountRequest() {
        //     global $db_conn;

        //     $result = executePlainSQL("SELECT Count(*) FROM demoTable");

        //     if (($row = oci_fetch_row($result)) != false) {
        //         echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
        //     }
        // }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        // function handlePOSTRequest() {
        //     if (connectToDB()) {
        //         if (array_key_exists('resetTablesRequest', $_POST)) {
        //             handleResetRequest();
        //         } else if (array_key_exists('updateQueryRequest', $_POST)) {
        //             handleUpdateRequest();
        //         } else if (array_key_exists('insertQueryRequest', $_POST)) {
        //             handleInsertRequest();
        //         } else if (array_key_exists('deleteQueryRequest', $_POST)) {
        //             handleDeleteRequest();
        //         }

        //         disconnectFromDB();
        //     }
        // }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        // function handleGETRequest() {
        //     if (connectToDB()) {
        //         if (array_key_exists('countTuples', $_GET)) {
        //             handleCountRequest();
        //         }

        //         if (array_key_exists('getData', $_GET)) {
        //             getData();
        //         } 

        //         // code I added
        //         if (array_key_exists('getRoommateData', $_GET)) {
        //             getRoommateData();
        //         }

        //         disconnectFromDB();
        //     }
        // }

		// if (
        //     isset($_POST['reset']) || 
        //     isset($_POST['updateSubmit']) || 
        //     isset($_POST['insertSubmit']) ||
        //     isset($_POST['deleteSubmit'])) {
        //     handlePOSTRequest();
        // } else if (isset($_GET['countTupleRequest']) || isset($_GET['getRoommateDataRequest'])) {
        //     handleGETRequest();
        // }

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
        }
		?>
	</body>
</html>
