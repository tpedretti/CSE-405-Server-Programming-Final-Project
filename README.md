CSE-405 - Server Programming - Final Project
=====

# **Course Overview:** 
Throughout the class we were tasked with different assignments from the professor which ranged from research on different frameworks to ideas for the final project of the class. The final project for the class was picked by students based on whatever they wanted to do it on, but had to involve a server and client/front-end. 

# **Project Overview:** 

This project is based around the database that I and my team designed in my Database Systems class. This database was created to reflect the new ruling passed in a executive order in 2019 (<https://www.nytimes.com/2019/11/15/health/list-hospital-prices-trump.html>), which forces hospitals to reveal the prices of operations and procedures that they have at their hospital. This takes the data from the Hospitals which range from csv, xml and other files types and puts them all into a database and then searches this database based on the user input and what hostpial they want to search at.

This project is mainly writen in PHP, using Bootstrap for HTML/CSS theming.

# **Project Details/Walkthrough:**

The main power house for the website is `index.php` which changes based on what the user is doing on the website. 


### Index.php

First we include some classes files to deal with different things for us:
```php
require_once 'classes/view.php';
require_once 'classes/Session.php';
```

`view.php` deals with what is displayed on the website based on the state and `Session.php` deals with our sessions for logging in and out of the website.

Next is to check if a session is going on to switch the header to user logined mode and display their username and logout button. After this we include the header file which will change based off current session info.

Once done with all of the above we declare `$view = new view();` which is our variable that deals with which state the view classes is in on our website. Then we delcare our if-else statement which handles which state the session switch is in.

```php
if($_SESSION['counter'] == 1 || $_SESSION['PAGE'] == "_FP" || $_SESSION['PAGE'] == '')
{
    echo $view->mainFrontWebPage();
}
else if($_SESSION['PAGE'] == "_RP")
{
    echo $view->resultPage();
}
```


### view.php

The view classes in this project is what handles what is returned to` index.php` based on the user action that is requested on it.

First thing that is done in this class is we include `dbConnect.php` which handles the communications between the website and database server which is MySQL server.

```php
function mainFrontWebPage()
{
    $content = "";    
       
    ...
    ...
    
    return $content;
}
```
The function `mainFrontWebPage()` handles the front page of the website when the page isn't in results mode. The front page is mainly just a search bar with a menu to select which hospital the user would like to search. Once the user clicks the search button the action is then send to the `servicesearch.php` to search for anything the user is looking for within the database.

```php
function resultPage()
{
    $hcCodes = array();        
    $content = "";
    
    ...
    ...
    
    Session::frontPage();        
    return $content;
}
```
The `resultPage()` function is what handles displaying the results that are returned from the database. This information is then turned into a table and displayed to the user.

```php
function userLogin()
{
    ...
    ...      
    return $content;
}
```
`userLogin()` function handles the login page in `login.php`

```php
function register()
{
    ...
    ...
    return $content;
}
```
`register()` function handles the register page in `register.php`


### dbConnect.php

`dbConnect.php` is the class which handles all communications from the website to the MySQL server. 

```php
require_once '../config.inc.php';
require_once 'EmailHandler.php';
```
First thing in this class is the includes which one is for info about the database info like IP and other things. The next is for `EmailHandler.php` which handles anything that needs to be emailed on this website like new user welcome email and lost password.

```php
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
```
Next is our variable that works with the mysqli functions for connecting and getting things from the database. Then comes the constructer and destruct for the class each time something needs to get handled a connect to the database is made and once done the connect is closed.

```php
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
```

Following the construct and destruct function is a errorhandler to handle any errors which would be thrown by the mysqli functions.

```php
function searchForService($userSearchTerm, $hospital)
{
    ...
    return mysqli_query($this->db, $query);
}
```

`searchForService()` function is used to search for a service that a user want to search. The term they want to look for and hospital to be search are passed to this function.


```php
function getAllHostpials()
{
    $query = "SELECT * FROM hospitalInfo;";
    $result = mysqli_query($this->db, $query);
    
    return $result;
}
```

As the function `getAllHostpials()`states this function get all hostpials in our database.

```php
function getHCPCSInfo($code)
{
    $query = "SELECT HCPCS_id, description FROM HCPCSCodes WHERE HCPCS_id=\"$code\";";           
    return mysqli_query($this->db, $query);
}
```

`getHCPCSInfo($code)` function gets all info about HCPCSCodes which are:
> HCPCS codes are used for billing Medicare & Medicaid patients — The Healthcare Common Prodecure Coding System (HCPCS) is a collection of codes that represent procedures, supplies, products and services which may be provided to Medicare beneficiaries and to individuals enrolled in private health insurance programs.

```php
function userLogin($userEmail, $password)
{
     ...
     ...    
    return FALSE;
}
```

`userLogin($userEmail, $password)` function handles the user login actions when a user is trying to login.

```php
function createNewAccount($userEmail, $password)
{
    ...
    ...    
    return FALSE;
}
```
`createNewAccount($userEmail, $password)` as the function name states this handles the creation of new accounts.

```php
function insertRegToken($registrationCode, $userEmail)
{
    ...  
    ...     
    return $result;
}

function checkToken($registrationToken)
{
    ...
    ...    
    return FALSE;
}
```
Both of these functions go hand in hand because once a new account is created a token for that account is created and emailed to the user. If a user doesn't go to their email and active their account in a given time the token isn't good anyone and that account would be deleted. 


### Session.php

`Session.php` handles our php sessions that happen on the website.

```php
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
```
`start()` function is called when a user vists the website. Sets everything to emtpy or false.

```php
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
```

These two functions go hand in hand. login handles when a user logins in and the other is used when they logout of the website.

```php
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
```

Both of these functions handle what page the user is currently looking at.

```php
public static function isLoggedIn()
{
    return $_SESSION['userLoggedIn'];
}

public static function getUserName()
{
    return $_SESSION['userName'];
}
```
Check to see if the user is logined in or not and if so what is their username.

### login.php and register.php

Both of this are laid out just like `index.php` they both call to the view class and then the function that deal with login or register.

### EmailHandler.php

Only a few things happen in this class first we make our variables that deal with our database talking to and the mail variable that is used with the `PHPMailer `library to send our registration links to new users.