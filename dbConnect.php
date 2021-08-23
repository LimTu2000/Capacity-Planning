<?php
$db_server ='localhost';
$db_user='root';
$db_pw ='';
$db_name='kapazitätsplanungstool';


$connect = new mysqli($db_server,$db_user,$db_pw,$db_name);
if($connect -> connect_error){
    echo 'Fehler';
    die;
}

?>