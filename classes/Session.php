<?php
/**
 * Description of Session
 *
 * @author Nex
 */
class Session 
{
    
    public static function start()
    {
        session_start();
        if( isset( $_SESSION['counter'])) 
        {
            $_SESSION['counter'] += 1;            
        }
        else 
        {
            $_SESSION['counter'] = 1;
            $_SESSION['PAGE'] = '';
            $_SESSION['userLoggedIn'] = FALSE;
        }
    }
    
    //Seassion Function for Login
    public static function login($userName)
    {
        session_start();
        $_SESSION['userLoggedIn'] = true;
        $_SESSION['userName'] = $userName;
        session_commit();
    }
    
    public static function logout()
    {
        session_start();
        $_SESSION['userLoggedIn'] = false;
        $_SESSION['userName'] = '';
        session_commit();
    }
    
    public static function resultPage($serviceTermInput, $hospitalSelect)
    {
        session_start();
        $_SESSION['PAGE'] = '_RP';
        $_SESSION['searchTerm'] = $serviceTermInput;
        $_SESSION['hospital'] = $hospitalSelect;
        $_SESSION['counter']++; 
        session_commit();
    }
    
    public static function frontPage()
    {
        session_start();
        $_SESSION['PAGE'] = '_FP';
        $_SESSION['searchTerm'] = '';
        $_SESSION['hospital'] = '';
        $_SESSION['counter']++; 
        session_commit();
    }    
    
    public static function isLoggedIn()
    {
        return $_SESSION['userLoggedIn'];
    }
    
    public static function getUserName()
    {
        return $_SESSION['userName'];
    }
}
