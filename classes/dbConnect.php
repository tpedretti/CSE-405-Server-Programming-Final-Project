<?php
/**
 * Description of dbConnect
 *
 * @author Nex
 * 
 * Class that is to handle all database connects and info coming from it.
 */
require_once '../config.inc.php';
require_once 'EmailHandler.php';

class dbConnect 
{
    private $db;
    
    function __construct() 
    {
        //set_error_handler(arrary($this, "errorHandler"));
        $old_error_handler = set_error_handler($this, "errorHandler");
        $this->db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
        mysqli_select_db($this->db, DB_NAME);
    }
    
    function __destruct() 
    {
        mysqli_close($this->db);
    }


    function errorHandler($errno, $errstr) 
    {
        switch ($errno)
        {
            case E_WARNING:
                echo '<b>There has been an error with the MySQL database connection. '.
                     'Please, make sure the config file is OK.</b>';
                die(); 
                break;
            default:
                return false;
        }
        
        return true;
    }
    
    //Function for searching for the serivce the user wants to find at given hospital
    //This function needs to search for all different version of a word because
    //some hospitals will maybe use a different name than another hospital, or
    //The user could enter X-Ray but could be in the DB be 
    function searchForService($userSearchTerm, $hospital)
    {
        //$query = "SELECT * FROM serviceInfo WHERE service_description LIKE '%$userSearch%' AND hospital_id=$hospital;";
        $query = "select service_description,charge,hospital_procedure_code,HCPCS_id from serviceInfo where hospital_id=$hospital and service_description LIKE '%$userSearchTerm%';";           
        return mysqli_query($this->db, $query);
    }
    
    function getAllHostpials()
    {
        $query = "SELECT * FROM hospitalInfo;";
        $result = mysqli_query($this->db, $query);
        
        return $result;
    }
    
    function getHCPCSInfo($code)
    {
        $query = "SELECT HCPCS_id, description FROM HCPCSCodes WHERE HCPCS_id=\"$code\";";           
        return mysqli_query($this->db, $query);
    }
    
    //-------------USER FUNCTIONS-------------------------
    
    //User Login/Resigter
    function userLogin($userEmail, $password)
    {
        //Check to see if user entered in both password and userEmail correct
        //If so login in if not state info is wrong even if account doesn't in DB.
        //Password should be hashed before coming to this function
        
        //SQL will check for only UserEmail but return the password for the user
        $query = "select email,password from users where email='$userEmail' and password='$password'";
        $result = mysqli_query($this->db, $query);
        //$row = mysqli_fetch_assoc($result);        
        
        /*echo "SQL Statement: " . $query . "</br>";
        echo "Email: " . $userEmail . "</br>";
        echo "Password: " . $password . "</br>";
        echo "Amount of rows returned: " . mysqli_num_rows($result) . "</br>";*/
        
        if(mysqli_num_rows($result) > 0)
        {
            $row = mysqli_fetch_assoc($result);
            //User account is there check to see if password is correct
            if($row['password'] == $password)
            {
                //User entered in correct info
                //Send to session to create session with info about user
                //echo "User Supplied password is same! </br>";                
                return TRUE;
            }
            else if($row['password'] != $password)
            {
                //User enter in wrong password
                //echo "User Supplied password isn't same! </br>";
                return FALSE;
            }
        }
        
        return FALSE;
    }
    
    //Function for creataing a new account for a user
    function createNewAccount($userEmail, $password)
    {
        //Need to check if useremail isn't already used.
        //If so return error standing userEmail is taken.
        $query = "SELECT `email` FROM `users` WHERE `email`='$userEmail'";
        $result = mysqli_query($this->db, $query);
        
        if(mysqli_num_rows($result) > 0)
        {
            //Useremail is taken can't create account.
            return FALSE;
        }
        else
        {
           //Useremail ins't taken can create account.
            
            //echo "Creating new account! </br>";            
            $query = "INSERT INTO `users`(`email`, `password`) VALUES (\"$userEmail\",\"$password\");";
            $result = mysqli_query($this->db, $query);            
            //echo mysqli_error($this->db);            
            //var_dump($result);
            $emailVer = new EmailHandler();
            $registrationCode = sha1($userEmail + date("M,d,Y h:i:s A"));
            $emailVer->registrationLink($userEmail, $registrationCode);
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    //User Registration Token that is created when they make an account then need to-go-to email to active account
    function insertRegToken($registrationCode, $userEmail)
    {
        $query = "INSERT INTO `userRegistration`(`registrationCode`, `valid`, `userEmail`) VALUES (\"$registrationCode\",1,\"$userEmail\");";
        $result = mysqli_query($this->db, $query);        
        return $result;
    }

    function checkToken($registrationToken)
    {
        $query = "SELECT `registrationCode`, `valid` FROM `userRegistration` WHERE `registrationCode`=\"$registrationToken\"";
        $result = mysqli_query($this->db, $query);       
        
        if(mysqli_num_rows($result) > 0)
        {
            $query = "UPDATE `userRegistration` SET `valid`=0, `usedOn`=now() WHERE `registrationCode`=\"$registrationToken\"";
            $result = mysqli_query($this->db, $query);   
            return TRUE;
        }
        
        return FALSE;
    }
    
    //Function for adding, editing, and removing a users bookmarks.
    function editBookmark()
    {
        
    }
}
?>