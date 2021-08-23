<?php

$month = date('m');
$year = date('Y');


header("location: ./kalender.php?view=month&month=".$month."&year=".$year."&worker=");
exit();

?>