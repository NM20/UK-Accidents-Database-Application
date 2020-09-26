<?php 

//$year = $_POST['year'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = "SELECT SUM(A.CNT) AS TUPLE_COUNT FROM
(SELECT COUNT(*) AS CNT FROM ACCIDENT 
UNION 
SELECT COUNT(*) AS CNT FROM LOCATION 
UNION 
SELECT COUNT(*) AS CNT FROM REGION 
UNION 
SELECT COUNT(*) AS CNT FROM CONDITIONS 
UNION 
SELECT COUNT(*) AS CNT FROM NMONGIA.CASUALTY 
UNION 
SELECT COUNT(*) AS CNT FROM AKHATRI.VEHICLE 
UNION 
SELECT COUNT(*) AS CNT FROM DRIVER) A";

$c = oci_connect($username, $password, $database);
if (!$c) {
    $m = oci_error();
    trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
}
$s = oci_parse($c, $query);
if (!$s) {
    $m = oci_error($c);
    trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
}
$r = oci_execute($s);
if (!$r) {
    $m = oci_error($s);
    trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
}


$row = oci_fetch_array($s, OCI_BOTH);

  //$data[] = $row;
  //'" < These quotes + Double quotes below on year represent X-Axis > "'
  $chart_data = $row["TUPLE_COUNT"];

//To remove last comma from $chart_data
$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
  
 </head>
 <body class="bg">
  <center>
  <br /><br />
  <div class="container" style="width:1000px;">
   <!--<h2 align="center">Morris.js chart with PHP & Mysql</h2>-->
   <h1 align="center">TUPLE COUNT</h3>   
   <br /><br/>
   <h1 class="tuplecount"><?=$row['TUPLE_COUNT']?></h1>
  </div>
  <button id="back" class="btn btn-primary">Back</button>
</center>
 </body>
</html>

<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "home.php";
    };
</script>