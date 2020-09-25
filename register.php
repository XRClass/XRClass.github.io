<?php
$error = NULL;

if(isset($_POST['submit'])){
    //Get form data
    $u = $_POST['u'];
    $p = $_POST['p'];
    $p2 = $_POST['p2'];
    $e = $_POST['e'];
    $vkey = md5(time().$username);
    require_once "sqlconnect/PHPMailer/PHPMailer.php";
    require_once "sqlconnect/PHPMailer/SMTP.php";
    require_once "sqlconnect/PHPMailer/Exception.php";

    if(strlen($u) < 8) {
        $error = "<p>Your username must be t least 8 characters</p>";
    }
    else if ($p2 != $p)
    {
        $error .= "<p>Your passwords do not match</p>";
    }
    else{
        //Form is valid

        //Connect to the database

        $con = mysqli_connect('bqbxbuerhzolifexxeim-mysql.services.clever-cloud.com', 'ur31kfvnrvrocgdy', 'fdZgRxAydtLaR9c3HhLe', 'bqbxbuerhzolifexxeim');
        if(mysqli_connect_errno())
        {
            echo "<p>1: Connection failed</p>"; //error code #1 = connection failed
            exit();
        }
        //check if name exists
        $namecheckquery = "SELECT username FROM players WHERE username='" . $u . "';";

        $emailcheckquery = "SELECT email FROM players WHERE email='" . $e . "';";

        $namecheck = mysqli_query($con, $namecheckquery) or die("<p>2: Name check query failed</p>"); //error code #2 - name check query failed

        $emailcheck = mysqli_query($con, $emailcheckquery) or die("<p>8: Email check query failed</p>"); //error code # 8 - email check query failed

        if(mysqli_num_rows($namecheck) > 0)
        {
            echo "<p>3: Name already exists</p>";//error code # 3  name exists cannot register
            exit();
        }

        if(mysqli_num_rows($emailcheck) > 0)
        {
            echo "<p>9: Email already exists</p>";//error code # 9  email exists cannot register
            exit();
        }
        //add user to the table
        $salt = "\$5\$rounds=5000\$" . "ruylopez" . $username . "\$";//sha 256 encryption
        //echo($salt);
        $hash = crypt($password, $salt);
        //echo($hash);
        //$insertuserquery = "INSERT INTO players (username, hashe, salt) VALUES ('" . $username . "', '" . $hash . "', '" . $salt . "');";
        $insertuserquery = "INSERT INTO players (username, hashe, salt, email, vkey) VALUES ('$username' , '$hash' , '$salt' , '$email' , '$vkey')";
        //echo($insertuserquery);
        if($con->query($insertuserquery) === TRUE)
        {
            //echo "New record created succesfully";
        }
        else{
            //THE KEYYYYY:    
            echo "<p>Error: " . $sql . "<br>" . $con->error . "</p>";
            //mysqli_query($con, $insertuserquery) or die("4: Insert player query failed"); //error code #4 - insert query failed
        }
        //mysqli_query($con, "INSERT INTO `players`(`id`, `username`, `password`) VALUES ('4', '$username', '$password');") or die("4: Failed To Write User Data To Database!"); // Error #4 Failed To Write User Data To Database
        //$to = $email;
        $subject = "XRClass Email Verification";
        $message = "Hello ". $username . ",\nPlease verify your account using this link: "."<a href='http://localhost/registration/verify.php?vkey=$vkey'>Register Account</a>";
        //$headers = "From: xrinclass@gmail.com";
        //$headers .= "MIME-Version: 1.0" . "\r\n";
        //$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        //
        //mail($to,$subject,$message,$headers) or die("FailedMAIL: " . "Error: " . "<br>" . $con->error);



        //MAIL USING <PHPMAILER class=""

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail = new PHPMailer();
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';//'smtp.yandex.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'xrinclass@gmail.com';//'support@xrclass.ml';                     // SMTP username
            $mail->Password   = 'Houseman1';                               // SMTP password
            $mail->SMTPSecure = "ssl";//PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;  
            $mail->SMTPOptions = array (
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' =>true
                )
            );                                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom($email, $username);
            //$mail->addAddress('xrinclass@gmail.com');     // Add a recipient
            $mail->addAddress($email);               // Name is optional
            $mail->addReplyTo('support@xrclass.ml', 'Support');
            //$mail->addCC('cc@example.com');
            $mail->addBCC('hb22holla@gmail.com');
        
            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            echo "<p>B: INVALID EMAIL</p>";//"A: Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }


        //header('location:thankyou.php');
        echo("0");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="">
        <table border="0" align="center" cellpadding="5">
            <tr>
                <td align="right">Username:</td>
                <td><input type="TEXT" name="u" required/></td>
            </tr>
            <tr>
                <td align="right">Password:</td>
                <td><input type="TEXT" name="p" required/></td>
            </tr>
            <tr>
                <td align="right">Repeat Password:</td>
                <td><input type="TEXT" name="p2" required/></td>
            </tr>
            <tr>
                <td align="right">Email Address:</td>
                <td><input type="TEXT" name="e" required/></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="SUBMIT" name="submit" value="Register" required/></td>
            </tr>
        </table>
    </form>
</body>
</html>