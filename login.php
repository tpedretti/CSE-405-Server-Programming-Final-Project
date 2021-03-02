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
echo $view->userLogin();

require_once 'semantics/footer/footer.html';
?>