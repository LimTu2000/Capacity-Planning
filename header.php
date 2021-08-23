<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
    <?php
        $file = basename($_SERVER['PHP_SELF']);
        $title = explode('.', $file);
        echo '<title>'.ucfirst($title[0]).'</title>';
    ?>
</head>
    <body>
        <header>
            <h1>Kapazitätsplanungstool</h1>
        </header>
        
        <nav>
            <ul> <!--https://www.w3schools.com/howto/howto_css_dropdown_navbar.asp-->
                <li>
                    <div class="navbar">
                        <div class="dropdown">
                            <button <?php if ($file == 'kalender.php'){ echo'class="dropbtn_active"';}else{ echo 'class="dropbtn"';}?>>Kalender
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropdown-content">
                                <a <?php echo 'href="formKalender.php?view=month"';?>>Monatsansicht</a>
                                <a <?php echo 'href="formKalender.php?view=year"';?>>Jahresansicht</a>
                            </div>
                    </div>
                </div>
                </li>
                <li><a <?php if ($file == 'aufgaben.php'){ echo 'class="active"';}?> href="aufgaben.php">Aufgaben</a></li>
                <li><a <?php if ($file == 'stammdaten.php'){ echo 'class="active"';}?> href="stammdaten.php">Stammdaten</a></li>
                <li>
            </ul>
        </nav>