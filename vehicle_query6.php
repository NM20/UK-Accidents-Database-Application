<?php 

//$year = $_POST['year'];
$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];
$speedlimit = $_POST['speedlimit'];
$accidentseverity = $_POST['accidentseverity'];
$ptofimpact = $_POST['ptofimpact'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = 
"SELECT years AS YEARS, ROUND((MOTORCYCLE/TOTAL*100),2) AS MOTORCYCLE , ROUND((CAR/TOTAL)*100,2) AS CAR  , ROUND((PEDAL_CYCLE/TOTAL)*100,2) AS PEDALCYCLE  ,   ROUND((BUS/TOTAL)*100,2) AS BUS
FROM 


(

SELECT extract(year from date_accident) as years,count(distinct a.accident_index) as  MOTORCYCLE 
FROM ACCIDENT A, AKHATRI.VEHICLE V , ACCIDENT_SEVERITY ACS , LOCATION L , POINT_OF_IMPACT P 
WHERE A.ACCIDENT_INDEX = V.ACCIDENT_INDEX
AND A.ACCIDENT_SEVERITY = ACS.CODE
AND L.LOCATION_ID = A.LOCATION_ID
AND V.POINT_OF_IMPACT = P.CODE
AND SPEED_LIMIT = '$speedlimit'
AND P.LABEL = '$ptofimpact'
AND ACS.LABEL = '$accidentseverity'
and extract(year from date_accident) between '$startyear' and '$endyear'
AND VEHICLE_TYPE BETWEEN 2 AND 5
group by extract(year from date_accident)
)

A,
(

SELECT extract(year from date_accident) as year,count(distinct a.accident_index) as  CAR FROM ACCIDENT A, AKHATRI.VEHICLE V , ACCIDENT_SEVERITY ACS , LOCATION L , POINT_OF_IMPACT P 
WHERE A.ACCIDENT_INDEX = V.ACCIDENT_INDEX
AND A.ACCIDENT_SEVERITY = ACS.CODE
AND L.LOCATION_ID = A.LOCATION_ID
AND V.POINT_OF_IMPACT = P.CODE
AND SPEED_LIMIT = '$speedlimit'
AND P.LABEL = '$ptofimpact'
AND ACS.LABEL = '$accidentseverity'
and extract(year from date_accident) between '$startyear' and '$endyear'
AND VEHICLE_TYPE BETWEEN 8 AND 9
group by extract(year from date_accident)
 )B,


( 
SELECT extract(year from date_accident) as year,count(distinct a.accident_index) as  PEDAL_CYCLE FROM ACCIDENT A, AKHATRI.VEHICLE V , ACCIDENT_SEVERITY ACS , LOCATION L , POINT_OF_IMPACT P 
WHERE A.ACCIDENT_INDEX = V.ACCIDENT_INDEX
AND A.ACCIDENT_SEVERITY = ACS.CODE
AND L.LOCATION_ID = A.LOCATION_ID
AND V.POINT_OF_IMPACT = P.CODE
AND SPEED_LIMIT = '$speedlimit'
AND P.LABEL = '$ptofimpact'
AND ACS.LABEL = '$accidentseverity'
and extract(year from date_accident) between '$startyear' and '$endyear'
AND VEHICLE_TYPE = 1
group by extract(year from date_accident)


)C,
( 
SELECT extract(year from date_accident) as year,count(distinct a.accident_index) as  BUS FROM ACCIDENT A, AKHATRI.VEHICLE V , ACCIDENT_SEVERITY ACS , LOCATION L , POINT_OF_IMPACT P 
WHERE A.ACCIDENT_INDEX = V.ACCIDENT_INDEX
AND A.ACCIDENT_SEVERITY = ACS.CODE
AND L.LOCATION_ID = A.LOCATION_ID
AND V.POINT_OF_IMPACT = P.CODE
AND SPEED_LIMIT = '$speedlimit'
AND P.LABEL = '$ptofimpact'
AND ACS.LABEL = '$accidentseverity'
and extract(year from date_accident) between '$startyear' and '$endyear'
AND VEHICLE_TYPE BETWEEN 10 AND 11
group by extract(year from date_accident)

)D,
(

SELECT extract(year from date_accident) as year,count(distinct a.accident_index) as  TOTAL FROM ACCIDENT A, AKHATRI.VEHICLE V , ACCIDENT_SEVERITY ACS , LOCATION L , POINT_OF_IMPACT P 
WHERE A.ACCIDENT_INDEX = V.ACCIDENT_INDEX
AND A.ACCIDENT_SEVERITY = ACS.CODE
AND L.LOCATION_ID = A.LOCATION_ID
AND V.POINT_OF_IMPACT = P.CODE
AND SPEED_LIMIT = '$speedlimit'
AND P.LABEL = '$ptofimpact'
AND ACS.LABEL = '$accidentseverity'
and extract(year from date_accident) between '$startyear' and '$endyear'
AND (VEHICLE_TYPE BETWEEN 1 AND 5 OR VEHICLE_TYPE BETWEEN 8 AND 11)
group by extract(year from date_accident)
) E
WHERE A.YEARS= B.YEAR
AND B.YEAR = C.YEAR
AND C.YEAR = D.YEAR
AND D.YEAR = E.YEAR
ORDER BY YEARS";

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
  $chart_data .= "{ years:'".$row["YEARS"]."', mc:".$row["MOTORCYCLE"].", c:".$row["CAR"].", pc:".$row["PEDALCYCLE"]." , bus:".$row["BUS"]."}, ";
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
   <h1 align="center">ACCIDENTS OBSERVED BY DIFFERENT TYPE OF VEHICLES.</h3>   
   <br /><br />
   <div id="chart"></div>
  </div>
  <button id="back" class="btn btn-primary">Back</button>
</center>
 </body>
</html>

<script>
Morris.Bar({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],
 xkey:'years',
 ykeys:['mc','c','pc','bus'],
 labels:['MOTORCYCLE', 'CAR', 'PEDALCYCLE', 'BUS'],
 hideHover:'auto',
 stacked:false
});
</script>
<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "vehicle.html";
    };
</script>