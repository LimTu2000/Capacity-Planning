<?php
include_once 'Header.php';
require_once 'functions.php';
require_once 'dbConnect.php';
?>

<div class="inhalt">


<?php

/*Die folgenden Zeilen definieren welche Parameter der Funktion createTableTask übergeben werden 
-wenn die Seite geladen ist und noch keine Usereingaben gemacht wurden
-wenn Usereingaben gemacht wurden.
Die Usereingaben werden in der Datei formAufgaben.php verarbeitet.
*/
if(!isset($_GET['submitDateAndID'])){
    
    $workerID = 'null';
    $currentDay = date('d');
    $currentMonth = date('m');
    $currentYear = date('Y');
    $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;
    $date = $currentDate;

    /*
    Prüfen, ob die Eingaben gültig sind. Da die Eingaben in der Adresszeile sichtbar sind, können sie
    auch dort verändert werden und müssen vor dem ausführen der Funktion überprüft werden.
    */

    $existUser = getMasterData($connect, false, $workerID);

    if($existUser==false && $workerID != null && $workerID != 'null'){
        http_response_code(404);
        include('my_404.php');
        die();
    }

    createTableTasks($connect, $workerID, $date);

}else{

     /*
    Prüfen, ob die Eingaben gültig oder überhaupt vorhanden sind. Da die Eingaben in der Adresszeile sichtbar sind, können sie
    auch dort verändert werden und müssen vor dem ausführen der Funktion überprüft werden.
    */

    if(!isset($_GET['worker']) || !isset($_GET['date'])){
        http_response_code(404);
        include('my_404.php');
        die();
    }

    $workerID = $_GET['worker'];
    $date = $_GET['date'];
    
   
    if ($date !== 'null'){
        $splittedDate = explode('-',$date);
        $checkdate = checkdate($splittedDate[1], $splittedDate[2], $splittedDate[0]);

        $year = (int) $splittedDate[0];
        $month = (int) $splittedDate[1];
        $day = (int) $splittedDate[2];

        if($checkdate == false || $year < 1900 || $year > 2999){
            http_response_code(404);
            include('my_404.php');
            die();
        }
    }
    $existUser = getMasterData($connect, false, $workerID);

    if($existUser==false && $workerID != null && $workerID != 'null'){
        http_response_code(404);
        include('my_404.php');
        die();
    }

    createTableTasks($connect, $workerID, $date);
}

?>

<form action="formAufgaben.php" method="get">
<Select class="select" name="worker"><?php if (isset($_GET['worker'])){$workerID = $_GET['worker']; echo getListOfWorker($connect, 'true', $workerID,);}else{ echo getListOfWorker($connect,'true'); }?></Select>
<input class="inputDate" type="date" name="date" value=<?php if(isset($_GET['date'])){echo $_GET['date'];}else{echo $currentDate;}?>>
<input class="button" type="submit" name="submitDateAndID" value="Anzeigen">

</form>

<?php
/*Um den Benutzer eine Information anzuzeigen, wenn dieser Eingabefelder leer lässt, werden error codes in formAufgaben.php
definiert und hier dazu genutzt eine entsprechende Information auszugeben.
*/
if(isset($_GET['error'])){
    
    switch ($_GET['error']) {
        
        case 'emptyInput':
            echo '<p class= "alert">Bitte fülle mindestens eines der beiden Eingabefelder aus.</p>';
            break;
        
        case 'emptyInputForm':
            echo '<p class= "alert">Bitte fülle alle Eingabefelder aus.</p>';
            break;
        
        case 'invalidDate':
            echo '<p class= "alert">Das Datum muss in der Zukunft und an einem Wochentag liegen.</p>';
            break;
        
        case 'none':
            echo '<p class= "affirm">Aufgaben erfolgreich gespeichert.</p>';
            break;
    }
}

if(isset($_GET['delete'])){
    if($_GET['delete']=='successful'){
        echo '<p class= "affirm">Aufgabe gelöscht.</p>';
    } 
}
?>
</div>

<?php
include_once 'footer.php';
?>