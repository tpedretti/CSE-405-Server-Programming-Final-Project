<?php
//For Erroing Reporting
//require_once 'errorOut.php';

require_once 'classes/view.php';
require_once 'classes/Session.php';
if(session_status() == PHP_SESSION_NONE)
{    
    Session::start();
}

require_once 'semantics/header/hMaster.php';

$view = new view();

if($_SESSION['counter'] == 1 || $_SESSION['PAGE'] == "_FP" || $_SESSION['PAGE'] == '')
{
    echo $view->mainFrontWebPage();
}
else if($_SESSION['PAGE'] == "_RP")
{
    echo $view->resultPage();
}

//echo "</br>". session_status() . "</br>";
//echo "</br>User Seassion PAGE state: " . $_SESSION['PAGE'] . "</br>";
//echo '<pre>' . var_dump($_SESSION) . '</pre>';

require_once 'semantics/footer/footer.html';
?>