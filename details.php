<?php
include_once 'Header.php';
require_once 'functions.php';
require_once 'dbConnect.php';
?>

<?php
if(isset($_GET['date']) && isset($_GET['worker'])){

    $date = $_GET['date'];
    $worker = $_GET['worker'];

    $workerID = explode(',', $worker);

    foreach($workerID as $user){
        $existUser = getMasterData($connect, false, $user);

        if($existUser==false){
            http_response_code(404);
            include('my_404.php');
            die();
        }
    }

    $dateArray = explode('-', $date);
    $checkdate = checkdate($dateArray[1], $dateArray[2], $dateArray[0]);

    if($checkdate==false){
        http_response_code(404);
        include('my_404.php');
        die();
    }

    showDetails($connect, $date, $workerID);
}



/*Aktuell soll keine Tabelle generiert werden, wenn weder Datum noch Mitarbeiter ausgewählt wurde,
wenn keiner der beiden Parameter definiert ist schickt die Datei formAufgaben.php den Benutzer 
auf die Datei aufgaben.php und setzt $_GET['error'] auf emptyInput. Dem User wird dann dieser Infotext
angezeigt.
*/
if(isset($_GET['error'])){
    if($_GET['error']=='emptyInput'){
        echo '<p class= "alert">Bitte fülle die Eingabefelder aus.</p>';
    } 
    if($_GET['error']=='invalidDate'){
        echo '<p class= "alert">Das Datum muss in der Zukunft und an einem Wochentag liegen.</p>';
    } 
}

if(isset($_GET['save'])){
    if($_GET['save']=='successful'){
        echo '<p class= "affirm">Aufgaben erfolgreich gespeichert.</p>';
    } 
}
?>

<?php
include_once 'footer.php';
?>