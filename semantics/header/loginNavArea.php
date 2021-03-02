<?php
require_once 'classes/Session.php';

//User Login If user is logged in change to show user info if no login button.
$content = "";

if(Session::isLoggedIn() == TRUE)
{
        $content .= "<ul class=\"nav navbar-nav ml-auto\">
                  <li class=\"nav-item\">
                    <a class=\"nav-link\">" . Session::getUserName() . "</a>                      
                  </li>
                  <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"logout.php\">Logout</a>
                  </li>
              </ul>";
}
else
{
    $content .= "<ul class=\"nav navbar-nav ml-auto\">
                  <li class=\"nav-item\">
                      <a class=\"nav-link\" href=\"login.php\">Login</a>
                  </li>
                  <li class=\"nav-item\">
                      <a class=\"nav-link\" href=\"register.php\">Register</a>
                  </li>
              </ul>";
}

echo $content;
?>
