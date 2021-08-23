<?php

require_once 'dbConnect.php';

    /*Funktionen hauptsächlich für Datei kalender.php */

    function createMonthCalendar($connect, $monthNumber, $year, array $workerIDs = null){
        /*
        Diese Funktion generiert den Kalender eines bestimmten Monats. Dabei ist der Parameter $workerIDs der die MitarbeiterID oder mehrere IDs enthält optional.
        Beim fehlen des Parameters wird eine einfache Kalenderdarstellung generiert ohne weitere Funktionen für die Visualisierung der Benutzerdaten aufzurufen.
        Wenn Mitarbeiter IDs übergeben wurden, werden die Namen der Mitarbeiter durch die Funktion displayWorkers() im Kopf der Tabelle ausgegeben. 
        Die IDs werden auch dazu benutzt über die Funktion createVisualCapacity() die Auslastung der Mitarbeiter aus der Datenbank abzufragen und als eine rechteckige Darstellung an dem
        jeweiligen Tag auszugeben.
        Weitere Informationen zu der Generierung des Kalenders gibt es in der Entwicklerdokumentation.
        */




        $firstDayOfMonth = date('l', mktime(0, 0, 0, $monthNumber, 1, $year));
        $monthLength = date('t', mktime(0, 0, 0, $monthNumber, 1, $year));
        setlocale(LC_TIME, "de_DE", "deu_deu");
        $monthName = strftime("%B", mktime(0, 0, 0, $monthNumber, 1, $year));

        if ($workerIDs == null){
        
            echo '<table class="calendar">
            <thead>
                <tr>
                    <th colspan="8">'.$monthName.' '.$year.'</th>
                </tr>
                <tr>
                    <th class="cwColumn">KW</th>
                    <th>Montag</th>
                    <th>Dienstag</th>
                    <th>Mittwoch</th>
                    <th>Donnerstag</th>
                    <th>Freitag</th>
                    <th>Samstag</th>
                    <th>Sonntag</th>
                </tr>
            </thead>';

            echo '<tbody>';
        
            for ($i=0; $i<=47; $i=$i+1){
                
                $day = calculateDay($i, $firstDayOfMonth, $monthLength);
                $CW = calculateCalenderWeek($i, $monthNumber, $year);
                $date = $year.'-'.$monthNumber.'-'.$day;

                if ($i == 0 || $i%8 == 0){ //Anfang einer Zeile
                    echo'<tr>';
                }

                if ($i == 0 ||$i%8 == 0){ //Erstes Feld einer Zeile zeigt die Kalenderwoche an, alle anderen den jeweiligen Tag
                    echo '<td class="cwColumn" id='.$i.'>'.$CW.'</td>'; //KW anzeigen
                }else{
                    if ($i == 6 ||$i == 7 ||$i == 14 ||$i == 15 ||$i == 22 ||$i == 23 ||$i == 30 ||$i == 31 ||$i == 38 ||$i == 39 ||$i == 46 ||$i == 47){ //Wochenenden
                        if(strtotime($date) == strtotime(date('d'.'-'.'m'.'-'.'Y'))){
                            echo '<td class="today" id='.$i.'>'.$day.'</td>';//Tag anzeigen, wenn es der heutige Tag ist wird er der Klasse today zugeordnet)
                        }else{
                            echo '<td class="weekend" id='.$i.'>'.$day.'</td>';//Wochenende werden der Class weekend zugeordnet
                        }
                    }
                    elseif (strtotime($date) == strtotime(date('d'.'-'.'m'.'-'.'Y'))){
                        echo '<td class="today" id='.$i.'>'.$day.'</td>';//Tag anzeigen, wenn es der heutige Tag ist wird er der Klasse today zugeordnet
                    } else {
                        echo '<td id='.$i.'>'.$day.'</td>';//Tag anzeigen
                    }
                }
                
                if ($i == 7||$i == 15||$i == 23||$i == 31||$i == 39||$i == 47){ //Ende einer Zeile
                    echo '</tr>';
                }

            }
        }else{
            $stringWorkerIDs = implode(', ', $workerIDs);
            $displayWorkers = displayWorkers($connect, $workerIDs);

            echo '<table class="calendar">
            <thead>
                <tr>
                    <th colspan="2">'.$displayWorkers.'</th>
                    <th colspan="6">'.$monthName.' '.$year.'</th>
                </tr>
                <tr>
                    <th class="cwColumn">KW</th>
                    <th>Montag</th>
                    <th>Dienstag</th>
                    <th>Mittwoch</th>
                    <th>Donnerstag</th>
                    <th>Freitag</th>
                    <th>Samstag</th>
                    <th>Sonntag</th>
                </tr>
            </thead>';

            echo '<tbody>';

            for ($i=0; $i<=47; $i=$i+1){
                
                $day = calculateDay($i, $firstDayOfMonth, $monthLength);
                $CW = calculateCalenderWeek($i, $monthNumber, $year);
                $date = $year.'-'.$monthNumber.'-'.$day;
                $VisualCapacity = createVisualCapacity($connect, $year, $monthNumber, $day, $workerIDs);

                if ($i == 0 || $i%8 == 0){ //Anfang einer Zeile
                    echo'<tr>';
                }

                if ($i == 0 ||$i%8 == 0){ //Erstes Feld einer Zeile zeigt die Kalenderwoche an, alle anderen den jeweiligen Tag
                    echo '<td class="cwColumn" id='.$i.'>'.$CW.'</td>'; //KW anzeigen
                }else{
                    if ($i == 6 ||$i == 7 ||$i == 14 ||$i == 15 ||$i == 22 ||$i == 23 ||$i == 30 ||$i == 31 ||$i == 38 ||$i == 39 ||$i == 46 ||$i == 47){ //Wochenenden
                        if(strtotime($date) == strtotime(date('d'.'-'.'m'.'-'.'Y'))){
                            echo '<td class="today" id='.$i.'><a href="details.php?date="'.$date.'"&worker="'.$stringWorkerIDs.'">'.$day.'</a></td>';//Tag anzeigen, wenn es der heutige Tag ist wird er der Klasse today zugeordnet)
                        }else{
                            echo '<td class="weekend" id='.$i.'>'.$day.'</td>';//Wochenende werden der Class weekend zugeordnet
                        }
                    }
                    elseif (strtotime($date) == strtotime(date('d'.'-'.'m'.'-'.'Y'))){
                        echo '<td class="today" id='.$i.'><a href="details.php?date='.$date.'&worker='.$stringWorkerIDs.'">'.$day.$VisualCapacity.'</a></td>';//Tag anzeigen, wenn es der heutige Tag ist wird er der Klasse today zugeordnet
                    } elseif (!empty($day)) {
                        echo '<td id='.$i.'><a href="details.php?date='.$date.'&worker='.$stringWorkerIDs.'">'.$day.$VisualCapacity.'</a></td>';//Tag anzeigen
                    } elseif (empty($day)){ //Kalenderfelder die nicht zum aktuellen Monat gehören enthalten keinen Link
                        echo '<td id='.$i.'></td>';
                    }
                }
                
                
                if ($i == 7||$i == 15||$i == 23||$i == 31||$i == 39||$i == 47){ //Ende einer Zeile
                    echo '</tr>';
                }
            }
        }

        echo '</tbody></table>';
    }

    function createYearCalendar($connect, $year, $workerID = null){
        /*
        Diese Funktion generiert den Kalender eines bestimmten Jahres. Dabei ist der Parameter $workerID der die MitarbeiterID enthält optional.
        Beim fehlen des Parameters wird eine einfache Kalenderdarstellung generiert ohne weitere Funktionen für die Visualisierung der Benutzerdaten aufzurufen.
        Wenn eine Mitarbeiter ID übergeben wurde, wird der Name des Mitarbeiters durch die Funktion getMasterdata() im Kopf der Tabelle ausgegeben. 
        Die IDs werden auch dazu benutzt über die Funktion createVisualCapacity() die Auslastung der Mitarbeiter aus der Datenbank abzufragen und als eine rechteckige Darstellung an dem
        jeweiligen Tag auszugeben. Am Ende jeder Monatszeile wird eine Bilanz über die freie und Gesamte Kapazität in diesem Monat ausgegeben, die verplante Kapazität wird von der Funktion getScheduledCapacity() errechnet
        und dann von der Gesamtkapazität abgezogen. Die Gesamtkapazität wird über die Funktion getMasterData() errechnet
        
        Weitere Informationen zu der Generierung des Kalenders gibt es in der Entwicklerdokumentation.
        */ 


        if($workerID == null){
            
            echo '<table class="calendar" id="year">
                <thead>
                    <tr>
                        <th colspan= "34">'.$year.'</th>
                    </tr>
                    <tr>
                        <th rowspan = "2" class="cwColumn"></th>';
            
            for ($i=1; $i<=31; $i=$i+1){
                $dayPadded = sprintf("%02d", $i);
                echo '<th rowspan="2">'.$dayPadded.'</th>';
            }

            echo '</thead><tbody>';
            
            for ($i=1; $i<=12; $i=$i+1){
                setlocale(LC_TIME, "de_DE", "deu_deu");
                $monthName = strftime('%b', mktime(0, 0, 0, $i, 1, $year));
                $DaysInMonth = date('t', mktime(0, 0, 0, $i, 1, $year));
                
                
                echo '<tr>';
                echo '<td>'.$monthName.'</td>';

                for ($j=1; $j<=31; $j=$j+1){
                    
                    $DayOfTheWeek = date('l', mktime(0, 0, 0, $i, $j, $year));
                    $jIsDay = true;
                    
                    if($j<=31){

                        if ($j > $DaysInMonth){
                            $jIsDay = false;
                        }elseif($DayOfTheWeek == 'Saturday' || $DayOfTheWeek == 'Sunday'){
                            $jIsDay = false;
                        }

                        
                        
                        if ($jIsDay == false){
                            echo '<td class= "weekend">  </td>';
                        }else{

                            echo '<td></td>';
                        }
                    }

                    
            }
            echo '</tr>';
        }

        }else{
            $workerIDArray[] = $workerID;
            $workerName = getMasterData($connect, false, $workerID)[0];
        
            echo '<table class="calendar" id="year">
                <thead>
                    <tr>
                        <th colspan= "34">'.$workerName.'  '.$year.'</th>
                    </tr>
                    <tr>
                    <th rowspan = "2" class="cwColumn"></th>';
            
            for ($i=1; $i<=31; $i=$i+1){
                $dayPadded = sprintf("%02d", $i);
                echo '<th rowspan="2">'.$dayPadded.'</th>';
            }
            echo    '<th rowspan="1" colspan="2">Kapazität</th>
                </tr>';
            echo '<tr>
                    <td colspan="1">Frei</td>';
            echo    '<td colspan="1">Gesamt</td>
                </tr>';

            echo '</thead><tbody>';
            
            for ($i=1; $i<=12; $i=$i+1){
                unset($basicScheduledCapacity);
                unset($ScheduledCapacity);
                unset($worktime);
                setlocale(LC_TIME, "de_DE", "deu_deu");
                $monthName = strftime('%b', mktime(0, 0, 0, $i, 1, $year));
                $DaysInMonth = date('t', mktime(0, 0, 0, $i, 1, $year));
                
                
                echo '<tr>';
                echo '<td>'.$monthName.'</td>';

                for ($j=1; $j<=32; $j=$j+1){
                    $date = $year.'-'.$i.'-'.$j;
                    $DayOfTheWeek = date('l', mktime(0, 0, 0, $i, $j, $year));
                    $jIsDay = true;

                    $VisualCapacity = createVisualCapacity($connect, $year, $i, $j, $workerIDArray);
                    
                    
                    if($j<=31){

                        if ($j > $DaysInMonth){
                            $jIsDay = false;
                        }elseif($DayOfTheWeek == 'Saturday' || $DayOfTheWeek == 'Sunday'){
                            $jIsDay = false;
                        }

                        
                        
                        if ($jIsDay == false){
                            echo '<td class= "weekend">  </td>';
                        }else{

                            echo '<td><a href="details.php?date='.$date.'&worker='.$workerID.'">'.$VisualCapacity.'</a></td>';

                            $worktime [] = getMasterData($connect, false, $workerID)[1];
                            $ScheduledCapacity [] = getScheduledCapacity($connect, $date, $workerID);
                            $basicScheduledCapacity [] = getMasterData($connect, false, $workerID)[2];
                        }
                }

                    if ($j > 31){
                        $MonthScheduledCapacity = array_sum($ScheduledCapacity) + array_sum($basicScheduledCapacity);
                        $MonthTotalCapacity = array_sum($worktime);
                        $MonthFreeCapacity = $MonthTotalCapacity - $MonthScheduledCapacity;

                        echo '<td class="monthCapacity">'.$MonthFreeCapacity.'</td>';
                        echo '<td class="monthCapacity">'.$MonthTotalCapacity.'</td>';
                    }
                }
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
    }



        function calculateDay($fieldID, $firstDayOfMonth, $monthLength){
            $day;
            if ($fieldID == 0 ||$fieldID%8 == 0){
            return; 
            }
            /*Je nachdem mit welchen Wochentag der Monat beginnt, fängt die Nummerierung der Felder nicht in dem ersten Feld an einem Montag an.
            Anhand der FieldID und dem ersten Tag des Monats wird dann das Datum des Tages errechnet
            Bsp: Wenn der Monat einem Dienstag anfängt, ist die FieldID des ersten Tages die 2 (Tabelle fängt bei 0 an), 
            es verschiebt sich demnach alles um -1
            */

            switch ($firstDayOfMonth) {
                case 'Monday':
                    $day = $fieldID;
                    break;
                case 'Tuesday':
                    $day = $fieldID - 1;
                    break;
                case 'Wednesday':
                    $day = $fieldID - 2;
                    break;
                case 'Thursday':
                    $day = $fieldID - 3;
                    break;
                case 'Friday':
                    $day = $fieldID - 4;
                    break;
                case 'Saturday':
                    $day = $fieldID - 5;
                    break;
                case 'Sunday':
                    $day = $fieldID - 6;
                    break;
            }

            /*Das Feld der Kalenderwoche wird als Tag gezählt, dementsprechend wird jede Woche ein Tag übersprungen, um das 
            auszugleichen wird ab Monatswoche 1 der Tag um eins verringert, ab Monatswoche 2 um zwei usw... */
            if ($fieldID > 8 && $fieldID < 16){
                $day = $day - 1;
            }
            if ($fieldID > 16 && $fieldID < 24){
                $day = $day - 2;
            }
            if ($fieldID > 24 && $fieldID < 32){
                $day = $day - 3;
            }
            if ($fieldID > 32 && $fieldID < 40){
                $day = $day - 4;
            }
            if ($fieldID > 40 && $fieldID < 48){
                $day = $day - 5;
            }
            if ($day >0 && $day <= $monthLength ){
                $dayPadded = sprintf("%02d", $day);
                return $dayPadded;
            }
        }

        function calculateCalenderWeek($fieldID, $monthNumber, $year){
            $CW = '';
            
            //Die folgenden FieldIDs sind jeweils die IDs der KW-Spalte
            switch ($fieldID) {
                case '0':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 1, $year));
                    break;
                case '8':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 8, $year));
                    break;
                case '16':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 15, $year));
                    break;
                case '24':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 22, $year));
                    break;
                case '32':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 29, $year));
                    break;
                case '40':
                    $CW = date('W', mktime(0, 0, 0, $monthNumber, 36, $year));
                    break;
            }

            //Umwandeln einstelliger Kalenderwochen in eine Zahl mit führender Null
            $cwPadded = sprintf("%02d", $CW);
            return $cwPadded;
        }

        function getScheduledCapacity($connect, $date, $workerID){
            /*Die bisher verplante Kapazität an einem bestimmten Tag wird durch Addierung aller Aufgaben
            an dem jeweiligen Tag berechnet. Verarbeitet werden die Daten von der Funktion calculateScheduledCapacityInPercent()
            */
            
            
            $sql = "SELECT
                    SUM(subaufgabenKapazität)
                    FROM kt_subaufgaben
                    WHERE subaufgabenMitarbeiterID = ?
                    AND subaufgabenBearbeitungstermin = ?;";

            $stmt = mysqli_stmt_init($connect);
            
            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmt, $sql)){
                return;
            }

            mysqli_stmt_bind_param($stmt, "is", $workerID, $date);
            mysqli_stmt_execute($stmt);
                
            $resultData = mysqli_stmt_get_result($stmt);

            if ($row=mysqli_fetch_row($resultData)){
                $scheduledCapacity = $row[0];
                return $scheduledCapacity;

            } else {
                $scheduledCapacity = false;
                return $scheduledCapacity;
            }

            mysqli_stmt_close($stmt);
        }

        function getMasterData($connect, $showTable, $workerID = null){
            /*
            Aus der Stammdatentabelle wird der Mitarbeitername, die Arbeitszeit des Mitarbeiters, sowie die Grundauslastung
            des Mitarbeiters abgefragt. Der Parameter workerID ist optional, da dieser nicht gebraucht wird, wenn die Funktion alle Stammdaten
            als Tabelle anzeigen soll ($showTable = True)
            */
            
            if ($showTable == TRUE){
                
                $sql = "SELECT *
                    FROM kt_mitarbeiter;";

                $stmt = mysqli_stmt_init($connect);
                
                //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
                
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    return;
                }
                
                //mysqli_stmt_bind_param($stmt, "i", $workerID);
                mysqli_stmt_execute($stmt);
                    
                $resultData = mysqli_stmt_get_result($stmt);
                while ($row = $resultData->fetch_assoc()) {
                    $hourWorkTime = 'Stunden';
                    $hourBasicScheduledCapacity = 'Stunden';
                    
                    if($row['MitarbeiterArbeitszeit']==1){
                        $hourWorkTime = 'Stunde';
                    }
                    if($row['MitarbeiterGrundauslastung']==1){
                        $hourBasicScheduledCapacity = 'Stunde';
                    }
                    echo '<tr>
                            <td><input class="tasks" type="text" name="workerName[]" maxlength="50" value="'.$row['MitarbeiterName'].'"><input type="hidden" name="workerID[]" value="'.$row['MitarbeiterID'].'"></td>
                            <td><input class="tasksCapacity" type="number" name="workTime[]" value="'.$row['MitarbeiterArbeitszeit'].'" min="6" max="10">'.$hourWorkTime.'</td>
                            <td><input class="tasksCapacity" type="number" name="basicScheduledCapacity[]" value="'.$row['MitarbeiterGrundauslastung'].'" min="0" max="4">'.$hourBasicScheduledCapacity.'</td>
                            <td class="button"><button type="submit" class="submit_button" name="delete" value="'.$row['MitarbeiterID'].'">Löschen</button></td></tr>
                    ';
                }
                    echo '<tr>
                            <td><input class="newTasks" type="text" name="newWorkerName" maxlength="50" placeholder="Neuer Mitarbeiter"></td>
                            <td style="color:grey;"><input class="newTasksCapacity" type="number" name="newWorkTime" placeholder="6" min="6" max="10">Stunden</td>
                            <td style="color:grey;"><input class="newTasksCapacity" type="number" name="newBasicScheduledCapacity" placeholder="0" min="0" max="4">Stunden</td>
                            <td class= "button"></td>
                        
                        <tr>
                            <td class="button" colspan="3"><input class="button" id="masterButton" type="submit" name="save" value="Speichern"></td>
                            <td class="button"></td></tr>
                    ';

            }else{

                $sql = "SELECT
                        MitarbeiterName, MitarbeiterArbeitszeit, MitarbeiterGrundauslastung
                        FROM kt_mitarbeiter
                        WHERE MitarbeiterID = ?";

                $stmt = mysqli_stmt_init($connect);
                
                //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    return $totalCapacity = false;
                }
                
                mysqli_stmt_bind_param($stmt, "i", $workerID);
                mysqli_stmt_execute($stmt);
                    
                $resultData = mysqli_stmt_get_result($stmt);

                if ($row=mysqli_fetch_row($resultData)){
                    $workerName = $row[0];
                    $totalCapacity = $row[1];
                    $basicUsedCapacity = $row[2];

                    return array($workerName, $totalCapacity, $basicUsedCapacity);

                } else {
                    $totalCapacity = false;
                    return $totalCapacity;
                }
            }
            mysqli_stmt_close($stmt);
        }

        function calculateScheduledCapacityInPercent($connect, $date, array $workerIDs){
            /*
            Mit den Ergebnissen der Datenbankabfragen (Arbeitszeit, sowie Grundauslastung und 
            Kapazität der Aufgaben des jeweiligen Tages) wird die verplante Kapazität in Prozent berechnet.
            */
            foreach ($workerIDs as $workerID){
                $scheduledCapacity = getScheduledCapacity($connect, $date, $workerID);
                $basicScheduledCapacity = getMasterData($connect, false, $workerID)[2];
                $totalScheduledCapacity[] = $scheduledCapacity + $basicScheduledCapacity;
                $totalCapacity[] = getMasterData($connect, false, $workerID)[1];
            }

            $totalScheduledCapacity = array_sum($totalScheduledCapacity);
            $totalCapacity = array_sum($totalCapacity);
            
            if ($totalScheduledCapacity == 0){
                return 0;
            }

            if ($totalScheduledCapacity !== false && $totalCapacity !== false){
                $scheduledCapacityInPercent = $totalScheduledCapacity/$totalCapacity*100;
                
                return $scheduledCapacityInPercent;
            }else {
                return;
            }

        }
        
        function createVisualCapacity($connect,$year, $monthNumber, $day, array $workerID){
            /*
            Die Funktion bestimmt basierend auf der verplanten Kapazität in Prozent wie der Füllstand des jeweiligen
            Tages gestylt wird. Als Rückgabe wird eine Scalabale Vector Graphic (SVG) mit
            einer bestimmten class ausgegeben. Die Class (redrect, yellowrect, greenrect) wird dann
            durch den linearGradient mit der ID redfill, yellowfill oder greenfill gestylt 
            */
            $Wochentag = date('l', mktime(0, 0, 0, $monthNumber, $day, $year));
        
            if (empty($day) || $Wochentag == 'Saturday' || $Wochentag == 'Sunday'){
                return;
            }
        
            $date = $year.'-'.$monthNumber.'-'.$day;
            
            $scheduledCapacityInPercent = calculateScheduledCapacityInPercent($connect, $date, $workerID);
            if (empty($scheduledCapacityInPercent) && !is_numeric($scheduledCapacityInPercent)){
                return;
            }
            
            if($scheduledCapacityInPercent >= 101){
                return '
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
                    ';
            }


            if($scheduledCapacityInPercent < 50){
                
                return 
                        '<svg width="120" height="120" viewBox="0 0 120 120">
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
                    ';
            }
        
            if($scheduledCapacityInPercent >= 90){
                return '
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
            ';
            }else{
                return '<svg width="120" height="120" viewBox="0 0 120 120">
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
            ';
            }
        }
    
        function getListOfWorker($connect, $withEmptyField, $workerID = null){
            /* Diese Funktion erstellt das Drowpdown Menü um einen Mitarbeiter auszuwählen.
            Dazu werden aus der Stammdatentabelle die Mitarbeiter IDs, sowie die Mitarbeiter Namen abgefragt und jeweils ein
            HTML <option> tag erstellt. Die Funktion wird in Kalender.php und Aufgaben.php aufgerufen.
            Die Funktion hat einen optionalen Parameter um den ausgewählten Mitarbeiter in aufgaben.php als selected anzeigen zu können
            */
            
            $sql = "SELECT
                    MitarbeiterID, MitarbeiterName
                    FROM kt_mitarbeiter;";

                
            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            $resultData=$connect->query($sql);
            $numberOfResults = $resultData->num_rows;
            
            if($numberOfResults===0){
                return '<option value="null"selected>Erst Mitarbeiter anlegen</option>';
            }


            if ($withEmptyField == 'false'){
        
                if($numberOfResults===1){
                    
                    while ($row = $resultData->fetch_assoc()) {
                        return '<option value="'.$row['MitarbeiterID'].'"selected>'.$row['MitarbeiterName'].'</option>';
                    }
                }else{
                    while ($row = $resultData->fetch_assoc()) {
                        if ($row['MitarbeiterID']==$workerID){
                            $option[] = '<option value="'.$row['MitarbeiterID'].'"selected>'.$row['MitarbeiterName'].'</option>';
                        }else{
                            $option[] = '<option value='.$row['MitarbeiterID'].'>'.$row['MitarbeiterName'].'</option>';
                        }
                    }
                    $optionString = implode(' ', $option);
                    
                    return $optionString;
                    $connect->close();
                }
            } 
            
            if ($workerID == null){
                while ($row = $resultData->fetch_assoc()) {
                    $option[] = '<option value='.$row['MitarbeiterID'].'>'.$row['MitarbeiterName'].'</option>';               
                }
                $option[] = '<option value= null selected>-</option>';
            }else{
                while ($row = $resultData->fetch_assoc()) {
                    
                    if ($row['MitarbeiterID']==$workerID){
                        $option[] = '<option value="'.$row['MitarbeiterID'].'"selected>'.$row['MitarbeiterName'].'</option>';
                    }else{
                        $option[] = '<option value='.$row['MitarbeiterID'].'>'.$row['MitarbeiterName'].'</option>';
                    }
                }
                if ('null' == $workerID){
                    $option[] = '<option value= null selected>-</option>';
                }else{
                    $option[] = '<option value= null>-</option>';
                }
                
            }
            $optionString = implode(' ', $option);
            return $optionString;
            
            $connect->close();
        }

        function displayWorkers($connect, array $arrayWorkerIDs){
            /*Im Kalenderkopf soll der Name der ausgewählten Mitarbeiter mit einem Komma getrennt angezeigt werden
            diese Funktion fragt mit der ID des Mitarbeiters den Namen aus der Datenbank ab und erstellt den 
            anzuzeigenden Text 
            */ 
            
            foreach ($arrayWorkerIDs as $workerID){
                $workerNames[] = getMasterData($connect, false, $workerID)[0];
            } 
            $displayedString = implode(', ', $workerNames);

            return $displayedString;
        }

/*Funktionen für Datei aufgaben.php */

    function createTableTasks($connect, $workerID, $date){
        echo '<form action="formAufgaben.php" method="post" name="tasks">';
        echo '<table class="tasks">
            <thead>
                <tr>
                    <th>Aufgabentitel</th>
                    <th>Kapazität</th>
                    <th>Endtermin</th>
                    <th>Mitarbeiter</th>
                    <th class= button></th>
                </tr>
            </thead>';

        echo '<tbody>';
        getTasks($connect,$workerID, $date);
        echo '</tbody></table></form>';
    }

    function getTasks($connect,$workerID = null, $date = null){
        /*
        Die Funktion erstellt eine Datenbank Abfrage, um auf der Seite aufgaben.php Aufgaben anzuzeigen
        als Parameter gibt es die ID des Mitarbeiters und das Datum. Beide sind optional um dem Benutzer
        sowohl Aufgaben eines bestimmten Mitarbeiters anzuzeigen, Aufgaben an einem bestimmten Tag anzuzeigen
        oder Aufgaben eines bestimmten Tages sowie gleichzeitig eines bestimmten Mitarbeiters anzuzeigen.
        Als Ausgabe werden Tabellenzeilen mit den jeweiligen Daten generiert. 
        */

        if ($workerID == 'null'){
            
            $sqlTasks = "SELECT
                AufgabenID, AufgabenTitel, AufgabenKapazität, AufgabenEndtermin, AufgabenMitarbeiterID
                FROM kt_aufgaben
                WHERE AufgabenEndtermin = ?;";

            $stmtTasks = mysqli_stmt_init($connect);
            
            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmtTasks, $sqlTasks)){
                return $error = true;
            }
 
            mysqli_stmt_bind_param($stmtTasks, "s", $date);
            mysqli_stmt_execute($stmtTasks);
        }

        elseif ($date == 'null'){
            
            $sqlTasks = "SELECT
                AufgabenID, AufgabenTitel, AufgabenKapazität, AufgabenEndtermin, AufgabenMitarbeiterID
                FROM kt_aufgaben
                WHERE AufgabenMitarbeiterID = ?;";

            $stmtTasks = mysqli_stmt_init($connect);
            
            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmtTasks, $sqlTasks)){
                return $error = true;
            }
 
            mysqli_stmt_bind_param($stmtTasks, "i", $workerID);
            mysqli_stmt_execute($stmtTasks);
        }

        else{
            $sqlTasks = "SELECT
                AufgabenID, AufgabenTitel, AufgabenKapazität, AufgabenEndtermin, AufgabenMitarbeiterID
                FROM kt_aufgaben
                WHERE AufgabenMitarbeiterID = ?
                AND AufgabenEndtermin = ?;";

            $stmtTasks = mysqli_stmt_init($connect);
            
            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmtTasks, $sqlTasks)){
                return $error = true;
            }
             
            mysqli_stmt_bind_param($stmtTasks, "is", $workerID, $date);
            mysqli_stmt_execute($stmtTasks);
        }       
        
        //$resultData = mysqli_stmt_get_result($stmt);
        $resultData=mysqli_stmt_get_result($stmtTasks);
             
        
        while ($row = $resultData->fetch_assoc()) {
            $workerListForTasksExist = getListOfWorker($connect, 'false', $row['AufgabenMitarbeiterID']);
            $hourTaskCapacity = 'Stunden';
            
            if($row['AufgabenKapazität']==1){
                $hourTaskCapacity = 'Stunde';
            }

            
            echo '<tr>
                    <td><input class= "tasks" type="text" name="AufgabenTitel'.$row['AufgabenID'].'" maxlength="50" value="'.$row['AufgabenTitel'].'"></td>
                    <td><input class= "tasksCapacity" type="number" name="AufgabenKapazität'.$row['AufgabenID'].'" value="'.$row['AufgabenKapazität'].'"min="1" max="500">'.$hourTaskCapacity.'</td>
                    <td><input class= "tasks" type="date" name="AufgabenEndtermin'.$row['AufgabenID'].'" value="'.$row['AufgabenEndtermin'].'"></td>
                    <td><Select class="select" name="MitarbeiterID'.$row['AufgabenID'].'">'.$workerListForTasksExist.'</Select></td>
                    <td class="button"><button type="submit" class="submit_button" name="save" value="'.$row['AufgabenID'].'">Speichern</button></td>
                    <td class="button"><button type="submit" class="submit_button" name="delete" value="'.$row['AufgabenID'].'">Löschen</button></td>
                  </tr>';
        }
        $workerListForNewTasks = getListOfWorker($connect, 'false');
        
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;
        $date = date('Y-m-d', strtotime($currentDate . ' +1 Weekday'));//Neue Aufgaben können nur eine Deadline in der Zukunft haben
        
        echo '<tr>
                <td><input class= "newTasks" type="text" name="newTaskTitle" maxlength="50" placeholder="Neue Aufgabe"></td>
                <td style="color:grey;"><input class= "newTaskCapacity" type="number" name="newTaskCapacity" placeholder="1" min="1" max="500">Stunde</td>
                <td><input class= "newTasks" type="date" name="newTaskDeadline" min="'.$date.'"></td>
                <td><Select class="newTaskSelectWorker"  name="newTaskWorker">'.$workerListForNewTasks.'</Select></td>
                <td class="button"><button type="submit" class="submit_button" name="save" value="newTask">Speichern</button></td>
                </tr>';
    }   

    function UpdateDatabase($connect, $taskID, $taskTitle, $database, $taskCapacity = null, $taskDeadline = null, $workerID = null){
        /*
        Die Funktion wird dazu genutzt entweder Einträge in der Tabelle kt_aufgaben oder kt_subaufgaben zu ändern. Die Einträge in der Tabelle
        kt_subaufgaben werden nur geändert wenn der Titel der Hauptaufgabe geändert wird (Weshalb nur der Aufgabentitel und die ID gebraucht werden 
        und die restlichen Parameter der Funktion optional sind). Ansonsten werden die Einträge gelöscht und neu angelegt.
        */

        if ($database == 'kt_aufgaben'){

            $sql = "UPDATE kt_aufgaben
                    SET AufgabenTitel = ?, AufgabenKapazität = ?, AufgabenEndtermin = ?, AufgabenMitarbeiterID = ?
                    WHERE AufgabenID = ?;";

            $stmt = mysqli_stmt_init($connect);

            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmt, $sql)){
                return $error = true;
            }
             
            mysqli_stmt_bind_param($stmt, "sisii", $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }

        if ($database == 'kt_subaufgaben'){

            $sql = "UPDATE kt_subaufgaben
                    SET subaufgabenTitel = ?
                    WHERE subaufgabenStammaufgabe = ?;";

            $stmt = mysqli_stmt_init($connect);

            //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
            if(!mysqli_stmt_prepare($stmt, $sql)){
                return $error = true;
            }
             
            mysqli_stmt_bind_param($stmt, "si", $taskTitle, $taskID);

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }
    }

    function InsertDatabase($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID){
        
        $sql = "INSERT INTO kt_aufgaben (AufgabenTitel, AufgabenKapazität, AufgabenEndtermin, AufgabenMitarbeiterID)
                VALUES (?,?,?,?);";
        
        $stmt = mysqli_stmt_init($connect);
            
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
             
        mysqli_stmt_bind_param($stmt, "sisi", $taskTitle, $taskCapacity, $taskDeadline, $workerID);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    function emptyInputTasks($taskTitle, $taskCapacity, $taskDeadline, $workerID){
        //Prüfen, ob das Formular vollständig ausgefüllt wurde
        $result;
        if (empty($taskTitle) || empty($taskCapacity) || empty($taskDeadline) || empty($workerID)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    function assignTaskToDate($connect, $taskTitle, $taskCapacity, $taskDeadline, $taskWorkerID, $taskID = null, $startDate = null){
        /*Die Funktion teilt eine Aufgabe auf verschiedene Tage auf. Der Parameter $taskID ist optional, da neue Aufgaben noch keine 
        ID haben und die HAuptaufgabe erst in dieser Funktion angelegt wird. Der Parameter wird nur bei der Änderung von Aufgaben benötigt.
        Genauso ist auch der Parameter startdate (Datum an dem die Aufgabe begonnen wird, in der Regel der frühest mögliche Zeitpunkt) optional
        da bei der Änderung an Aufgaben wie z.B. ändern der Deadline die Aufgabe neu aufgeteilt wird und die Aufgabe am selben Zeitpunkt anfangen soll wie zuvor
        */

        if ($startDate == !null){
           $arrayStartDate = explode('-',$startDate);
           $currentYear = $arrayStartDate[0];
           $currentMonth = $arrayStartDate[1];
           $currentDay = $arrayStartDate[2];
           $days = 0;
       }else{
           $currentDay = date('d');
           $currentMonth = date('m');
           $currentYear = date('Y');
           $days = 1;
       }

       if ($taskID == null){
           $masterTaskID = selectIDFromLatestTask($connect);
       }else{
           $masterTaskID = $taskID;
       }

       

       while($taskCapacity !== 0){
           
           $currentDate = $currentYear.'-'.$currentMonth.'-'.$currentDay;

           $date = date('Y-m-d', strtotime($currentDate . ' +'.$days.' Weekday'));

           $scheduledCapacity = getScheduledCapacity($connect, $date, $taskWorkerID); //Bereits Verplante Zeit für diesen Tag
           $basicScheduledCapacity = getMasterData($connect, false, $taskWorkerID)[2]; //Grundauslastung des Mitarbeiters pro Tag
           $totalCapacity = getMasterData($connect, false, $taskWorkerID)[1]; //Arbeitszeit des Mitarbeiters an einem Tag
           $freeCapacity = $totalCapacity - ($scheduledCapacity + $basicScheduledCapacity); //Freie Zeit an dem Tag
           
           if ($freeCapacity > $taskCapacity){
               $subtaskCapacity = $taskCapacity;
           }else{
               $subtaskCapacity = $freeCapacity;
           }

           if ($date == $taskDeadline){
               $subtaskCapacity = $taskCapacity;
           }

           if($freeCapacity > 0 || $date == $taskDeadline){
           
                insertIntoSubtasks($connect, $taskTitle, $subtaskCapacity, $date, $taskWorkerID, $masterTaskID);

                $taskCapacity = $taskCapacity - $subtaskCapacity;
           }

           $days = $days + 1;
       }

   }

    function insertIntoSubtasks($connect, $taskTitle, $taskCapacity, $processingDate, $workerID, $masterTaskID){
        
        
        $sql = "INSERT INTO kt_subaufgaben (subaufgabenTitel, subaufgabenKapazität, subaufgabenBearbeitungstermin, subaufgabenMitarbeiterID, subaufgabenStammaufgabe)
                VALUES (?,?,?,?,?);";
        
        $stmt = mysqli_stmt_init($connect);
            
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
            
        mysqli_stmt_bind_param($stmt, "sisii", $taskTitle, $taskCapacity, $processingDate, $workerID, $masterTaskID);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    function selectIDFromLatestTask($connect){

        $sql = "SELECT MAX(AufgabenID)
                FROM kt_aufgaben;";

        $result=$connect->query($sql);

        if ($row=mysqli_fetch_row($result)){
            $latestTaskID = $row[0];
            return $latestTaskID;
        }else{
            return $error = true;
        }
    }

    function editTasks($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID){
        /*Die Funktion ändert die Aufgaben in der Datenbank. Dabei werden unterschiedliche Funktionen aufgerufen, je nachdem was der Benutzer
        geändert hat.
        -Benutzer teilt bestehende Aufgabe einen anderen Mitarbeiter zu:
         Die Subtasks werden bei dem alten Mitarbeiter gelöscht (deleteSubtasks).
         Daraufhin wird die Hauptaufgabe geupdatet (UpdateDatabase) und in Subaufgaben zerteilt und dem neuen Mitarbeiter zugeteilt.(assigntaskToDate)
         
        -Kapazität oder Deadline wurde geändert:
        Es wird das Datum gewählt, an dem der Mitarbeiter die Aufgabe anfangen sollte, da ab diesem Zeitpunkt die Aufteilung der Hauptaufgabe wieder beginnt.
        Die Subtasks werden gelöscht (deleteSubtasks).
        Die Hauptaufgabe wird aktualisiert und mit der veränderten Kapazität und/oder Deadline neu aufgeteilt.
         
        -Nur der Titel der Aufgabe wurde geändert:
        Der Titel der Hauptaufgabe und der subaufgaben werden geupdatet.
         */
        $changes = checkChanges($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID);
    
        if (in_array('WorkerNotChanged', $changes) && in_array('CapacityNotChanged', $changes) && in_array('DeadlineNotChanged', $changes) && in_array('TitleNotChanged', $changes)){
            
            return;
        }

        

        if (in_array('WorkerChanged', $changes)){
            deleteSubtasks($connect, $taskID);
            $database = 'kt_aufgaben';
            UpdateDatabase($connect, $taskID, $taskTitle, $database, $taskCapacity, $taskDeadline, $workerID);
            assignTaskToDate($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID);
            return;
        }
    

        if (in_array('CapacityChanged', $changes) || in_array('DeadlineChanged', $changes)){
            $database = 'kt_aufgaben';
            $newDeadline = $taskDeadline;
            $subTasks = selectSubtasks($connect, $taskID);
            $firstProcessingDay = $subTasks[2][0];


            deleteSubtasks($connect, $taskID);
            UpdateDatabase($connect, $taskID, $taskTitle, $database, $taskCapacity, $newDeadline, $workerID);
            assignTaskToDate($connect, $taskTitle, $taskCapacity, $newDeadline, $workerID, $taskID, $firstProcessingDay);
            
        }


        if (in_array('TitleChanged', $changes)){
            $database = 'kt_aufgaben';
            UpdateDatabase($connect, $taskID, $taskTitle, $database, $taskCapacity, $taskDeadline, $workerID);
            $database = 'kt_subaufgaben';
            UpdateDatabase($connect, $taskID, $taskTitle, $database);
        }
    }

    function selectSubtasks($connect, $taskID){
        /*Diese Funktion fragt die Subaufgaben einer Hauptaufgabe in der Datenbank ab. Aktuell wird nur der erste Bearbeitungstermin
        einer Aufgabe genutzt um bestehende Aufgaben ab diesem Zeitpunkt neu aufteilen zu können. */
        $sql = "SELECT subaufgabenTitel, subaufgabenKapazität, subaufgabenBearbeitungstermin, subaufgabenMitarbeiterID
                FROM kt_subaufgaben
                WHERE subaufgabenStammaufgabe = ?;";
    
        $stmt = mysqli_stmt_init($connect);
        
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
            
        mysqli_stmt_bind_param($stmt, "i", $taskID);
        mysqli_stmt_execute($stmt);

        $resultData=mysqli_stmt_get_result($stmt);
    

        while ($row = $resultData->fetch_assoc()){

            $listTitle[] = $row['subaufgabenTitel'];
            $listCapacity[] = $row['subaufgabenKapazität'];
            $listProcessingDate[] = $row['subaufgabenBearbeitungstermin'];
            $listWorker[] = $row['subaufgabenMitarbeiterID'];
        }

        $list = array($listTitle, $listCapacity, $listProcessingDate, $listWorker);

        mysqli_stmt_close($stmt);
        return $list;
    }

    function deleteSubtasks($connect, $taskID){
        /*Die Funktion löscht die Subaufgaben einer Hauptaufgabe, wird genutzt wenn der Benutzer eine bestehende Aufgabe einem anderen Mitarbeiter zuordnet
        oder die Deadline/Kapazität einer bestehenden Aufgabe ändert.*/

        $sql = "DELETE FROM kt_subaufgaben 
                WHERE subaufgabenStammaufgabe = ?;";
    
        $stmt = mysqli_stmt_init($connect);
        
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
            
        mysqli_stmt_bind_param($stmt, "i", $taskID);
        mysqli_stmt_execute($stmt);
    }

    function checkChanges($connect, $taskTitle, $taskCapacity, $taskDeadline, $workerID, $taskID){
    
        $sqlTasks = "SELECT
                AufgabenTitel, AufgabenKapazität, AufgabenEndtermin, AufgabenMitarbeiterID
                FROM kt_aufgaben
                WHERE AufgabenID = ?;";

        $stmtTasks = mysqli_stmt_init($connect);
            
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmtTasks, $sqlTasks)){
            return $error = true;
        }

        mysqli_stmt_bind_param($stmtTasks, "i", $taskID);
        mysqli_stmt_execute($stmtTasks);
            
        $resultData=mysqli_stmt_get_result($stmtTasks);

        if ($row=mysqli_fetch_row($resultData)){
            $oldTaskTitle = $row[0];
            $oldTaskCapacity = $row[1];
            $oldTaskDeadline = $row[2];
            $oldTaskWorkerID = $row[3];
        }
        
        if($oldTaskTitle == $taskTitle && $oldTaskCapacity == $taskCapacity && $oldTaskDeadline == $taskDeadline && $oldTaskWorkerID == $workerID){
            $changes[] = 'none';
            mysqli_stmt_close($stmtTasks);
            return $changes;
        }

        if ($oldTaskTitle !== $taskTitle){
            $changes[] = 'TitleChanged';
        }else{
            $changes[] = 'TitleNotChanged';
        }

        if ($oldTaskCapacity != $taskCapacity){
            $changes[]= 'CapacityChanged';
        }else{
            $changes[] = 'CapacityNotChanged';
        }

        $deadlines;
        if ($oldTaskDeadline !== $taskDeadline){
            $changes[] = 'DeadlineChanged';
            $deadlines[] = $oldTaskDeadline;
            $deadlines[] = $taskDeadline;
        }else{
            $changes[] = 'DeadlineNotChanged';
        }

        if ($oldTaskWorkerID != $workerID){
            $changes[]= 'WorkerChanged';
        }else{
            $changes[] = 'WorkerNotChanged';
        }

        mysqli_stmt_close($stmtTasks);

        return $changes;
    }

    function deleteTask($connect, $taskID){
        /*Die Funktion löscht eine gewisse Hauptaufgabe.*/

        $sql = "DELETE FROM kt_aufgaben 
                WHERE AufgabenID = ?;";
    
        $stmt = mysqli_stmt_init($connect);
        
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
            
        mysqli_stmt_bind_param($stmt, "i", $taskID);
        mysqli_stmt_execute($stmt);
    }



/*Funktionen für Datei stammdaten.php*/

    function createTableMasterdata($connect){
        echo '<form action="formStammdaten.php" method="post" id="masterdata">';
        echo '<table class="tasks">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Arbeitszeit</th>
                    <th>Grundauslastung</th>
                    <th class="button"> </th>
                </tr>
            </thead>';

        echo '<tbody>';
        getMasterdata($connect, TRUE);
        echo '</tbody></table></form>';
    }

    function UpdateMasterDatabase($connect, $workerID, $workerName, $workTime, $basicScheduledCapacity){
        
        $sql = "UPDATE kt_mitarbeiter
                SET MitarbeiterName = ?, MitarbeiterArbeitszeit = ?, MitarbeiterGrundauslastung = ?
                WHERE MitarbeiterID = ?;";

        $stmt = mysqli_stmt_init($connect);
            
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
             
        mysqli_stmt_bind_param($stmt, "siii", $workerName, $workTime, $basicScheduledCapacity, $workerID);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    function InsertMasterDatabase($connect, $workerName, $workTime, $basicScheduledCapacity){
        $sql = "INSERT INTO kt_mitarbeiter (MitarbeiterName, MitarbeiterArbeitszeit, MitarbeiterGrundauslastung)
                VALUES (?,?,?);";
        
        $stmt = mysqli_stmt_init($connect);
            
        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmt, $sql)){
            return $error = true;
        }
             
        mysqli_stmt_bind_param($stmt, "sii", $workerName, $workTime, $basicScheduledCapacity);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    function emptyInputWorker($workerName, $workTime, $basicScheduledCapacity){
        //Prüfen, ob das Formular vollständig ausgefüllt wurde
        $result;
        if (empty($workerName) || empty($workTime) || empty($basicScheduledCapacity)  && !is_numeric($basicScheduledCapacity)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }

    function isDateInvalid($date){

        $dateTime = new DateTime($date);
        $now = new DateTime();

        if($dateTime < $now) {
            return true;
        }
        $date = explode('-',$date);
        $day = $date[2];
        $month = $date[1];
        $year = $date[0];

        $weekday = date('l', mktime(0, 0, 0, $month, $day, $year));

        if($weekday == 'Saturday' || $weekday == 'Sunday'){
            return true;
        }

        return false;
    }

    function deleteWorker($connect, $workerID){

        $sqlDeleteTasks = "DELETE FROM kt_aufgaben 
                            WHERE AufgabenMitarbeiterID = ?;";

        $stmtDeleteTasks = mysqli_stmt_init($connect);

        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmtDeleteTasks, $sqlDeleteTasks)){
        return $error = true;
        }

        mysqli_stmt_bind_param($stmtDeleteTasks, "i", $workerID);
        mysqli_stmt_execute($stmtDeleteTasks);

        
        
        
        $sqlDeleteSubtasks = "DELETE FROM kt_subaufgaben 
                                WHERE subaufgabenMitarbeiterID = ?;";

        $stmtDeleteSubtasks = mysqli_stmt_init($connect);

        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmtDeleteSubtasks, $sqlDeleteSubtasks)){
        return $error = true;
        }

        mysqli_stmt_bind_param($stmtDeleteSubtasks, "i", $workerID);
        mysqli_stmt_execute($stmtDeleteSubtasks);

        
        
        
        $sqlDeleteWorker = "DELETE FROM kt_mitarbeiter 
                                WHERE MitarbeiterID = ?;";

        $stmtDeleteWorker = mysqli_stmt_init($connect);

        //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
        if(!mysqli_stmt_prepare($stmtDeleteWorker, $sqlDeleteWorker)){
        return $error = true;
        }

        mysqli_stmt_bind_param($stmtDeleteWorker, "i", $workerID);
        mysqli_stmt_execute($stmtDeleteWorker);


        mysqli_stmt_close($stmtDeleteWorker);
    }

/*Funktionen für Datei details.php */

        function showDetails($connect, $date, array $workerIDs){
            /*
            Die Funktion zeigt die Aufgaben eines Mitarbeiters an einem bestimmten Tag in einer Tabelle an.
            -An Werktagen die in der Zukunft liegen und keine Aufgaben eingetragen sind, wird der Tabellenkopf mit einer Maske für eine neue Aufgabe angezeigt.
            -An Werktagen die in der Zukunft liegen und Aufgaben enthalten, wird der Tabellenkopf mit Zeilen für die Aufgaben und einer Maske für neue Aufgaben angezeigt.
            -An Werktagen die nicht in der Zukunft liegen und keine Aufgaben eingetragen sind, wird der Tabellenkopf und eine leere Zeile angezeigt.
            -An Werktagen die nicht in der Zukunft liegen und Aufgaben enthalten wird der Tabellenkopf und Zeilen für die Aufgaben angezeigt.
             */
            
            //Datum in das Format DD.MM.YYYY ändern
            $parseDate = explode('-', $date);
            $showDate = sprintf("%02d", $parseDate[2]).'.'.sprintf("%02d", $parseDate[1]).'.'.sprintf("%02d", $parseDate[0]);
            
            if(count($workerIDs)>1){
                $workerListForNewTasks = getListOfWorker($connect, 'false');
            }else{
                $workerListForNewTasks = getListOfWorker($connect, 'false', $workerIDs[0]);
            }

            echo '<form action="formDetails.php" method="post" name="tasks">';
            echo '<table class="tasks" id="details">
                    <thead>
                        <tr class="nameAndDate">
                            <th colspan="4"><input type="hidden" name="hiddenDate" value="'.$date.'">'.$showDate.'</th>
                        </tr>
                        <tr>
                            <th>Aufgabe</th>
                            <th>Dauer (Std.)</th>
                            <th>Deadline</th>
                            <th>Mitarbeiter</th>
                        </tr>
                    </thead>';

            echo '<tbody>';
            
            $queryEmpty = true; //Keine Aufgaben an diesem Tag = true
            
            foreach($workerIDs as $worker){
                $functionReturnisEmpty = getDetails($connect, $date, $worker);
                
                if($functionReturnisEmpty !== true){
                    $queryEmpty = false;
                }
                if($queryEmpty == false){
                    echo getDetails($connect, $date, $worker);
                }
            }
                $stringWorkerIDs = implode(',', $workerIDs);
                $deadline = sprintf("%02d", $parseDate[0]).'-'.sprintf("%02d", $parseDate[1]).'-'.sprintf("%02d", $parseDate[2]);
            
                if (isDateInvalid($date) == false){
            
                    echo '<tr>
                        <td><input class= "newTasks" type="text" name="newTaskTitle" maxlength="50" placeholder="Neue Aufgabe"></td>
                        <td style="color:grey;"><input class= "newTaskCapacity" type="number" name="newTaskCapacity" placeholder="1"min="1" max="500"></td>
                        <td><input class= "newTasks" type="date" name="newTaskDeadline" value="'.$deadline.'" readonly></td> 
                        <td><input type="hidden" value="'.$stringWorkerIDs.'" name="hiddenWorker"><Select class="newTaskSelectWorker"  name="newTaskWorker">'.$workerListForNewTasks.'</Select></td>
                        </tr>
            
                        <tr>
                        <td colspan="4" class="button"><input class="button" type="submit" name="save" value="Speichern"></td>
                        </tr>
                        ';
                }
                if($queryEmpty == true && isDateInvalid($date) !== false){
                    echo '<tr>
                        <td></td>
                        <td></td>
                        <td></td> 
                        <td></td>
                        </tr>';
                }   
            echo '</tbody></table></form>';
        }

            function getDetails($connect, $date, $workerID){
                /*Die Funktion stellt Informationen für die Funktion showDetails() in Form eines
                Tabellenkörpers zur Verfügung. 
                 */
                
                $sql = "SELECT subaufgabenTitel, subaufgabenKapazität, subaufgabenStammaufgabe
                        FROM kt_subaufgaben
                        WHERE subaufgabenBearbeitungstermin = ?
                        AND subaufgabenMitarbeiterID = ?;";
            
                $stmt = mysqli_stmt_init($connect);
                
                //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
                if(!mysqli_stmt_prepare($stmt, $sql)){
                    $error = true;
                    return $error;
                }
                
                mysqli_stmt_bind_param($stmt, "si", $date, $workerID);
                mysqli_stmt_execute($stmt);

                $resultData=mysqli_stmt_get_result($stmt);
            
                $name = getMasterData($connect, false, $workerID)[0];

                $numberOfResults = $resultData->num_rows;
                if($numberOfResults===0){
                    $empty = true;
                    return $empty;
                }

                $resultArray;
                while ($row = $resultData->fetch_assoc()){
                    $deadline = getDeadline($connect, $row['subaufgabenStammaufgabe']);
                    $parseDate = explode('-', $deadline);
                    $deadlineFormatted = $parseDate[2].'.'.$parseDate[1].'.'.$parseDate[0];
                    $resultArray[] = '<tr>
                            <td><a href="aufgaben.php?worker='.$workerID.'&date='.$deadline.'&submitDateAndID=Anzeigen">'.$row['subaufgabenTitel'].'</a></td>
                            <td>'.$row['subaufgabenKapazität'].'</td>
                            <td>'.$deadlineFormatted.'</td>
                            <td>'.$name.'</td>
                          </tr>';
                }
                $resultString = implode(' ', $resultArray);

                return $resultString;
                mysqli_stmt_close($stmt);
            }

                function getDeadline($connect, $taskID){
                    /*Diese Funktion fragt auf der Datenbank die Deadline einer Hauptaufgabe an und stellt diese
                    der Funktion getDetails() zur Verfügung. */
                    
                    $sql = "SELECT AufgabenEndtermin
                    FROM kt_aufgaben
                    WHERE AufgabenID = ?;";

                    $stmt = mysqli_stmt_init($connect);
            
                    //Prüfen ob die Abfrage funktionieren würde (ohne Usereingaben)
                    if(!mysqli_stmt_prepare($stmt, $sql)){
                        return $error = true;
                    }
                    
                    mysqli_stmt_bind_param($stmt, "i", $taskID);
                    mysqli_stmt_execute($stmt);

                    $resultData=mysqli_stmt_get_result($stmt);
                    
                    if ($row=mysqli_fetch_row($resultData)){
                        $deadline = $row[0];
                        //Deadline in das Format DD.MM.YY ändern.
                    }
                    return $deadline;
                }

                

?>