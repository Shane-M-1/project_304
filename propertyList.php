<?php

// ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters


$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

$failedBefore = false;


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Property List</title>
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
      <h1>Property Listings</h1>
      <a href="newProperty.php"><button class="generalbtn">Add New Property</button></a>
      <form method="POST" action="propertyList.php">
      <input type="hidden" name="projectRequest">
        <table>
          <th>Show Property Attributes</th>
          <tbody>
          <tr>
            <td><input type="checkbox" name="check[]" value="listDate">
            <label for="listDate">Listing Date</label></td>
            <td><input type="checkbox" name="check[]" value="listPrice">
            <label for="listPrice">Listing Price</label></td>
            <td><input type="checkbox" name="check[]" value="pID">
            <label for="pID">Property ID</label></td>
            
          </tr>
          <tr>
            <td><input type="checkbox" name="check[]" value="numBath">
            <label for="numBath"># Bath(s)</label></td>
            <td><input type="checkbox" name="check[]" value="govtValuation">
            <label for="govtValuation">Gov. Valuation</label></td>
            <td><input type="checkbox" name="check[]" value="sqft">
            <label for="sqft">Sq. footage</label></td>
            
          </tr>
          <tr>
            <td><input type="checkbox" name="check[]" value="streetNum">
            <label for="streetNum">Street #</label></td>
            <td><input type="checkbox" name="check[]" value="salePrice">
            <label for="salePrice">Sale Price</label></td>
            <td><input type="checkbox" name="check[]" value="dateOfSale">
            <label for="dateOfSale">Date of Sale</label></td>
            
          </tr>
          <tr>
            <td><input type="checkbox" name="check[]" value="A_email">
            <label for="A_email">Agent Email</label></td>
            <td><input type="checkbox" name="check[]" value="city">
            <label for="city">City</label></td>
            <td><input type="checkbox" name="check[]" value="province">
            <label for="province">Province</label></td>
          </tr>
          <tr>
            <td><input type="checkbox" name="check[]" value="numBed">
            <label for="numBed"># Bed(s)</label></td>
            <td><input type="checkbox" name="check[]" value="postalCode">
            <label for="postalCode">Postal Code</label></td>
            <td><input type="checkbox" name="check[]" value="B_email">
            <label for="B_email">Buyer Email</label></td>
          </tr>
          <tr><td><button type="submit" name="projectSubmit" class="generalbtn" id="showProp">Show Properties</button></td></tr>
          </tbody>

          </table>
          
       
       <h4>Filter Properties</h4>
       
       <button id="addFilter" class="generalbtn" name="addFilter" type="button">Add Filter</button>
       <input type="hidden" name="filterRequest" id="hiddenNum">
  
       <br>
       <div id="filtercontainer"></div>
       </form>
       
       <hr />
       <h4>Demo Queries</h4>
       <form method="GET" action="propertyList.php" style="display: inline;">
       <input type="hidden" name="getAboveAverageRequest" id="getAboveAverageRequest">
       Gets average list price of cities with average listed prices higher than the average of all properties:
       <button type="submit" class="generalbtn" name="aboveAverageSubmit" id="aggregationQuery">GET</button> 
       </form>
       <br>
       <form method="GET" action="propertyList.php" style="display: inline;">
        <input type="hidden" name="groupByRequest">
        <input type="radio" name="groupBy" id="max" value="max">
        <label for="max">See highest property price per city</label>
        <input type="radio" name="groupBy" id="min" value="min">
        <label for="min">See lowest property price per city</label>
        <input type="radio" name="groupBy" id="avg" value="avg">
        <label for="avg">See average property sq. ft per city</label>
        <br>
        <button type="submit" name="groupBySubmit" class="generalbtn">GET</button>  
       </form>
       <hr />


       <script type="text/javascript">
       const btn = document.getElementById("addFilter");
       let flag = false;
       let num = 0;
       btn.addEventListener('click', function() {

            const selectCon = document.createElement("select");
            selectCon.name = "con" + num;
            const selectExp = document.createElement("select");
            selectExp.name = "exp" + num;
            const and = document.createElement("option");
            const or = document.createElement("option");
            and.value = "AND";
            and.text = "AND";
            or.value = "OR";
            or.text = "OR";

            const eq = document.createElement("option");

            const geq = document.createElement("option");
            const leq = document.createElement("option");
            const neq = document.createElement("option");
            const less = document.createElement("option");
            const greater = document.createElement("option");

            eq.value = "=";
            eq.text = "=";
            geq.value = ">=";
            geq.text = ">=";
            neq.value = "<>";
            neq.text = "<>";
            leq.value = "<=";
            leq.text = "<=";
            less.value = "<";
            less.text = "<";
            greater.value = ">";
            greater.text = ">";

            const pid = document.createElement("option");
            pid.value = "pID";
            pid.text = "Property ID";
            const listDate = document.createElement("option");
            listDate.value = "listDate";
            listDate.text = "Listing Date";
            const listPrice = document.createElement("option");
            listPrice.value = "listPrice";
            listPrice.text = "Listing Price";
            const numBed = document.createElement("option");
            numBed.value = "numBed";
            numBed.text = "# Bed(s)";
            const numBath = document.createElement("option");
            numBath.value = "numBath";
            numBath.text = "# Bath(s)";
            const gov = document.createElement("option");
            gov.value = "govtValuation";
            gov.text = "Gov. Valuation";
            const sqft = document.createElement("option");
            sqft.value = "sqft";
            sqft.text = "Sq. Footage";
//             const postalCode = document.createElement("option");
//             postalCode.
            const city = document.createElement("option");
            city.value = "city";
            city.text = "City";
            const prov = document.createElement("option");
            prov.value = "province";
            prov.text = "Province";
//             const streetNum = document.createElement("option");
            const b_email = document.createElement("option");
//             const a_email = document.createElement("option");
            // const floorNum = document.createElement("option");
            // floorNum.value = "floorNum";
            // floorNum.text = "Floor";
            // const yardSize = document.createElement("option");
            // yardSize.value = "yardSize";
            // yardSize.text = "Yard Size (sq.ft)";


            const text1 = document.createElement("select")
            text1.name = "first" + num;
            const text2 = document.createElement("input");
            text2.type = "text";
            text2.name = "second" + num;
            text2.required = true;
            text2.placeholder = "name, number, or date (DD-MM-YYYY)";

            const div = document.getElementById("filtercontainer");
            if (flag) {
                div.appendChild(selectCon);
                selectCon.appendChild(and);
                selectCon.appendChild(or);
            }
             flag = true;

             addKids(text1, [pid, listDate, listPrice, numBed, numBath, gov, sqft, city, prov]);
             addKids(selectExp, [eq, geq, leq, neq, greater, less]);

             div.appendChild(text1);
             div.appendChild(selectExp); 
             div.appendChild(text2);

             document.getElementById("hiddenNum").value = num;
             num++;
       });

       const showbtn = document.getElementById("showProp");
       showbtn.addEventListener('click', function () {
          num = 0;
       });

       function addKids(parent, children) {
            for(let child of children) {
                parent.appendChild(child);
            }
       }
       </script>


  <?php 
  function isSanitized($dirty) {
    return !(str_contains($dirty,"\INSERT") || str_contains($dirty,"insert") || str_contains($dirty, "DROP") || str_contains($dirty, "drop") || str_contains($dirty, "\SELECT") || str_contains($dirty, "select"));
  }

  function printError($msg) {
    echo "<p>" . $msg . "</p>";
  }

  function friendlyName($attribute) {
    if ($attribute == "pID") {
      return "Property ID";
    } else if ($attribute == "listDate") {
      return "Listing Date";
    } else if ($attribute == "listPrice") {
      return "Listing Price";
    } else if ($attribute == "max(listPrice)") {
      return "Max List Price";
    } else if ($attribute == "min(listPrice)") {
      return "Minimum List Price";
    } else if ($attribute == "avg(sqft)") {
      return "Average Sq. footage";
    } else if ($attribute == "B_email") {
      return "Buyer Email";
    } else if ($attribute == "govtValuation") {
      return "Gov. Valuation";
    } else if ($attribute == "sqft") {
      return "Sq. footage";
    } else if ($attribute == "numBath") {
      return "# Bath(s)";
    } else if ($attribute == "numBed") {
      return "# Bed(s)";
    } else if ($attribute == "salePrice") {
      return "Sale Price";
    } else if ($attribute == "streetNum") {
      return "Street #";
    } else if ($attribute == "dateOfSale") {
      return "Date of Sale";
    } else if ($attribute == "A_email") {
      return "Agent Email";
    } else if ($attribute == "postalCode") {
      return "Postal Code";
    } else if ($attribute == "province") {
      return "Province";
    } else if ($attribute == "city") {
      return "City";
    } else {
      return $attribute;
    }
  }

  function handleGroupBy() {
    // query for most expensive property by city
    // OR cheapest property by city
    // OR avg sqft by city
    global $success;
    global $failedBefore;
    $val = $_GET['groupBy'];
    $statement = "";
    $attrs = [];
    if ($val == 'max') {
      $statement = "SELECT city, MAX(listPrice) FROM Property p, PropertyAddress pa WHERE p.postalCode = pa.postalCode GROUP BY city HAVING count(*) > 0";
      $attrs[0] = "city";
      $attrs[1] = "max(listPrice)";
    } else if ($val == "min") {
      $statement = "SELECT city, MIN(listPrice) FROM Property p, PropertyAddress pa WHERE p.postalCode = pa.postalCode GROUP BY city";
      $attrs[0] = "city";
      $attrs[1] = "min(listPrice)";
    } else {
      $statement = "SELECT city, AVG(sqft) FROM Property p, PropertyAddress pa WHERE p.postalCode = pa.postalCode GROUP BY city";
      $attrs[0] = "city";
      $attrs[1] = "avg(sqft)";
    }
    
    $result = executePlainSQL($statement);

    if($success) {
      printProjection($result, $attrs);
    } else if (!$failedBefore) {
        printError("Uh oh, encountered an error, please refresh and try again!");
        $failedBefore = true;
    }
    $success = true;
    
  }
  
  function handleProjection() {
    global $success;
    global $failedBefore;
    $attrs = [];
    $statement = "";
    
    if (array_key_exists("check", $_POST)) {
      $i = 0;
      foreach($_POST['check'] as $val) {
        if ($val != "") {
          $attrs[$i] = $val;
          if ($val === "postalCode") {
            $val = "p." . $val;
          }
          if ($i != 0) {
            $statement .= ", " . $val; 
          } else {
            $statement .= $val;
          }
        }
        $i++;
      }
    }
    
    $statement = "SELECT " . $statement . " FROM Property p, PropertyAddress pa WHERE p.postalcode = pa.postalCode";
    $result = "";

    if (array_key_exists("first0", $_POST)) {
      $statement .= " AND (";
      $result = handleFilterRequest($statement);
    } else {
      $result = executePlainSQL($statement);
    }
    if($success) {
      printProjection($result, $attrs);
    } else if (!$failedBefore) {
        printError("Please select at least one attribute and/or specify a valid filter!");
        $failedBefore = true;
    }
    $success = true;
    
    
  }

  function printProjection($result, $attrs) {
    echo "<table>";
    echo "<tr>";
    foreach($attrs as $temp) {
      echo "<th>" . friendlyName($temp) . "</th>";
    }
    echo "</tr>";
    
    
    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
      echo "<tr>";
      foreach($attrs as $temp) {
        if (array_key_exists(strtoupper($temp), $row)) { //$row[strtoupper($temp)]
          echo "<td>" . $row[strtoupper($temp)] . "</td>";
        } else {
          echo "<td>N/A</td>";
        }
        
      }
      echo "</tr>";
    }
     echo "</table>";
  }

  function handleFilterRequest($statement) {
    global $success;
    $num = $_POST['filterRequest'];
    $num = (int) $num;
    $i = -1;

    while (++$i <= $num) {
        $temp1 = $_POST['first' . $i];
        $temp2 = $_POST['exp' . $i];
        $temp3 = $_POST['second' . $i];
        if (!isSanitized("\\" . $temp3)) {
          printError('NICE TRY');
          $success = false;
          return null;
        }
        if ($i != 0) {
          $temp4 = $_POST['con' . $i];
          $statement .= " " . $temp4 . " " . $temp1 . " " . $temp2 . " " . "'" . $temp3 . "'";
        } else {
          $statement .= $temp1 . " " . $temp2 . " " . "'" . $temp3 . "'" . " ";
        }
    }
    $result = executePlainSQL($statement . ")");
    return $result;
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
          // echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
          // $e = oci_error($statement); // For oci_execute errors pass the statementhandle
          // echo htmlentities($e['message']);
          $success = False;
      }

      return $statement;
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

	function getAboveAverageCities()
  {
    global $db_conn;
    $result = executePlainSQL("SELECT city, avg(listprice) FROM property p, propertyAddress pa WHERE p.postalcode=pa.postalcode GROUP BY city HAVING avg(listPrice) > (SELECT avg(listPrice) from Property)");


    echo "<table>";
    echo "<tr><th>City</th><th>Average List Price</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        echo "<tr><td>".$row["CITY"]."</td><td>".$row["AVG(LISTPRICE)"]."</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
  }

  // HANDLE ALL POST ROUTES
	
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('projectRequest', $_POST)) {
        handleProjection();
      }
			disconnectFromDB();
		}
	}
	// HANDLE ALL GET ROUTES

	function handleGETRequest()
	{
		if (connectToDB()) {
      if (array_key_exists('getAboveAverageRequest', $_GET)) {
				getAboveAverageCities();
      } else if (array_key_exists('groupByRequest', $_GET)) {
        handleGroupBy();
      }

			disconnectFromDB();
		}
	}

	if (isset($_POST['projectSubmit'])) {
		handlePOSTRequest();
  } else if (isset($_GET['aboveAverageSubmit']) || isset($_GET['groupBySubmit'])) {
    handleGETRequest();
  }

  ?>
</body>
</html>