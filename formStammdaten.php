<?php
require_once 'functions.php';
require_once 'dbConnect.php';

if(isset($_POST['delete'])){
    $workerID = $_POST['delete'];
    print_r($workerID);
    deleteWorker($connect, $workerID);

    header("location: ./stammdaten.php?");
    exit();
}

if (isset($_POST['save'])){
    /*Wenn der Benutzer den "Speichern"-Button drückt, wird jede Tabellenspalte in
    einem Array gespeichert. Die Arrayelemente werden dann in einer For-Schleife als
    Parameter an die Funktion UpdateDatabase gesendet. Auch, wenn nichts an den Werten
    geändert wurde.*/


    foreach ($_POST['workerID'] as $workerID){
        $workerIDs [] = $workerID;
    }
    foreach ($_POST['workerName'] as $workerName){
        $workerNames [] = $workerName;
    }
    foreach ($_POST['workTime'] as $workTime){
        $arrayWorkTime [] = $workTime;
    }
    foreach ($_POST['basicScheduledCapacity'] as $basicScheduledCapacity){
        $arrayBasicScheduledCapacity [] = $basicScheduledCapacity;
    }
    
    for ($i=0;$i<=count($workerIDs)-1;$i=$i+1){

        if (emptyInputWorker($workerNames[$i], $arrayWorkTime[$i], $arrayBasicScheduledCapacity[$i]) !== false){
			header("location: ./stammdaten.php?error=emptyInput");
            exit();
        }

        UpdateMasterDatabase($connect, $workerIDs[$i], $workerNames[$i], $arrayWorkTime[$i], $arrayBasicScheduledCapacity[$i]);
    }
    

    if (!empty($_POST['newWorkerName'])){
        /*Wenn der Benutzer eine neue Aufgabe anlegen möchte, erkennt das Programm dies bislang durch das Prüfen des Eingabefeld für den Aufgabentitel,
         daraufhin wird geprüft ob die anderen Eingabefelder auch einen Wert enthalten.
         Falls ein Wert fehlt, wird der Nutzer auf die Seite aufgaben.php geschickt und bekommt
         über $_GET['error'] angezeigt, dass er alle Felder ausfüllen muss.*/

        $newWorkerName = $_POST['newWorkerName'];
        $newWorkTime = $_POST['newWorkTime'];
        $newBasicScheduledCapacity = $_POST['newBasicScheduledCapacity'];

        if (emptyInputWorker($newWorkerName, $newWorkTime, $newBasicScheduledCapacity) !== false){
            header("location: ./stammdaten.php?error=emptyInput");
            exit();
        }

        InsertMasterDatabase($connect, $newWorkerName, $newWorkTime, $newBasicScheduledCapacity);
        
    }
	
    header("location: ./stammdaten.php?");
    exit();
    
}