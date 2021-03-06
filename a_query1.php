<?php 

//$year = $_POST['year'];
$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];
$gender = $_POST['gender'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = 
"SELECT A.YEAR AS YEARS, ROUND((A.NUMBER_OF_ACCIDENTS_MALE/B.TOTAL_ACCIDENTS)*100, 3) AS RATE
FROM (SELECT COUNT(*) AS NUMBER_OF_ACCIDENTS_MALE, EXTRACT(YEAR FROM DATE_ACCIDENT) AS YEAR
      FROM ACCIDENT a,  NMONGIA.CASUALTY c,   SEX s
      WHERE a.accident_index = c.accident_index
      and c.sex_of_casualty = s.code
      AND S.label = '$gender'
      AND EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
      GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
      ORDER BY EXTRACT(YEAR FROM DATE_ACCIDENT)) A,
      (SELECT COUNT(*) AS TOTAL_ACCIDENTS, EXTRACT(YEAR FROM DATE_ACCIDENT) AS YEAR
       FROM ACCIDENT JOIN NMONGIA.CASUALTY
                     ON ACCIDENT.ACCIDENT_INDEX = NMONGIA.CASUALTY.ACCIDENT_INDEX
       WHERE EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
       GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
       ORDER BY EXTRACT(YEAR FROM DATE_ACCIDENT)) B
WHERE A.YEAR = B.YEAR";

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

$chart_data = " ";
while($row = oci_fetch_array($s, OCI_BOTH)){
  //$data[] = $row;
  //'" < These quotes + Double quotes below on year represent X-Axis > "'
  $chart_data .= "{ label:'".$row["YEARS"]."', value:".$row["RATE"]."}, ";
}
//To remove last comma from $chart_data
$chart_data = substr($chart_data, 0, -2);

?>


<!DOCTYPE html>
<html>
 <head>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
  
 </head>
 <body>
  <center>
  <br /><br />
  <div class="container" style="width:1000px;">
   <!--<h2 align="center">Morris.js chart with PHP & Mysql</h2>-->
   <h1 align="center">GENDER WISE DRIVER'S ACCIDENTS STATISTICS.</h3>   
   <br /><br />
   <div id="chart"></div>
  </div>
  <button id="back" class="btn btn-primary">Back</button>
</center>
 </body>
</html>

<script>
Morris.Donut({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],

});
</script>
<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "accident.html";
    };
</script>