<?php
require_once 'classes/Session.php';

if(Session::isLoggedIn() == TRUE)
    echo "Login worked!";

else
    echo "Login Failed!!"
?>