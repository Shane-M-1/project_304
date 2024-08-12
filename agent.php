<?php


$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; 

?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Agent Info</title>
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
    <h1>Agent Information</h1>
    <h2>Current Agents</h2>
    <?php
    if (connectToDB()) {
          $result = executePlainSQL("SELECT * FROM Agent");
          printResult($result);
      }
      disconnectFromDB();
     ?>
    <hr>
    

  <?php
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
        //   echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        //   $e = OCI_Error($db_conn);
        //   echo htmlentities($e['message']);
          $success = False;
      }

      $r = oci_execute($statement, OCI_DEFAULT);
      if (!$r) {
        //   echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        //   $e = oci_error($statement); 
        //   echo htmlentities($e['message']);
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
              //echo $val;
              //echo "<br>".$bind."<br>";
              oci_bind_by_name($statement, $bind, $val);
              unset($val);
          }

          $r = oci_execute($statement, OCI_DEFAULT);
          if (!$r) {
            //   echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            //   $e = OCI_Error($statement);
            //   echo htmlentities($e['message']);
            //   echo "<br>";
              $success = False;
          }
      }
  }

  function printResult($result)
  { //prints results from a select statement
      echo "<table>";
      echo "<tr><th>Email</th><th>Name</th><th>License Renewal Date</th></tr>";

      while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        if (array_key_exists("LICENSERENEWALDATE", $row)) {
          echo "<tr><td>" . $row["EMAIL"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["LICENSERENEWALDATE"] . "</td></tr>"; 
        } else {
          echo "<tr><td>" . $row["EMAIL"] . "</td><td>" . $row["NAME"] . "</td><td>N/A</td></tr>"; 
        }
        
      }

      echo "</table>";
  }

  function connectToDB()
	{
		global $db_conn;
		global $config;

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

	function handleDisplayRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM Lawyer");
		printResult($result);
	}

    function handleDisplayOption() {
        global $db_conn;
		$result = executePlainSQL("SELECT * FROM Lawyer");

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
          echo "<option>" . $row["EMAIL"] . "</option>";}
    }


	// HANDLE ALL GET ROUTES
	function handleGETRequest()
	{
		if (connectToDB()) {
            if (array_key_exists('displayTuples', $_GET)) {
				handleDisplayRequest();
			}

			disconnectFromDB();
		}
	}


	if (isset($_GET['displayTuplesRequest'])) {
		handleGETRequest();
	}

  ?>
</body>
</html>