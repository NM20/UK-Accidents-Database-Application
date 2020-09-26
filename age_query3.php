<?php 

//$year = $_POST['year'];
$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = 
"
SELECT YEARS, ROUND((YOUNG_DRIVER/TOTAL_DRIVER*100),2) AS YDP
, ROUND((MIDDLE_AGED_DRIVER/TOTAL_DRIVER*100),2) AS MADP ,
ROUND((OLD_DRIVER/TOTAL_DRIVER*100),2) AS ODP

FROM 
(
SELECT count(d.driver_id) as young_driver,extract(year from a.date_accident) as years
FROM DRIVER D , ACCIDENT A ,ACCIDENT_SEVERITY ACS, CASUALTY_SEVERITY CSS, NMONGIA.CASUALTY CAS , CONDITIONS C , ROAD_SURFACE_CONDITIONS RSC , WEATHER_CONDITIONS WC, LIGHT_CONDITIONS LC , AKHATRI.VEHICLE V
WHERE D.DRIVER_ID = V.DRIVER_ID
AND V.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND CAS.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND A.CONDITIONS_ID=C.CONDITIONS_ID
AND C.ROAD_SURFACE_CONDITIONS = RSC.CODE
AND C.WEATHER_CONDITIONS= WC.CODE
AND C.LIGHT_CONDITIONS= LC.CODE
AND ACS.CODE=A.ACCIDENT_SEVERITY
AND CSS.CODE = CAS.CASUALTY_SEVERITY
AND ACS.LABEL= 'Fatal'
AND CSS.LABEL = 'Fatal'
AND RSC.LABEL = 'DRY'
and WC.LABEL = 'Fine without high winds'
and LC.LABEL = 'Daylight'
and age_band_of_driver between 1 and 5
AND EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
group by extract(year from a.date_accident)
order by extract(year from a.date_accident)

)A,
(

SELECT count(d.driver_id) as middle_aged_driver,extract(year from a.date_accident) as year 
FROM DRIVER D , ACCIDENT A ,ACCIDENT_SEVERITY ACS, CASUALTY_SEVERITY CSS, NMONGIA.CASUALTY CAS , CONDITIONS C , ROAD_SURFACE_CONDITIONS RSC , WEATHER_CONDITIONS WC, LIGHT_CONDITIONS LC , AKHATRI.VEHICLE V
WHERE D.DRIVER_ID = V.DRIVER_ID
AND V.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND CAS.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND A.CONDITIONS_ID=C.CONDITIONS_ID
AND C.ROAD_SURFACE_CONDITIONS = RSC.CODE
AND C.WEATHER_CONDITIONS= WC.CODE
AND C.LIGHT_CONDITIONS= LC.CODE
AND ACS.CODE=A.ACCIDENT_SEVERITY
AND CSS.CODE = CAS.CASUALTY_SEVERITY
AND ACS.LABEL= 'Fatal'
AND CSS.LABEL = 'Fatal'
AND RSC.LABEL = 'DRY'
and WC.LABEL = 'Fine without high winds'
and LC.LABEL = 'Daylight'
and age_band_of_driver between 6 and 8
AND EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
group by extract(year from a.date_accident)
order by extract(year from a.date_accident)



)B,
(
SELECT count(d.driver_id) as old_driver,extract(year from a.date_accident)as year 
FROM DRIVER D , ACCIDENT A ,ACCIDENT_SEVERITY ACS, CASUALTY_SEVERITY CSS, NMONGIA.CASUALTY CAS , CONDITIONS C , ROAD_SURFACE_CONDITIONS RSC , WEATHER_CONDITIONS WC, LIGHT_CONDITIONS LC , AKHATRI.VEHICLE V
WHERE D.DRIVER_ID = V.DRIVER_ID
AND V.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND CAS.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND A.CONDITIONS_ID=C.CONDITIONS_ID
AND C.ROAD_SURFACE_CONDITIONS = RSC.CODE
AND C.WEATHER_CONDITIONS= WC.CODE
AND C.LIGHT_CONDITIONS= LC.CODE
AND ACS.CODE=A.ACCIDENT_SEVERITY
AND CSS.CODE = CAS.CASUALTY_SEVERITY
AND ACS.LABEL= 'Fatal'
AND CSS.LABEL = 'Fatal'
AND RSC.LABEL = 'DRY'
and WC.LABEL = 'Fine without high winds'
and LC.LABEL = 'Daylight'
and age_band_of_driver between 9 and 11
AND EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
group by extract(year from a.date_accident)
order by extract(year from a.date_accident)

)C,

(

SELECT count(d.driver_id) as total_driver,extract(year from a.date_accident) as year FROM DRIVER D , ACCIDENT A ,ACCIDENT_SEVERITY ACS, CASUALTY_SEVERITY CSS, NMONGIA.CASUALTY CAS , CONDITIONS C , ROAD_SURFACE_CONDITIONS RSC , WEATHER_CONDITIONS WC, LIGHT_CONDITIONS LC , AKHATRI.VEHICLE V
WHERE D.DRIVER_ID = V.DRIVER_ID
AND V.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND CAS.ACCIDENT_INDEX=A.ACCIDENT_INDEX
AND A.CONDITIONS_ID=C.CONDITIONS_ID
AND C.ROAD_SURFACE_CONDITIONS = RSC.CODE
AND C.WEATHER_CONDITIONS= WC.CODE
AND C.LIGHT_CONDITIONS= LC.CODE
AND ACS.CODE=A.ACCIDENT_SEVERITY
AND CSS.CODE = CAS.CASUALTY_SEVERITY
AND ACS.LABEL= 'Fatal'
AND CSS.LABEL = 'Fatal'
AND RSC.LABEL = 'DRY'
and WC.LABEL = 'Fine without high winds'
and LC.LABEL = 'Daylight'
AND EXTRACT(YEAR FROM DATE_ACCIDENT) BETWEEN '$startyear' AND '$endyear'
group by extract(year from a.date_accident)
order by extract(year from a.date_accident)
)D
WHERE A.YEARs=B.YEAR
AND C.YEAR=D.YEAR
AND B.YEAR= C.YEAR";

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
  $chart_data .= "{ years:'".$row["YEARS"]."', ydp:".$row["YDP"].", madp:".$row["MADP"].", odp:".$row["ODP"]."}, ";
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
   <h1 align="center">Percentage of people of different age groups involved in accidents between years as selected by users.</h3>   
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
 ykeys:['ydp','madp','odp'],
 labels:['Percentage of Young People','Percentage of Middle-aged People','Percentage of Old People'],
 hideHover:'auto',
 stacked:false
});
</script>
<script type="text/javascript">
    document.getElementById("back").onclick = function () {
        location.href = "age.html";
    };
</script>