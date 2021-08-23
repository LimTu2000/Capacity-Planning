<?php
require_once 'functions.php';
require_once 'dbConnect.php';
/*Diese Datei sorgt dafür, dass dem Benutzer auch angezeigt wird, was dieser sehen möchte. Der Block If1 wird ausgeführt, wenn
der Nutzer nur auf der Navigationsleiste des Tools Kalender und einen der Unterpunkte auswählt. Die Ansichten werden dann mit den
Standardparametern aktuelles Datum und Mitarbeiter mit der ID=1 generiert.

Der zweite If-Block wird dann ausgeführt, wenn der Benutzer Formulareingaben auf kalender.php tätigt. Diese werden hier überprüft und
dann als Parameter für die jeweilige Ansicht genutzt.
*/

            $currentMonth = date('m');
            $currentYear = date('Y');
            $worker = null;


//If 1
if(isset($_GET['view'])) {

    
    if ($_GET['view'] == 'month'){
        if (empty($_GET['month']) || empty($_GET['yearAndMonth']) || empty($_GET['worker'])){
            header("location: ./kalender.php?view=month&month=".$currentMonth."&year=".$currentYear."&worker=".$worker);
            exit();
        }
    }

    if ($_GET['view'] == 'year'){
        if (empty($_GET['year']) || empty($_GET['worker'])){
            header("location: ./kalender.php?view=year&year=".$currentYear."&worker=".$worker);
            exit();
        }
    }
}

//If 2
if(empty($_GET['view'])){
    if(!empty($_GET['submityearAndMonth']) && isset($_GET['submityearAndMonth'])){
        
        if(empty($_GET['yearAndMonth']) || empty($_GET['worker'])){
            header("location: ./kalender.php?view=month&month=".$currentMonth."&year=".$currentYear."&worker=".$worker."&error=emptyInput");
            exit();
        }

        $yearAndMonth = $_GET['yearAndMonth']; //Das Ergebnis der Benutzerauswahl im Format YYYY-MM wird gesplittet im Array gespeichert $yearAndMonth[]=[0]=>YYYY [1]=>MM
        $yearAndMonth = explode('-',$yearAndMonth);
        
        $year = $yearAndMonth[0];
        $month = $yearAndMonth[1];
        $worker = $_GET['worker'];
        $workerID = implode(', ', $worker);

        header("location: ./kalender.php?view=month&month=".$month."&year=".$year."&worker=".$workerID);
        exit();
    }

    if(!empty($_GET['submityear']) && isset($_GET['submityear'])){
        if(empty($_GET['year']) || empty($_GET['worker'])){
            header("location: ./kalender.php?view=year&year=".$currentYear."&worker=".$worker."&error=emptyInput");
            exit();
        }

        $year = $_GET['year'];
        $worker = $_GET['worker'];

        header("location: ./kalender.php?view=year&year=".$year."&worker=".$worker);
        exit();
    }
}
?>