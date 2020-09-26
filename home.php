<?php 

//$year = $_POST['year'];
//$startyear = $_POST['startyear'];
//$endyear = $_POST['endyear'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = "SELECT * FROM (
(SELECT 2005 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2005
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))
UNION
(SELECT 2006 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2006
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))

UNION
(SELECT 2007 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2007
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))


UNION
(SELECT 2008 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2008
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))


UNION
(SELECT 2009 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2009
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))
UNION
(SELECT 2010 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2010
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))
UNION
(SELECT 2011 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2011
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))
UNION
(SELECT 2012 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2012
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))

UNION
(SELECT 2013 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2013
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))

UNION
(SELECT 2014 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2014
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))

UNION
(SELECT 2015 as YEAR, STANDARD_DEVIATION, AVERAGE, MEDIAN
FROM (
SELECT ROUND(STDDEV(COUNT(ACCIDENT_INDEX)),2) AS STANDARD_DEVIATION , ROUND(AVG(COUNT(ACCIDENT_INDEX)),2) AS AVERAGE , MEDIAN(COUNT(ACCIDENT_INDEX)) AS MEDIAN
FROM ACCIDENT
WHERE EXTRACT (YEAR FROM DATE_ACCIDENT) = 2015
GROUP BY EXTRACT(MONTH FROM DATE_ACCIDENT)))
)
WHERE YEAR BETWEEN 2005 AND 2015";

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
  $chart_data .= "{ years:'".$row["YEAR"]."', sd:".$row["STANDARD_DEVIATION"].", avg:".$row["AVERAGE"].", mdn:".$row["MEDIAN"]."}, ";
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
        <link rel="stylesheet" href="style.css"/>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
        crossorigin="anonymous"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <title>HOME</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 temp1">
                    <h1>UK ACCIDENTS DATABASE APPLICATION</h1>
                </div>
            </div>
            <div class="row">
                <div class="col temp2"><a href="home.php">HOME</a></div>
                <div class="col temp2"><a href="accident.html">ACCIDENTS</a></div>
                <div class="col temp2"><a href="age.html">AGE TREND</a></div>
                <div class="col temp2"><a href="time.html">TIME TREND</a></div>
                <div class="col temp2"><a href="holiday.html">DAY TREND</a></div>
                <div class="col temp2"><a href="vehicle.html">VEHICLES</a></div>
                <div class="col temp2"><a href="ac.html">ACC COUNT</a></div>
                <div class="col temp2"><a href="tuple.php">TUPLE COUNT</a></div>
            </div>
            <br><br><br><br><br>
            <div id="chart"></div>
        </div>
        
    </body>
</html>
<script>
Morris.Bar({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],
 xkey:'years',
 ykeys:['sd','avg','mdn'],
 labels:['STANDARD DEVIATION', 'MEAN', 'MEDIAN'],
 hideHover:'auto',
 stacked:false
});
</script>