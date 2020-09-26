<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$startyear = $_POST['startyear'];
$endyear = $_POST['endyear'];

$username = "PAGUPTA";                  // Use your username

$password = "ParthGupta";             // and your password

$database = "oracle.cise.ufl.edu/orcl";   // and the connect string to connect to your database

$query = "
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


?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <div class = "temp" style = "text-align:center">
            <?php    
echo "<table border='1'>\n";

$ncols = oci_num_fields($s);

echo "<tr>\n";

for ($i = 1; $i <= $ncols; ++$i) {

    $colname = oci_field_name($s, $i);

    echo "  <th><b>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</b></th>\n";

}

echo "</tr>\n";



while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {

    echo "<tr>\n";

    foreach ($row as $item) {

        echo "<td>";

        echo $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";

        echo "</td>\n";

    }

    echo "</tr>\n";

}

echo "</table>\n";
            ?>
            
        </div>
    </body>
</html>