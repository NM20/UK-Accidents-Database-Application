<?php

  $username = "PAGUPTA";   	                  
  $password = "ParthGupta";	    	              
  $database = "oracle.cise.ufl.edu/orcl";   

  $sql = "INSERT INTO TEST_DATA VALUES ('NAYAN', 'MONGIA')";
 
  $c = oci_connect($username, $password, $database);
  if (!$c) {
    $m = oci_error();
    trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
  }
  $s = oci_parse($c, $sql);
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