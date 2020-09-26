<?php 

//$year = $_POST['year'];
$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = "SELECT years as YEARS, AVERAGE_FOR_HOLIDAYS AS AFH, AVERAGE_FOR_NORMAL AS AFN
FROM
(SELECT year as years, round((TOTAL_ACCIDENTS_HOLIDAY/TOTAL_DAYS),3) AS AVERAGE_FOR_HOLIDAYS
     FROM
          (SELECT COUNT(*) AS TOTAL_ACCIDENTS_HOLIDAY, COUNT(DISTINCT DATE_ACCIDENT) AS TOTAL_DAYS , EXTRACT(year from date_accident) as year
          FROM ACCIDENT 
          WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear' 
          AND (Extract (day from date_accident) = 25 AND Extract(month from date_accident) = 12 )
          OR (Extract (day from date_accident) = 30 AND Extract(month from date_accident) = 11 )
          OR (Extract (day from date_accident) = 17 AND Extract(month from date_accident) = 03 )
          OR (Extract (day from date_accident) = 31 AND Extract(month from date_accident) = 12 )
          OR (Extract (day from date_accident) = 01 AND Extract(month from date_accident) = 01 )
          OR (Extract (day from date_accident) = 26 AND Extract(month from date_accident) = 12 ) 
          Group By Extract(year from date_accident)
          Order by Extract(year from date_accident)
) )
 a,
(SELECT year, round((TOTAL_ACCIDENTS_NORMALDAYS/ TOTAL_DAYS),3) AS AVERAGE_FOR_NORMAL 
 FROM(SELECT COUNT(*) AS TOTAL_ACCIDENTS_NORMALDAYS, COUNT(DISTINCT DATE_ACCIDENT) AS TOTAL_DAYS , extract(year from date_accident) as year
      FROM
           ACCIDENT 
        WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'  
        AND (Extract (day from date_accident) <> 25 OR Extract(month from date_accident) <> 12 )
          AND (Extract (day from date_accident) <> 30 OR Extract(month from date_accident) <> 11 )
          AND (Extract (day from date_accident) <> 17 OR Extract(month from date_accident) <> 03 )
          AND (Extract (day from date_accident) <> 31 OR Extract(month from date_accident) <> 12 )
          AND (Extract (day from date_accident) <> 01 OR Extract(month from date_accident) <> 01 )
          AND (Extract (day from date_accident) <> 26 OR Extract(month from date_accident) <> 12 ) 
           Group by Extract(year from date_accident)
           Order by Extract(year from date_accident)
)
) b
WHERE a.years = b.year";

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
  $chart_data .= "{ years:'".$row["YEARS"]."', afh:".$row["AFH"].", afn:".$row["AFN"]."}, ";
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
   <h1 align="center">Accidents Event v/s Normal Days.</h3>   
   <br /><br />
   <div id="chart"></div>
  </div>
  <button id="back" class="btn btn-primary">Back</button>
</center>
 </body>
</html>

<script>
Morris.Line({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],
 xkey:'years',
 ykeys:['afh','afn'],
 labels:['Average Number of Accidents on a Holiday', 'Average Number of Accidents on Normal Day'],
 hideHover:'auto',
 stacked:false
});
</script>
<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "holiday.html";
    };
</script>