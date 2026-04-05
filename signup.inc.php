<?php


if(isset($_POST["submit"])) 

{
    
    //Grabbing the Data
    
    $uid=$_POST["uid"];
    $pwd=$_POST["pwd"];
    $pwdRepeat=$_POST["pwdrepeat"];
    $email=$_POST["email"];
    //$city=$_POST["city"];
    $address=$_POST["address"];


    //Instanciating Signup contr class
    include "./dbh.classes.php";
    include "./signup.classes.php";
    include "./sign-contr.classes.php";
    

    $signup= new SignupContr($uid, $pwd, $pwdRepeat, $email/* ,$city */,$address);

    // Error Handling
       $signup->signupUser();

    //Going back to front page
        header("Location: ./login.php?error=none");
    }


