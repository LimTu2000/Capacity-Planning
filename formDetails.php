<?php

require_once 'functions.php';
require_once 'dbConnect.php';

if (isset($_POST['save'])){


    $newTaskTitle = $_POST['newTaskTitle'];
    $newTaskCapacity = $_POST['newTaskCapacity'];
    $newTaskDeadline = $_POST['newTaskDeadline'];
    $newTaskWorkerID = $_POST['newTaskWorker'];
    
    if (emptyInputTasks($newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID) !== false){
        
        header("location: ./details.php?worker=".$_POST['hiddenWorker']."&date=".$_POST['hiddenDate']."&error=emptyInput");
        exit();
    }

    if (isDateInvalid($newTaskDeadline) !== false){
        header("location: ./details.php?error=invalidDate");
         exit();
     }

    InsertDatabase($connect, $newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID);

    assignTaskToDate($connect, $newTaskTitle, $newTaskCapacity, $newTaskDeadline, $newTaskWorkerID);
    
    header("location: ./aufgaben.php?worker=".$newTaskWorkerID."&date=".$newTaskDeadline."&submitDateAndID=Anzeigen&save=successful");
    exit();





}





?>