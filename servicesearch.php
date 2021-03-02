<?php
require_once 'classes/Session.php';
if(isset($_POST['serviceTermInput']) &&
   isset($_POST['hospitalSelect']))
{
    //echo "Data is set coming from index.php <br>";
    //echo $_POST['serviceTermInput'] . "<br>";
    //echo $_POST['hospitalSelect'] . "<br>";
    //echo '<pre>' . var_dump($_SESSION) . '</pre>';
    Session::resultPage($_POST['serviceTermInput'], $_POST['hospitalSelect']);
    header("Location: index.php");
}
else
{
    echo "Data isn't set coming from index.php <br>";
}
?>