<?php
use PHPMailer\PHPMailer\PHPMailer;
use HPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class EmailHandler 
    {    
    
    private $mail;
    private $db;

    function __construct()
    {
        $this->db = new dbConnect();
    }
    
    function registrationLink($userEmail, $registrationCode)
    {
        $this->mail = new PHPMailer(true);
        try 
        {
            //Server settings
            //$this->mail->SMTPDebug = 2;                                       // Enable verbose debug output
            $this->mail->isSMTP();                                            // Set mailer to use SMTP
            $this->mail->Host       = '';                    // Specify main and backup SMTP servers
            $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $this->mail->Username   = '';                 // SMTP username
            $this->mail->Password   = '';                         // SMTP password
            $this->mail->SMTPSecure = '';                                  // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port       = 0;                                    // TCP port to connect to       
            
            $this->mail->setFrom('noreply@valkyrie.one', 'noreply');
            $this->mail->addAddress($userEmail); 

            // Content
            $this->mail->isHTML(true); // Set email format to HTML
            $this->mail->Subject = '[Valkyrie.One] Confirm your new account';
            $this->mail->Body    = '<p>Welcome to Valkyrie.One!</p>
                              <p>Click the following link to confirm and activate your new account: http://valkyrie.one/register.php?token='. $registrationCode . '</p>
                              <p>If the above link is not clickable, try copying and pasting it into the address bar of your web browser.</p>';        
            $this->db->insertRegToken($registrationCode, $userEmail);        
            $this->mail->send();
        } 
        catch (Exception $e) 
        {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

?>