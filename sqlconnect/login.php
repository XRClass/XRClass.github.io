<?php
    $con = mysqli_connect('bqbxbuerhzolifexxeim-mysql.services.clever-cloud.com', 'ur31kfvnrvrocgdy', 'fdZgRxAydtLaR9c3HhLe', 'bqbxbuerhzolifexxeim');

    //check that connection happened

    if(mysqli_connect_errno())
    {
        echo "1: Connection failed"; //error code #1 = connection failed
        exit();
    }

    $username = mysqli_real_escape_string($con,$_POST["name"]);//$_POST["name"];
    $usernameclean = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    $password = $_POST["password"];

    //check if name exists
    $namecheckquery = "SELECT username, salt, hashe, score, ADMN FROM players WHERE username='" . $usernameclean . "';";

    $namecheck = mysqli_query($con, $namecheckquery) or die("2: Name check query failed"); //eror code #2 - name check query failed

    if(mysqli_num_rows($namecheck) != 1)
    {
        echo "5: Name does not exist!"; //error code #5 - number of names matching != 1
        exit();
    }

    //echo (string)mysqli_num_rows($namecheck);

    //get login info from array
    $existinginfo = mysqli_fetch_assoc($namecheck);
    $salt = $existinginfo["salt"];

    //echo $salt;

    $hash = $existinginfo["hashe"];

    //echo "_____________________". $hash;

    $loginhash = crypt($password, $salt);
    if($hash != $loginhash)
    {
        echo "6: Incorrect password"; // error code #6 - password does not hash to match table
        exit();
    }

    echo "0\t" . $existinginfo["score"] ."\t" . $existinginfo["ADMN"];


?>