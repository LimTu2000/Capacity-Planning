<?php
include_once 'Header.php';
require_once 'functions.php';
require_once 'dbConnect.php';
?>

<div class="inhalt">

<div class ="legende">

<table>
<thead>
    <tr><th colspan="2">Legende</th></tr>
</thead>
<tbody>

<tr>
<td>
    <svg width="120" height="120" viewBox="0 0 120 120">
        <defs>
            <linearGradient id="greenfill" x1=0% y1=0% x2=0% y2=100%>
                <stop offset="0%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="60%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="60%" stop-color="#00cc00"></stop>
                <stop offset="100%" stop-color="#00cc00"></stop>
            </linearGradient>
        </defs>
                                
        <rect class= "greenrect" x="10" y="10" width="100" height="100" stroke="black"/>
    </svg>
</td>
<td> &lt; 50%</td></tr>

<tr>
<td>
    <svg width="120" height="120" viewBox="0 0 120 120">
        <defs>
            <linearGradient id="yellowfill" x1=0% y1=0% x2=0% y2=100%>
                <stop offset="0%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="30%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="30%" stop-color="#ffff00"></stop>
                <stop offset="100%" stop-color="#ffff00"></stop>
            </linearGradient>
        </defs>
        <rect class= "yellowrect" x="10" y="10" width="100" height="100" stroke="black"/>
    </svg>
</td><td>50% - 89%</td></tr>

<tr>
<td>
    <svg width="120" height="120" viewBox="0 0 120 120">
        <defs>
            <linearGradient id="redfill" x1=0% y1=0% x2=0% y2=100%>
                <stop offset="0%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="10%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="10%" stop-color="#ff0000"></stop>
                <stop offset="100%" stop-color="#ff0000"></stop>
            </linearGradient>
        </defs>

        <rect class= "redrect" x="10" y="10" width="100" height="100" stroke="black"/>
    </svg>
</td><td>90% - 100%</td></tr>

<tr>
<td>
    <svg width="120" height="120" viewBox="0 0 120 120">
        <defs>
            <linearGradient id="magentafill" x1=0% y1=0% x2=0% y2=100%>
                <stop offset="0%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="0%" stop-color="#ffffff" stop-opacity="1"></stop>
                <stop offset="0%" stop-color="#B400B4"></stop>
                <stop offset="100%" stop-color="#B400B4"></stop>
            </linearGradient>
        </defs>

        <rect class= "magentarect" x="10" y="10" width="100" height="100" stroke="black"/>
    </svg>
</td><td>> 100%</td></tr>



</tbody>
</table>
</div>

<?php
//Variable view in der Adresszeile gelöscht
if(!isset($_GET['view'])){
    http_response_code(404);
    include('my_404.php');
    die();
}

//Variable view in der Adresszeile geändert und ist weder year noch month
if($_GET['view'] != 'month' && $_GET['view'] != 'year'){
    http_response_code(404);
    include('my_404.php');
    die();
}


//Monatsansicht generieren
if ($_GET['view'] == 'month'){
    
    //Variable worker, year oder month in der Adresszeile gelöscht
    if(!isset($_GET['worker']) || !isset($_GET['year']) || !isset($_GET['month'])){
        http_response_code(404);
        include('my_404.php');
        die();
    }

    $worker = $_GET['worker'];
    $month = (int) $_GET['month'];
    $year = (int) $_GET['year'];

    /*
    Prüfen, ob die Eingaben gültig sind. Da die Eingaben in der Adresszeile sichtbar sind, können sie
    auch dort verändert werden und müssen vor dem ausführen der Funktion überprüft werden.
    */
    
    $existUser = getMasterData($connect, false, $worker);
    $checkdate = checkdate($month, 01, $year);
    
    //Variable worker, month oder year in der Adresszeile zu einem ungültigen Wert geändert
    if($existUser==false && $worker != null || $month > 12 || $month < 1 || $year > 2999 || $year < 1900 ||$checkdate==false){
        http_response_code(404);
        include('my_404.php');
        die();
    }
    

    if($worker == null){
        
        createMonthCalendar($connect, $month, $year);
    
    }else{
    
        $workerID = explode(',', $worker);
        createMonthCalendar($connect, $month, $year, $workerID);
    }
}

//Jahresansicht generieren
if ($_GET['view'] == 'year'){
    //Variable worker oder year in der Adresszeile gelöscht
    if(!isset($_GET['worker']) || !isset($_GET['year'])){
        http_response_code(404);
        include('my_404.php');
        die();
    }
    
    
    $worker = $_GET['worker'];
    $year = (int) $_GET['year'];
    $currentYear = date('Y'); 
    
    /*
    Prüfen, ob die Eingaben gültig sind. Da die Eingaben in der Adresszeile sichtbar sind, können sie
    auch dort verändert werden und müssen vor dem ausführen der Funktion überprüft werden.
    */
    
    $min= $currentYear - 2;
    $max= $currentYear + 2;
    $existUser = getMasterData($connect, false, $worker);
    $checkdate = checkdate(01, 01, $year);

    //Variable worker, month oder year in der Adresszeile zu einem ungültigen Wert geändert
    if($existUser==false && $worker != null || $year < $min || $year > $max || $checkdate==false){
        http_response_code(404);
        include('my_404.php');
        die();
    }
?>
    



<?php
    if($worker == null){
        createYearCalendar($connect, $year);
    }else{
        createYearCalendar($connect, $year, $worker);
    }
}
?>






<form action="formKalender.php" method="get">

<?php 
/*
Auswahlmenü für den Mitarbeiter, die Jahresansicht kann nur einen Mitarbeiter anzeigen, die Monatsansicht mehrere.
Deshalb wird für die Montsansicht eine Mehrfachauswahl generiert
*/

$selectOptions = getListOfWorker($connect, 'false', $worker);


if ($_GET['view'] == 'month'){
    echo '<Select class="multiselect" name= "worker[]" multiple>'.$selectOptions.'</Select>';

}elseif ($_GET['view'] == 'year'){
    echo '<Select class="select" name= "worker">'.$selectOptions.'</Select>';
}
?>


<!--Je nach Kalenderansicht (Jahr oder Monat) wird entweder ein Eingabefeld vom Typ Number oder vom Typ Month angezeigt-->
<!-- Gültige Eingaben für das Eingabefeld vom Typ Number ist das aktuelle Jahr +/- 2 Jahre.-->
<?php 


if ($_GET['view'] == 'month'){ 
    $month = sprintf("%02d", $month);
    echo '<input class="inputMonth" type="month" name="yearAndMonth" value='.$year.'-'.$month.'>';
}elseif ($_GET['view'] == 'year'){
    $currentYear = date('Y'); 
    $min= $currentYear - 2;
    $max= $currentYear + 2;
    echo '<input type="number" name="year" min="'.$min.'"max="'.$max.'" step="1" value="'.$year.'">';
}
?>


<!--Um zu erkennen, ob der Benutzer sich ein Jahr oder Monat anzeigen lassen möchte, werden je nach Ansicht verschieden benannte Buttons generiert-->
<!--Die Datei formKalender leitet den Benutzer dann mit der Funktion header zurück auf kalender.php mit den Nutzereingaben als GET Variabeln-->
<?php 
if ($_GET['view'] == 'month'){
    echo '<input class="button" type="submit" name="submityearAndMonth" value="Anzeigen">'; //Monatsansicht
}elseif ($_GET['view'] == 'year'){
    echo '<input class="button" type="submit" name="submityear" value="Anzeigen">'; //Jahresansicht
}?>
</form>






<!--Fehlermeldungen-->
<?php
if(isset($_GET['error'])){
    if($_GET['error']=='emptyInput'){
        echo '<p class= "alert">Bitte fülle beide Eingabefelder aus.</p>';
    } 
}
?>
</div>
<?php
include_once 'footer.php';
?>