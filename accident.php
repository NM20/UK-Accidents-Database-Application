<?php 

//$year = $_POST['year'];
$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];

$username = "PAGUPTA";                   // Use your username
$password = "ParthGupta";                  // and your password
$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = 
"SELECT A.YEAR AS YEARS, ROUND((A.NUMBER_OF_ACCIDENTS_MALE/B.TOTAL_ACCIDENTS)*100, 3) AS RATE
FROM (SELECT COUNT(*) AS NUMBER_OF_ACCIDENTS_MALE, EXTRACT(YEAR FROM DATE_ACCIDENT) AS YEAR
      FROM ACCIDENT JOIN NMONGIA.CASUALTY 
                    ON ACCIDENT.ACCIDENT_INDEX = NMONGIA.CASUALTY.ACCIDENT_INDEX
      WHERE AGE_BAND_OF_CASUALTY = 3
      AND SEX_OF_CASUALTY = 1
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
  $chart_data .= "{ years:'".$row["YEARS"]."', rate:".$row["RATE"]."}, ";
}
//To remove last comma from $chart_data
$chart_data = substr($chart_data, 0, -2);

?>
<html>
<head>
	<title>ACCIDENTS</title>
	<link rel="stylesheet" href="style.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
    crossorigin="anonymous"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container-fluid">
        <div class="row">
            <div class="col-12 temp1">
                <h1>UK ACCIDENTS DATABASE APPLICATION</h1>
            </div>
        </div>
        <div class="row">
            <div class="col temp2"><a href="home.html">HOME</a></div>
            <div class="col temp2"><a href="accident.html">ACCIDENTS</a></div>
            <div class="col temp2"><a href="home.html">VEHICLES</a></div>
            <div class="col temp2"><a href="#ex4">CASUALTIES</a></div>
            <div class="col temp2"><a href="#ex5">CONDITIONS</a></div>
            <div class="col temp2"><a href="#ex6">REGION</a></div>
            <div class="col temp2"><a href="#ex7">DRIVER</a></div>
            <div class="col temp2"><a href="#ex7">TUPLE COUNT</a></div>
        </div>    
    </div>
	<div class = "main">ACCIDENTS</div>
	<form action = "accident.php" method="post">
		<select name = "startyear" class = "demo">
			<option>SELECT START YEAR</option>
			<option value = "2005">2005</option>
			<option value = "2006">2006</option>
			<option value = "2007">2007</option>
			<option value = "2008">2008</option>
			<option value = "2009">2009</option>
			<option value = "2010">2010</option>
			<option value = "2011">2011</option>
			<option value = "2012">2012</option>
			<option value = "2013">2013</option>
			<option value = "2014">2014</option>
			<option value = "2015">2015</option>
		</select>
		<select name = "endyear" class = "demo">
			<option>SELECT END YEAR</option>
			<option value = "2005">2005</option>
			<option value = "2006">2006</option>
			<option value = "2007">2007</option>
			<option value = "2008">2008</option>
			<option value = "2009">2009</option>
			<option value = "2010">2010</option>
			<option value = "2011">2011</option>
			<option value = "2012">2012</option>
			<option value = "2013">2013</option>
			<option value = "2014">2014</option>
			<option value = "2015">2015</option>
		</select>
		<div class = "demo1">
			 <button id="myButton" class="btn btn-primary">SUBMIT</button>
		</div>
	</form>
	<div id="chart"></div>
</body>
</html>

<script type="text/javascript">
    document.getElementById("myButton").onclick = function () {
        location.href = "query1.php";
    };
</script>
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