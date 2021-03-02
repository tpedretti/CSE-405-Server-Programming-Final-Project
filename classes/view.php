<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of view
 *
 * @author Nexus
 */

require_once 'dbConnect.php';

class view 
{
    private $db;
    
    function __construct() {
        $this->db = new dbConnect();
    }
    
    //Function for creating whole webpage the users see.
    function mainFrontWebPage()
    {
        $content = "";       
        $content .= 
                "<form class=\"form-search-service\" method=\"post\" action=\"servicesearch.php\">
                    
                    <div class=\"form-group\">
                        <legend for=\"serviceSearchTerm\">Service Search</legend>
                        <input type=\"text\" class=\"form-control\" name=\"serviceTermInput\" aria-describedby=\"serviceTerm\" placeholder=\"Service Term\">
                    </div>
                    
                    <div class=\"form-group\">
                        <legend for=\"hospitalMenu\">Hospital</legend>
                        <select class=\"form-control\" name=\"hospitalSelect\">";
        
                        //Get all Hostipals
                        $hospitals = $this->db->getAllHostpials();
                        while($row = mysqli_fetch_assoc($hospitals))
                        {
                            $content .= "<option value=".$row['hospital_id'].">".$row['hospital_name']."</option>";
                        }
        
        $content .=    "</select>
                    </div>
                    
                    <button type=\"submit\" class=\"btn btn-primary\">Search</button>
                 </form>";
        
        return $content;
    }
    
    //Function to create webpage after the user seaches for a term.
    function resultPage()
    {
        $hcCodes = array();        
        $content = "";
        
        //Warn use that the prices they see may not be the same and their insurance can mod it.
        $content .= "<div class=\"alert alert-dismissible alert-warning\">
                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
                            <h4 class=\"alert-heading\">Warning!</h4>
                            <p class=\"mb-0\">All prices are an average from hospital. Prices may vary depending on type of insurance you have.</p>
                    </div>
                    <table class=\"table table-hover\">
                        <thead>
                            <tr>                                
                                <th scope=\"col\">Service Name</th>
                                <th scope=\"col\">Average Price</th>
                                <th scope=\"col\">Procedure Code</th>
                                <th scope=\"col\">HCPCS Code</th>
                            </tr>
                        </thead>";
        
        $serviceResults = $this->db->searchForService($_SESSION['searchTerm'], $_SESSION['hospital']);
        
        if(mysqli_num_rows($serviceResults) > 0)
        {
            while($row = mysqli_fetch_assoc($serviceResults))
            {
                $content .= "<tr>";
                $content .= "<th scope=\"row\">". $row['service_description'] ."</th>";
                $content .= "<td>" . number_format($row['charge'],2) . "</td>";
                
                if($row['hospital_procedure_code'] != '' || $row['HCPCS_id'] != null)
                { 
                    $content .= "<td>" . $row['hospital_procedure_code'] . "</td>";
                }
                else
                {
                    $content .= "<td>" . $row['hospital_procedure_code'] . "</td>";
                }
                
                //HCPCS Codes for Result Table    
                if($row['HCPCS_id'] != '' || $row['HCPCS_id'] != null)
                {
                    if(!in_array($row['HCPCS_id'], $hcCodes))
                    {
                        array_push($hcCodes, $row['HCPCS_id']);
                    }

                    $content .= "<td>" . $row['HCPCS_id'] . "</td>";
                }
                else
                {
                    $content .= "<td>" . $row['HCPCS_id'] . "</td>";;
                }
                
                $content .= "</tr>";
            }
            
            $content .= "</table>";
            
            if(count($hcCodes) > 0)
            {
                $content .= "<table class=\"table table-hover\">
                                <thead>
                                    <tr>                                
                                        <th scope=\"col\">HCPCS Code</th>
                                        <th scope=\"col\">HCPCS Description</th>
                                    </tr>
                                </thead>";
                
                foreach($hcCodes as $v)
                {
                    //var_dump($hcCodes);
                    
                    $hcInfo = $this->db->getHCPCSInfo($v);
                    //var_dump($hcInfo) . "</br></br></br>";
                    if($hcInfo != null)
                    {
                        if(($row = mysqli_fetch_assoc($hcInfo)) != null)
                        {
                            //var_dump($row);
                            $content .= "<tr>";
                            $content .= "<th scope=\"row\">". $row['HCPCS_id'] ."</th>";
                            $content .= "<td>". $row['description'] ."</td>";
                            $content .= "</tr>";
                        }
                        else
                        {
                            $content .= "<tr>";
                            $content .= "<th scope=\"row\">". $v ."</th>";
                            $content .= "<td>No info found about this code in database!</td>";
                            $content .= "</tr>";
                        }
                    }
                }
                
                $content .= "</table>";
            }
        }
        else
        {
            $content .= "<tbody>
                            <tr>
                                <th scope=\"row\">NOTHING FOUND</th>
                                <td colspan=\"3\">NOTHING FOUND</td>
                            </tr>
                        </tbody>";
            $content .= "</table>";
        }     

        
       // $content .= "</table>";
        
        Session::frontPage();        
        return $content;
    }

    //Function for creating user login page.
    function userLogin()
    {
        //Checks to see if the server had a request that is post, so check
        //if useremail and password is set and if the useremail isn't take already.
        //if everything is right login user in and return to homepage
        if($_SERVER["REQUEST_METHOD"] == "POST")
        {
            if(isset($_POST['inputEmail']) && isset($_POST['inputPassword']))
            {
                $hashed = sha1($_POST['inputPassword']);                
                if($this->db->userLogin($_POST['inputEmail'], $hashed) == TRUE)
                {
                    Session::login($_POST['inputEmail']);
                    //header("Location: index.php");
                    echo "<script type=\"text/javascript\">window.location.href ='http://valkyrie.one';</script>";
                }
                else
                {
                    $_SESSION['loginFail'] = TRUE;
                }
            }
        }
        
        $content = "";
        
        if($_SESSION['loginFail'] == TRUE)
        {
            $content .= "<div class=\"alert alert-dismissible alert-danger\">
                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
                            <strong>User email or Password is wrong!</strong>
                        </div>";
        }
        
        $content .= 
                "<form class=\"form-signin\" method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">
                    <fieldset>
                        <legend>User Login</legend>
                        
                        <div class=\"form-group\">
                            <label for=\"inputEmail\" class=\"col-sm-2 col-form-label\">Email Address</label>
                            <input type=\"email\" id=\"inputEmail\" name=\"inputEmail\" class=\"form-control\" placeholder=\"Email address\" required autofocus>
                        </div>

                        <div class=\"form-group\">
                            <label for=\"inputPassword\" class=\"col-sm-2 col-form-label\">Password</label>
                            <input type=\"password\" id=\"inputPassword\" name=\"inputPassword\" class=\"form-control\" placeholder=\"Password\" required>
                        </div>
                        
                        <button class=\"btn btn-lg btn-primary btn-block\" type=\"submit\">Sign in</button>
                    </fieldset>
                </form>";        
        
        $_SESSION['loginFail'] = FALSE;        
        return $content;
    }
    
    function register()
    {
        $content = "";
        $vaildToken = false;
        $accountCreated = false;
        $accountTaken = false;
        
        if(isset($_GET['token']))
        {
            //Token from url that user get from email link. Why make a new php file and not do in one.
            //echo $_GET['token'] . "</br>";
            $vaildToken = $this->db->checkToken($_GET['token']);
            //echo $vaildToken . "</br>";
        }           
        
        if($_SERVER["REQUEST_METHOD"] == "POST" && $vaildToken == false)
        {
            if(isset($_POST['inputEmail']) && isset($_POST['inputPassword']))
            {
                $hashed = sha1($_POST['inputPassword']);                
                if($this->db->createNewAccount($_POST['inputEmail'], $hashed) == TRUE)
                {                
                    $accountCreated = true;
                }
                else
                {
                    //UserEmail is already taken so say email is taken.
                    $accountTaken = true;
                }
            }
        }
        
        if($accountCreated == false && $vaildToken == false && $vaildToken == false)
        {
            if($accountTaken == TRUE)
            {
                $content .= "<div class=\"alert alert-dismissible alert-danger\">
                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
                                <strong>Email is already taken!</strong>
                            </div>";
            }            
            
            $content .= 
                    "<form class=\"form-signin\" method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">
                        <fieldset>
                            <legend>Register</legend>

                            <div class=\"form-group row\">
                                <label for=\"inputEmail\" class=\"col-sm-2 col-form-label\">Enter Email Address</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"email\" id=\"inputEmail\" name=\"inputEmail\" class=\"form-control\" placeholder=\"Email address\" required autofocus>
                                </div>
                            </div>

                            <div class=\"form-group row\">
                                <label for=\"inputPassword\" class=\"col-sm-2 col-form-label\">Enter Password</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"password\" id=\"inputPassword\" name=\"inputPassword\" pattern=\"(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}\"  class=\"form-control\" placeholder=\"Password\" required>
                                </div>
                            </div>

                            <button class=\"btn btn-lg btn-primary btn-block\" type=\"submit\">Create Account</button>
                        </fieldset>
                    </form>";
        
            return $content;
        }
        else if($accountCreated == true && $vaildToken == false && $vaildToken == false)
        {
            $content .= "<main role=\"main\" class=\"inner cover\">
                            <h1 class=\"cover-heading\">Account Created</h1>
                            <p class=\"lead\">Please check your email to comfirm account.</p>
                        </main>";
            
            return $content;
        }
        
        if($vaildToken == true)
        {
            $content .= "<main role=\"main\" class=\"inner cover\">
                            <h1 class=\"cover-heading\">Account Activated</h1>
                            <p class=\"lead\">You may now login.</p>
                        </main>";
            
            return $content;
        }
        
        return $content;
    }
}
?>