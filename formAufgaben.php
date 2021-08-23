<?php
require_once 'functions.php';
require_once 'dbConnect.php';


if (isset($_POST['save'])){
   /*Wenn der Benutzer den "Speichern"-Button drückt, entscheidet der Wert des Buttons, ob eine neue
   Aufgabe angelegt wird oder eine bestehende Aufgabe bearbeitet wird. Der Wert des Buttons in der Zeile für eine neue
   Aufgabe ist 'newTask' die anderen Buttons haben als Wert die ID der Aufgabe. Festgelegt wird der Wert in der Funktion
   getTasks()*/

    $taskID = $_POST['save'];
    
    if ($taskID == 'newTask'){
         /*
         Wenn der Benutzer eine neue Aufgabe anlegen möchte,
         wird geprüft ob die Eingabefelder auch einen Wert enthalten.
         Falls ein Wert fehlt, wird der Nutzer auf die Seite aufgaben.php geschickt und bekommt
         über $_GET['error'] angezeigt, dass er alle Felder ausfüllen muss.
         Es wird auch geprüft, ob der Endtermin in der Vergangenheit liegt.
         Falls alle Felder ausgefüllt sind und der Endtermin nicht in der Vergangenheit liegt, wird die Aufgabe von der Funktion InsertDatabase() angelegt und von der Funktion assignTaskToDate() auf Tage aufgeteilt.
         */

         $newTaskTitle = $_POST['newTaskTitle'];
         $newTaskCapacity = $_POST['newTaskCapacity'];
         $newTaskDeadline = $_POST['newTaskDeadline'];
         $newTaskWorkerID = $_POST['newTaskWorker'];
 
         if (emptyInputTasks($newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID) !== false){
             header("location: ./aufgaben.php?error=emptyInputForm");
             exit();
         }

         if (isDateInvalid($newTaskDeadline) !== false){
            header("location: ./aufgaben.php?error=invalidDate");
             exit();
         }
         
         InsertDatabase($connect, $newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID);
 
         assignTaskToDate($connect, $newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID);
         
         header("location: ./aufgaben.php?worker=".$newTaskWorkerID."&date=".$newTaskDeadline."&submitDateAndID=Anzeigen&error=none");
         exit();
         
    }else{
        $taskTitle = $_POST["AufgabenTitel".$taskID];
        $taskCapacity = $_POST["AufgabenKapazität".$taskID]; 
        $taskDeadline = $_POST["AufgabenEndtermin".$taskID]; 
        $workerID = $_POST["MitarbeiterID".$taskID];

        if (emptyInputTasks($taskTitle, $taskCapacity, $taskDeadline, $workerID) !== false){
            header("location: ./aufgaben.php?error=emptyInputForm");
            exit();
        }

        editTasks($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID);
        header("location: ./aufgaben.php?worker=".$workerID."&date=".$taskDeadline."&submitDateAndID=Anzeigen&error=none");
        
    }
    exit();
}



if (isset($_GET['submitDateAndID'])){

    /*Wenn der Submit Knopf für das Datum und die Mitarbeiter ID gedrückt wird, wird in den folgenden
    Zeilen geprüft, ob ein Wert für das Datum und dem Mitarbeiter gewählt wurde, falls nicht
    wird der Funktion createTable der Wert null für einen der leeren Parameter übergeben.
    Es können nicht beide Eingabefelder leer sein. */
    
    if (empty($_GET['date']) && $_GET['worker'] == 'null'){
        header("location: ./aufgaben.php?error=emptyInput");
        exit();
    }

    if(!isset($_GET['worker'])){
        $workerID = null;
    }else{
        $workerID = $_GET['worker'];
    }

    if(!isset($_GET['date'])){
        $date = 'null';
    }else{
        $date = $_GET['date'];
    }
    if(isset($_GET['date'])==empty($_GET['date'])){
        $date = 'null';
    }

    header("location: ./aufgaben.php?worker=".$workerID."&date=".$date."&submitDateAndID=Anzeigen");
    exit();
}

if(isset($_POST['delete'])){

    $taskID = $_POST['delete'];
    $date = $_POST["AufgabenEndtermin".$taskID]; 
    $workerID = $_POST["MitarbeiterID".$taskID];

    deleteSubtasks($connect, $taskID);
    deleteTask($connect, $taskID);

    header("location: ./aufgaben.php?worker=".$workerID."&date=".$date."&submitDateAndID=Anzeigen&delete=successful");
}