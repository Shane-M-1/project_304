<?php

// ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



$db_conn = NULL;	// login credentials are used in connectToDB()


$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())


?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Disclosures Page</title>
</head>
<body>
    <nav>
        <a href="propertyList.php"><button>Property List</button></a>
        <a href="agent.php"><button>Agent Info</button></a>
        <a href="seller.php"><button>Seller Info</button></a>
        <a href="buyer.php"><button>Buyer Info</button></a>
        <a href="lawyer.php"><button>Lawyer Info</button></a>
        <a href="login.php"><button id="signout">Sign Out</button></a>
      </nav>
    
    <h1>Disclosures Information</h1>
    <h2>Select Property</h2>

    <form method="POST" action="disclosure.php">
        <input type="hidden" id="displayQueryRequest" name="displayQueryRequest">
        Select Property:
        <select name="propertySelect">
        <?php
        if (connectToDB()) {
            $result = executePlainSQL("SELECT * FROM Apartment1 a, Property p WHERE p.pid=a.pid");
            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<option>"."#".$row["PID"].". ".$row["APTNUM"]."-". $row["STREETNUM"] ." ". $row["POSTALCODE"] ."</option>";
            }

            $result = executePlainSQL("SELECT * FROM House h, Property p WHERE p.pid=h.pid");
            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<option>"."#".$row["PID"].". ". $row["STREETNUM"] ." ". $row["POSTALCODE"] ."</option>";
            }
        }
        disconnectFromDB();

        ?>
        </select>
        <input type="submit" value="VIEW" name="viewProperty" class="generalbtn"></p>
    </form>

    <hr />
    <h2>Add New Disclosure</h2>
	<form method="POST" action="disclosure.php">
		<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
        <select name="insertDisclosure" id="insertDisclosureSelect" style="display: none;">
        <?php
        $property = substr($_POST['propertySelect'], 1, 3);
        echo "<option>".$property."</option>";
        ?>
		</select>

		Location: <input type="text" name="insLocation" required> <br /><br />
		Type: <input type="text" pattern="[0-9]{4}" name="insType" minlength="4" maxlength="4" required> <br /><br />
        Date Logged: <input type="date" name="insDate" required> <br /><br />
        Resolved?: <input type="text" pattern="[0-1]" name="insResolved" required> <br /><br />
		<input type="submit" value="Insert" name="insertSubmit" class="generalbtn"></p>
	</form>

	<hr />

    <h2>Update Disclosure</h2>
	<form method="POST" action="disclosure.php">
		<input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
		Update Disclosure:
        <select name="updateDisclosure" id="updateDisclosureSelect">
        <?php
        $property = substr($_POST['propertySelect'], 1, 3);
        if (connectToDB()) {
            $result = executePlainSQL("SELECT * FROM Disclosures WHERE pid='".$property."'");

            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<option>"."(".$property.") #".$row["DID"]." ".$row["TYPE"]." - ".$row["LOCATION"]."</option>";
            }
        }
        disconnectFromDB();
        ?>
		</select>

		<br /><br />
		Change Location: <input type="text" name="upLocation" > <br /><br />
		Change Type: <input type="text" pattern="[0-9]{4}" name="upType" minlength="4" maxlength="4" > <br /><br />
        Change Date Logged: <input type="date" name="upDate" > <br /><br />
        Change Resolved?: <input type="text" pattern="[0-1]" name="upResolved" > <br /><br />

		<input type="submit" value="Update" name="updateSubmit" class="generalbtn"></p>
	</form>

	<hr />

    <h2>Delete Disclosure</h2>
	<p>This action is irreversible!!</p>

	<form method="POST" action="disclosure.php">
		<input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
		Delete Disclosure:
        <select name="deleteDisclosure" id="deleteDisclosureSelect">
        <?php
        $property = substr($_POST['propertySelect'], 1, 3);
        if (connectToDB()) {
            $result = executePlainSQL("SELECT * FROM Disclosures WHERE pid='".$property."'");

            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                //echo "<option>"."Property #".$property.": ".$row["DID"]."-".$row["TYPE"]." - ".$row["LOCATION"]."</option>";
                echo "<option>"."(".$property.") #".$row["DID"]." ".$row["TYPE"]." - ".$row["LOCATION"]."</option>";
            }
        }
        disconnectFromDB();
        ?>
		</select>

		<input type="submit" value="DELETE" name="deleteSubmit" class="generalbtn"></p>
	</form>

    <hr />
    <h2>Demo Queries</h2>
    <form method="POST" action="disclosure.php">
        <input type="hidden" id="minDisclosureRequest" name="minDisclosureRequest">
        Get all property listed prices with more than one disclosure:
        <input type="submit" value="GET" name="minDisclosureSubmit" class="generalbtn"></p>
    </form>
    <form method="POST" action="disclosure.php">
        <input type="hidden" id="allDisclosureRequest" name="allDisclosureRequest">
        Get all Properties with which contain all disclosure types:
        <input type="submit" value="GET" name="allDisclosureSubmit" class="generalbtn"></p>
    </form>
    <hr />
    <h2>View</h2>

  <?php

  // The following code will be parsed as PHP
  function debugAlertMessage($message)
  {
      global $show_debug_alert_messages;

      if ($show_debug_alert_messages) {
          echo "<script type='text/javascript'>alert('" . $message . "');</script>";
      }
  }

  function executePlainSQL($cmdstr)
  { //takes a plain (no bound variables) SQL command and executes it
      global $db_conn, $success;

      $statement = oci_parse($db_conn, $cmdstr);


      if (!$statement) {
          echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
          $e = OCI_Error($db_conn); 
          echo htmlentities($e['message']);
          $success = False;
      }

      $r = oci_execute($statement, OCI_DEFAULT);
      if (!$r) {
          echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
          $e = oci_error($statement); 
          echo htmlentities($e['message']);
          $success = False;
      }

      return $statement;
  }

  function executeBoundSQL($cmdstr, $list)
  {


      global $db_conn, $success;
      $statement = oci_parse($db_conn, $cmdstr);

      if (!$statement) {
        //   echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        //   $e = OCI_Error($db_conn);
        //   echo htmlentities($e['message']);
          $success = False;
      }

      foreach ($list as $tuple) {
          foreach ($tuple as $bind => $val) {
            //   echo $val;
            //   echo "<br>".$bind."<br>";
              oci_bind_by_name($statement, $bind, $val);
              unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
          }

          $r = oci_execute($statement, OCI_DEFAULT);
          if (!$r) {
            //   echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            //   $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
            //   echo htmlentities($e['message']);
            //   echo "<br>";
              $success = False;
          }
      }
}

  function printResult($result)
    { //prints results from a select statement
     // echo "<br>Retrieved data from table Lawyer:<br>";
      echo "<table>";
      echo "<tr><th>#.</th><th>Type</th><th>Location</th><th>dateLogged</th><th>resolved</th></tr>";

      while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        echo "<tr><td>".$row["DID"]."</td><td>".$row["TYPE"]."</td><td>".$row["LOCATION"]."</td><td>".$row["DATELOGGED"]."</td><td>".$row["RESOLVED"]."</td></tr>"; //or just use "echo $row[0]"
      }

      echo "</table>";
    }

  function connectToDB()
    {
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
    }

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	function handleResetRequest()
	{
		global $db_conn;
		// Drop old table
		executePlainSQL("DROP TABLE Lawyer cascade constraints");

		// Create new table
		echo "<br> creating new table <br>";
		executePlainSQL("CREATE TABLE Lawyer (email VARCHAR(40) PRIMARY KEY, name VARCHAR(40), firm VARCHAR(40))");
		oci_commit($db_conn);
	}

    function handleInsertRequest()
	{
		global $db_conn, $success;

        $property = substr($_POST['insertDisclosure'],0,3);
        $location = $_POST['insLocation'];
        $type = $_POST['insType'];
        $date = $_POST['insDate'];
        $resolved = $_POST['insResolved'];

        $tuple = array (
            ":bind1" => $location
        );

        $all = array($tuple);

        $result = executePlainSQL("SELECT MAX(dID) FROM Disclosures WHERE pID='".$property."'");
        if ($success) {
            $row = OCI_Fetch_Array($result, OCI_ASSOC);
            if (isset($row["MAX(DID)"])) {
                $dID=++$row['MAX(DID)'];
            } else {
                $dID= 1;
            }
        }
        
        executeBoundSQL("INSERT INTO Disclosures VALUES('$dID', '$type', :bind1, date '$date', '$resolved', '$property')", $all);
        oci_commit($db_conn);
	}


    function handleDeleteRequest()
    {
        global $db_conn;

        $property = substr($_POST['deleteDisclosure'],1,3);
        $dID = substr($_POST['deleteDisclosure'],7,1);

		executePlainSQL("DELETE FROM Disclosures WHERE pID='".$property."' AND dID='".$dID."'");
		oci_commit($db_conn);

        echo "<br> Deleted disclosure #".$dID." from Property #".$property." <br>";
    }

    function handleUpdateRequest()
    {
        global $db_conn;
        $property = substr($_POST['updateDisclosure'],1,3);
        $dID = substr($_POST['updateDisclosure'],7,1);

        $location = $_POST['upLocation'];
        $type = $_POST['upType'];
        $date = $_POST['upDate'];
        $resolved = $_POST['upResolved'];

        $tuple = array (
            ":bind1" => $location
        );

        $all = array($tuple);

        if ($location != '') {
            executeBoundSQL("UPDATE Disclosures SET location=:bind1 WHERE dID='".$dID."' AND pid='".$property."'", $all);
            oci_commit($db_conn);
            echo "<p>Changed location for Disclosure ".$dID." to " .$location. "<p>";
        }

        if ($type != '') {
            executePlainSQL("UPDATE Disclosures SET type='" . $type . "' WHERE dID='".$dID."' AND pid='".$property."'");
            oci_commit($db_conn);
            echo "<p>Changed type for Disclosure ".$dID." to " .$type. "<p>";
        }

        if ($date != '') {
            executePlainSQL("UPDATE Disclosures SET dateLogged=date'".$date."' WHERE dID='".$dID."' AND pid='".$property."'");
            oci_commit($db_conn);
            echo "<p>Changed date logged for Disclosure ".$dID." to " .$date. "<p>";
        }

        if ($resolved != '') {
            executePlainSQL("UPDATE Disclosures SET resolved='".$resolved. "' WHERE dID='".$dID."' AND pid='".$property."'");
            oci_commit($db_conn);
            echo "<p>Changed resolved status for Disclosure ".$dID." to " .$resolved. "<p>";
        }
        
    }

	function handleDisplayRequest()
	{
		global $db_conn, $propertySelected;
        $property = substr($_POST['propertySelect'], 1, 3);
        $propertySelected = $property;

		$result = executePlainSQL("SELECT * FROM Disclosures WHERE pid='".$property."'");
		printResult($result);

	}

    function handleMinDisclosure()
    {
        global $db_conn;
        // echo"<p>test<p>";
        $result = executePlainSQL("SELECT d.pid, MIN(listPrice), COUNT(d.pid) FROM Property p, Disclosures d WHERE p.pid = d.pid GROUP BY d.pid HAVING COUNT(*)>1");
        // echo "<p>test<p>";
        echo "<table>";
        echo "<tr><th>Property #</th><th>List Price</th><th>#. Disclosures</th></tr>";
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>".$row["PID"]."</td><td>".$row["MIN(LISTPRICE)"]."</td><td>".$row["COUNT(D.PID)"]."</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";
    }

    function handleAllDisclosure()
    {
        global $db_conn;

        $result = executePlainSQL("SELECT DISTINCT d.pid, p.postalCode, p.streetNum from disclosures d, property p WHERE NOT EXISTS ((SELECT d1.type from disclosures d1) MINUS (SELECT d2.type from disclosures d2 WHERE d2.pid=d.pid)) AND p.pid=d.pid");
        
        echo "<table>";
        echo "<tr><th>Property ID</th><th>Postal Code</th><th>Street #.</th></tr>";
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr><td>".$row["PID"]."</td><td>".$row["POSTALCODE"]."</td><td>".$row["STREETNUM"]."</td></tr>"; //or just use "echo $row[0]"
        }
        echo "</table>";

    }

  // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			} else if (array_key_exists('deleteQueryRequest', $_POST)) {
                handleDeleteRequest();
            } else if (array_key_exists('updateQueryRequest', $_POST)) {
                handleUpdateRequest();
            } else if (array_key_exists('viewProperty', $_POST)) {
                handleDisplayRequest();
            } else if (array_key_exists('minDisclosureRequest', $_POST)) {
                handleMinDisclosure();
            } else if (array_key_exists('allDisclosureRequest', $_POST)) {
                handleAllDisclosure();
            }

			disconnectFromDB();
		}
	}


	if (isset($_POST['reset']) || isset($_POST['updateSubmit'])
        || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])
        || isset($_POST['viewProperty']) || isset($_POST['minDisclosureSubmit'])
        || isset($_POST['allDisclosureSubmit'])) {
		handlePOSTRequest();
	}


  ?>
</body>
</html>