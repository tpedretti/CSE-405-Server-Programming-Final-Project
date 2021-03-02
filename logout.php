<?php
require_once 'classes/Session.php';
Session::logout();
header("Location: index.php");
?>