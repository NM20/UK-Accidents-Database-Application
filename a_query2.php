<?php 

$startYear = $_POST['startYear'];
$endYear = $_POST['endYear'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database
$printYears = '';

for ($i = $startYear; $i < $endYear; $i++) {
    $printYears = $printYears + 'Y' + strval($i) + ',';
    echo "$i";
}

$printYears = $printYears + 'Y' + strval($i);

echo $printYears;


$query = 
"SELECT $printYears FROM 
(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2005  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2005
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2006  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2006
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2007  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2007
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2008  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2008
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2009  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2009
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2010  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2010
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2011  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2011
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2012  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2012
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)

NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2013  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2013
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2014  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2014
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
NATURAL JOIN

(SELECT  LABEL, COUNT( extract(year from DATE_ACCIDENT)) AS Y2015  FROM LOCATION L, ACCIDENT A, ROAD_TYPE RT
WHERE extract(year from DATE_ACCIDENT) = 2015
AND L.LOCATION_ID = A.LOCATION_ID
AND L.ROAD_TYPE= RT.CODE
Group by LABEL,extract(year from DATE_ACCIDENT)
ORDER BY LABEL)
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
  $chart_data .= "{ years:'".$row["YEARS"]."', rate:".$row["RATE"]."}, ";
}
//To remove last comma from $chart_data
$chart_data = substr($chart_data, 0, -2);
echo ".$chart_data";
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
   <h1 align="center">Collision Data Through the years</h3>   
   <br /><br />
   <div id="chart"></div>
  </div>
</center>
 </body>
</html>

<script>
Morris.Bar({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],
 xkey:'years',
 ykeys:['rate'],
 labels:['Percentage Rate'],
 hideHover:'auto',
 stacked:false
});
</script>