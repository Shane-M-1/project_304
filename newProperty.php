<?php

// ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>List New Property</title>
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
  <h1>List New Property</h1>
  <section>
    <form method="POST", action="newProperty.php" id="property-form">
      <table class="new-property">
        <tr>
        <td>Agent Email:</td>
        <td>
        <select name="agentEmail">
        <?php
        if (connectToDB()) {
            $result = executePlainSQL("SELECT * FROM Agent");

            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                echo "<option>" . $row["EMAIL"] ."</option>"; //or just use "echo $row[0]"
            }
        }
        disconnectFromDB();
        ?>
        </select></td>
        </tr>

        <tr>
          <td>
            <label>
                <input type="radio" name="propertyType" value="house" id='houseType' checked> House
            </label>
            <label>
                <input type="radio" name="propertyType" value="apt" id='aptType'> Apartment
            </label>
          </td>
        </tr>

        <tr id="aptRow" style="display: none;">
            <td><label for="apartmentNumber">Apartment Number:</label></td>
            <td><input type="number" id="apartmentNumber" name="apartmentNumber"></td>
            <td><label for="floorNum">Floor Number:</label></td>
            <td><input type="number" id="floorNum" name="floorNum"></td>
        </tr>

        <tr id="bm-row" style="display: none;">
          <td>Building Manager:</td>
          <td><select id="bm-email" name='bm'>
          <?php
          if (connectToDB()) {
            $result = executePlainSQL("SELECT * FROM BuildingManager");

            while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
              echo "<option>" . $row["EMAIL"] ."</option>"; //or just use "echo $row[0]"
            }
          }
          disconnectFromDB();
          ?>
          </select></td>
          <td>Maintenance Fee:</td>
          <td><input type ='number' id="maintenanceFee" name='maintenanceFee'></td>
        </tr>

        <tr id="houseRow">
            <td><label for="yardSize">Yard Size:</label></td>
            <td><input type="number" id="yardSize" name="yardSize" required></td>
        </tr>

        <tr>
          <td> Square Footage</td>
          <td><input type="number" name="sqft" required></td>
        </tr>



        <tr>
          <td><label for="numBed"># Bed(s)</label></td>
          <td><input type="number" name="numBed" required></td>
          <td><label for="numBath"># Bath(s)</label></td>
          <td><input type="number" name="numBath" required></td>
        </tr>

        <tr>
          <td><label for="st">Street</label></td>
          <td><input type="text" placeholder="123 Main St" name="stNum" required></td>
          <td><label for="postal">Postal Code ("A1B2C3")</label></td>
          <td><input type="text" placeholder="A2C 3B4 " name="postCode" pattern="[A-Z][0-9][A-Z] [0-9][A-Z][0-9]" required></td>
        </tr>

        <tr>
          <td><label for="ct">City</label></td>
          <td><input type="text" placeholder="Vancouver" name="city" required></td>

          <td><label for="prov">Province</label></td>
          <td><input type="text" placeholder="BC" name="province" maxlength="2" minlength="2" required></td>
        </tr>

        <tr>
          <td><label for="value">Government Valuation</label></td>
          <td><input type="number" placeholder="500 000" name="govtVal" required></td>

          <td><label for="price">List Price</label></td>
          <td><input type="number" name="listPrice" placeholder="500 000" required></td>

          <td><label for="date">List Date</label></td>
          <td><input type="date" name="listDate" required></td>
        </tr>

        <tr>
          <td><label for="sellerSelect">Seller Email</label></td>
          <td>
          <select name="sellerSelect">
          <?php
          if (connectToDB()) {
              $result = executePlainSQL("SELECT * FROM Seller");

              while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
                  echo "<option>" . $row["EMAIL"] ."</option>"; //or just use "echo $row[0]"
              }
          }
          disconnectFromDB();
          ?>
          </select></td>
          <td><label for="price">Bought Price</label></td>
          <td><input type="number" name="boughtPrice" placeholder="500 000" required></td>
          <td><label for="date">Bought Date</label></td>
          <td><input type="date" name="boughtDate" required></td>
        </tr>
        <tr>
        <td><button type="submit" name="insertSubmit" id="insertSubmit" class="generalbtn">Add Property</button></td>
        </tr>
      </table>
  </form>

  <hr />

  <h2>View Disclosures</h2>
  <a href="disclosure.php"><button class="generalbtn">View Disclosures</button></a>


  <script type="text/javascript">
  const apt = document.getElementById("aptType");
  const house = document.getElementById("houseType");

  const houseRow = document.getElementById("houseRow");
  const aptRow = document.getElementById("aptRow");
  const bmRow = document.getElementById("bm-row");

  apt.addEventListener('click', function() {
    houseRow.style.display = "none";
    aptRow.style.display = "table-row";
    bmRow.style.display = "table-row";

    document.getElementById("apartmentNumber").required = true;
    document.getElementById("floorNum").required = true;
    document.getElementById("bm-email").required = true;
    document.getElementById("maintenanceFee").required = true;
    document.getElementById("yardSize").required = false;

  });
  
  house.addEventListener('click', function() {
    aptRow.style.display = "none";
    bmRow.style.display = "none";
    houseRow.style.display = "table-row";


    document.getElementById("apartmentNumber").required = false;
    document.getElementById("floorNum").required = false;
    document.getElementById("bm-email").required = false;
    document.getElementById("maintenanceFee").required = false;
    document.getElementById("yardSize").required = true;
  });
</script>


 <?php
  function sanitize($dirty) {
    if (str_contains($dirty,"INSERT") || str_contains($dirty,"insert") || str_contains($dirty,"DROP") || str_contains($dirty,"drop") || str_contains($dirty, "SELECT") || str_contains($dirty, "select")) {
      return false;
    } 
  }


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
            // echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            // $e = OCI_Error($db_conn);
            // echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                // echo $val;
                // echo "<br>".$bind."<br>";
                oci_bind_by_name($statement, $bind, $val);
                unset($val); 
            }

            $r = oci_execute($statement, OCI_DEFAULT);
            if (!$r) {
                // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                // $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
                // echo htmlentities($e['message']);
                // echo "<br>";
                $success = False;
            }
        }
    }

    function printResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table Lawyer:<br>";
        echo "<table>";
        echo "<tr><th>pid</th><th>streetnum</th><th>a_email</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
          echo "<tr><td>" . $row["PID"] . "</td><td>" . $row["STREETNUM"] . "</td><td>" . $row["A_EMAIL"] . "</td></tr>"; //or just use "echo $row[0]"
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
  			$e = OCI_Error(); 
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



      function handleInsertRequest()
  	{
  		global $db_conn;
      global $success;

  		//Getting the values from user and insert data into the table
      $nBed = $_POST['numBed'];
      $nBath = $_POST['numBath'];
      $govtVal = $_POST['govtVal'];
      $sqft = $_POST['sqft'];
      $post = $_POST['postCode'];
      $stNum = $_POST['stNum'];
      $city = $_POST['city'];
      $province = $_POST['province'];
      $listDate = $_POST['listDate'];
      $listPrice = $_POST['listPrice'];
      $agentEmail = $_POST['agentEmail'];
      
      $propType = $_POST['propertyType'];
      $aptNum = $_POST['apartmentNumber'];
      $floorNum = $_POST['floorNum'];
      $bm_email=$_POST['bm'];
      $maintenanceFee = $_POST['maintenanceFee'];

      $yard = $_POST['yardSize'];

      $s_email = $_POST['sellerSelect'];
      $boughtPrice=$_POST['boughtPrice'];
      $boughtDate=$_POST['boughtDate'];

      $result = executePlainSQL("SELECT * FROM propertyAddress WHERE postalCode='$post'");

      $tuple = array(":bind1" => $stNum);
      $all = array($tuple);

      $tuple2 = array(":bind1" => $city);
      $all2 = array($tuple2);

      if ($success) {
        $row = OCI_Fetch_Array($result, OCI_ASSOC);
        // echo "<p>" . $row["POSTALCODE"] . "<p>";
        if (isset($row["POSTALCODE"])) {
          executeBoundSQL("INSERT INTO property VALUES(pid_sequence.nextval, '$nBed', '$nBath', '$govtVal', '$sqft', '$post', :bind1, NULL, NULL, date '$listDate', '$listPrice', NULL, '$agentEmail')", $all);
          executePlainSQL("INSERT INTO Owns VALUES(pid_sequence.currval, '$s_email', '$boughtPrice', date '$boughtDate', 'Current Owner')");
        } else {
          executeBoundSQL("INSERT INTO propertyaddress VALUES ('$post', :bind1, '$province')", $all2);
  		    executeBoundSQL("INSERT INTO property VALUES(pid_sequence.nextval, '$nBed', '$nBath', '$govtVal', '$sqft', '$post', :bind1, NULL, NULL, date '$listDate', '$listPrice', NULL, '$agentEmail')", $all);
          executePlainSQL("INSERT INTO Owns VALUES(pid_sequence.currval, '$s_email', '$boughtPrice', date '$boughtDate', 'Current Owner')");
        }

        if ($propType == "apt") {
          executePlainSQL("INSERT INTO apartment2 values('$aptNum', '$floorNum')");
          executePlainSQL("INSERT INTO apartment1 values(pid_sequence.currval, '$aptNum', '$maintenanceFee', '$bm_email')");
        } else if ($propType=="house") {
          executePlainSQL("INSERT INTO house values(pid_sequence.currval, '$yard')");
        }
      }
      
      oci_commit($db_conn);
      echo "<p>New Property Inserted!</p>";
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

    // HANDLE ALL POST ROUTES
  	
  	function handlePOSTRequest()
  	{
  		if (connectToDB()) {
  			if (array_key_exists('insertSubmit', $_POST)) {
  				handleInsertRequest();
  			}

  			disconnectFromDB();
  		}
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

  	if (isset($_POST['insertSubmit'])) {
  		handlePOSTRequest();
  	} else if (isset($_GET['displayTuplesRequest'])) {
  		handleGETRequest();
  	}

    ?>
</body>
</html>
