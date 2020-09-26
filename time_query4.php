<?php 

//$year = $_POST['year'];
$YEAR = $_POST['year'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = "SELECT *
FROM ( WITH MORNING_ACC AS 
(
SELECT count(a.accident_index) AS MORNING
FROM ACCIDENT A , NMONGIA.A_TIME atime
WHERE A.ACCIDENT_INDEX=atime.ACCIDENT_INDEX
AND  EXTRACT(YEAR FROM DATE_ACCIDENT) = '$YEAR'
AND EXTRACT(HOUR FROM atime.Time) BETWEEN 6 AND 12
GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
)
,

AFTERNOON_ACC AS
(
SELECT count(a.accident_index) AS AFTERNOON
FROM ACCIDENT A , NMONGIA.A_TIME atime
WHERE A.ACCIDENT_INDEX=atime.ACCIDENT_INDEX
AND  EXTRACT(YEAR FROM DATE_ACCIDENT) = '$YEAR'
AND EXTRACT(HOUR FROM atime.Time) BETWEEN 13 AND 16
GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
)
,

EVENING_ACC AS
(
SELECT count(a.accident_index) AS EVENING
FROM ACCIDENT A , NMONGIA.A_TIME atime
WHERE A.ACCIDENT_INDEX=atime.ACCIDENT_INDEX
AND  EXTRACT(YEAR FROM DATE_ACCIDENT) = '$YEAR'
AND EXTRACT(HOUR FROM atime.Time) BETWEEN 17 AND 21
GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
)
,

NIGHT_ACC AS
(
SELECT count(a.accident_index) AS NIGHT
FROM ACCIDENT A , NMONGIA.A_TIME atime
WHERE A.ACCIDENT_INDEX=atime.ACCIDENT_INDEX
AND  EXTRACT(YEAR FROM DATE_ACCIDENT) = '$YEAR'
AND (EXTRACT(HOUR FROM atime.Time) BETWEEN 22 AND 23
OR EXTRACT(HOUR FROM atime.Time) BETWEEN 0 AND 5)
GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
)
,

TOTAL_ACC AS
(
SELECT count(a.accident_index) AS TOTAL
FROM ACCIDENT A , NMONGIA.A_TIME atime
WHERE A.ACCIDENT_INDEX=atime.ACCIDENT_INDEX
AND  EXTRACT(YEAR FROM DATE_ACCIDENT) = '$YEAR'
GROUP BY EXTRACT(YEAR FROM DATE_ACCIDENT)
)

SELECT 'MORNING' AS TIME, ROUND((MORNING/TOTAL*100),2) as Rate  FROM MORNING_ACC, TOTAL_ACC
UNION
SELECT 'AFTERNOON' AS TIME, ROUND((AFTERNOON/TOTAL*100),2) as Rate FROM AFTERNOON_ACC, TOTAL_ACC
UNION
SELECT 'EVENING' AS TIME,ROUND((EVENING/TOTAL*100),2) as Rate FROM EVENING_ACC, TOTAL_ACC
UNION
SELECT 'NIGHT' AS TIME, ROUND((NIGHT/TOTAL*100),2) as Rate FROM NIGHT_ACC, TOTAL_ACC)
";

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
  $chart_data .= "{ label:'".$row["TIME"]."', value:".$row["RATE"]."}, ";
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
   <h1 align="center">Time Wise Accident Trend.</h3>   
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
 data:[<?php echo $chart_data; ?>]
});
</script>
<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "time.html";
    };
</script>