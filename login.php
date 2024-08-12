<?php

// ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors
$failedBefore = false;
$failedLoginBefore = false;
$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Agent Login</title>
</head>
<body>


      <h1 id="login-heading">Welcome, please log in or create an account to continue</h1>
      <div class="login-parent">
      <form method="POST" action="login.php">
      <input type="hidden" id="checkLogin" name="checkLogin">
              <div class="login">

                  <h2>Agent Login</h2>

                  <input type="text" id="email" name="email" placeholder="Email" class="loginput" required>
                  <input type="password" id="password" name="password" placeholder="Password" class="loginput" required>
                  <input type="text" class="hidden">
                  <label for="lrd" class="hidden">License Renewal Date</label>
                  <input type="date" id="lrd" class="hidden">

                  <button type="submit" class="generalbtn" name="loginSubmit">Login</button>

              </div>
      </form>

      <form method="POST" action="login.php">
      <input type="hidden" name="signInRequest">
          <div class="login">
              <h2>Create Account</h2>
              <input type="text" id="email" name="newEmail" placeholder="Email" class="loginput" required>
              <input type="text" id="password" name="newPass" placeholder="Password" class="loginput" required>
              <input type="text" name="newName" placeholder="Name" required>
              <label for="lrd">Licence Renewal Date</label>
              <input type="date" name="date" id="lrd">
              <button type="submit" class="generalbtn" name="signInSubmit">Create Account</button>
              <?php
                function printError() {
                    echo "<p>Account with that email already exists!<p>";
                }
              ?>
          </div>
      </form>
          </div>

    <?php
    function isSanitized($dirty) {
        return !(str_contains($dirty,"\INSERT") || str_contains($dirty,"insert") || str_contains($dirty, "DROP") || str_contains($dirty, "drop") || str_contains($dirty, "\SELECT") || str_contains($dirty, "select"));
    }

    function handleLogin() {
        global $db_conn;
        global $success;
        global $failedLoginBefore;

        $email = $_POST['email'];
        $password = $_POST['password'];

        $tuple = array(":bind1" => $email);
        $all = array($tuple);
        $result = executeBoundSQL("SELECT * FROM agent WHERE EMAIL=:bind1", $all);
        // $result = executePlainSQL("SELECT * FROM agent WHERE EMAIL='$email'");


        if ($success) {
            $row = OCI_Fetch_Array($result, OCI_ASSOC);


            if ($row["PASSWORD"] === $password) {
                header('Location: propertyList.php');
                die;
            } else if (!$failedLoginBefore) {
                echo "<p>No account with that email and/or password exists!<p>";
                $failedLoginBefore = true;
            }

        }
    }

    function handleSignIn() {
        global $db_conn;
        global $success;
        global $failedBefore;

        $email = $_POST['newEmail'];
        $name = $_POST['newName'];
        $password = $_POST['newPass'];
        $date = $_POST['date'];

        $tuple = array (
            ":bind1" => $email,
            ":bind2" => $name,
            ":bind3"=> $password
        );

        $all = array($tuple);
        
        executeBoundSQL("insert into Agent values (:bind1, :bind2, :bind3, date '$date')", $all);
        
        if($success) {
            oci_commit($db_conn);
            header("Location: propertyList.php");
        } else if (!$failedBefore) {
            printError();
            $failedBefore = true;
        }
        $success = true;
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
              $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
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
      function executeBoundSQL($cmdstr, $list) {
       
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
              //   echo $val;
              //   echo "<br>".$bind."<br>";
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
            return $statement;
        }}

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

      // HANDLE ALL POST ROUTES

    	function handlePOSTRequest()
    	{
    		if (connectToDB()) {
    			if (array_key_exists('checkLogin', $_POST)) {
    			    handleLogin();
    			} else if (array_key_exists('signInRequest', $_POST)) {
    			    handleSignIn();
    			}
    			disconnectFromDB();
    		}
    	}

    	if (isset($_POST['loginSubmit']) || isset($_POST['signInSubmit'])) {
    		handlePOSTRequest();
    	}

      ?>

</body>
</html>


