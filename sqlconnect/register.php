<?php

    // Import PHPMailer classes into the global namespace
    // These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    //use PHPMailer\PHPMailer\SMTP;
    //use PHPMailer\PHPMailer\Exception;

    // Load Composer's autoloader
    //require 'vendor/autoload.php';

    // Instantiation and passing `true` enables exceptions
    

    $con = mysqli_connect('bqbxbuerhzolifexxeim-mysql.services.clever-cloud.com', 'ur31kfvnrvrocgdy', 'fdZgRxAydtLaR9c3HhLe', 'bqbxbuerhzolifexxeim');

    //check that connection happened

    if(mysqli_connect_errno())
    {
        echo "1: Connection failed"; //error code #1 = connection failed
        exit();
    }

    $username = $_POST["name"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $vkey = md5(time().$username);

    require_once "PHPMailer/PHPMailer.php";
    require_once "PHPMailer/SMTP.php";
    require_once "PHPMailer/Exception.php";

    //check if name exists
    $namecheckquery = "SELECT username FROM players WHERE username='" . $username . "';";

    $emailcheckquery = "SELECT email FROM players WHERE email='" . $email . "';";

    $namecheck = mysqli_query($con, $namecheckquery) or die("2: Name check query failed"); //error code #2 - name check query failed

    $emailcheck = mysqli_query($con, $emailcheckquery) or die("8: Email check query failed"); //error code # 8 - email check query failed

    if(mysqli_num_rows($namecheck) > 0)
    {
        echo "3: Name already exists";//error code # 3  name exists cannot register
        exit();
    }

    if(mysqli_num_rows($emailcheck) > 0)
    {
        echo "9: Email already exists";//error code # 9  email exists cannot register
        exit();
    }

    //if(mysqli_num_rows($namecheck) == 0)
    //{
    //    echo "table is empty";//table is empty
    //}

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
        echo "Error: " . $sql . "<br>" . $con->error;
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
        echo "B: INVALID EMAIL";//"A: Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }


    //header('location:thankyou.php');
    echo("0");


?>