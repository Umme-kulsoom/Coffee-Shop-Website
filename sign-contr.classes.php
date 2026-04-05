<?php

session_start();

class SignupContr extends SignUp{
    private $uid;
    private $pwd;
    private $pwdRepeat;
    private $email;
    //private $city;
    private $address;

    public function __construct($uid, $pwd, $pwdRepeat, $email/* ,$city */,$address) {

        $this->uid=$uid;
        $this->pwd=$pwd;
        $this->pwdRepeat=$pwdRepeat;
        $this->email=$email;
       //$this->city=$city;
        $this->address=$address;
    }


    
    private function emptyInput(){
        $result=true;
        if((empty($this->uid))||(empty($this->pwd))||(empty($this->pwdRepeat))||(empty($this->email))){
            $result=false;
        }
        return $result;
    }

    private function invalidUid(){
        $result=false;
        if(!preg_match("/[a-zA-Z0-9]*$/",$this->uid)){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }

    private function invalidEmail(){
        $result=false;
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }

    private function pwdMatch(){
        $result=false;
        if($this->pwd!==$this->pwdRepeat){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }
/*
    private function uidTakenCheck(){
        $result=false;
        if(!($this->checkUser($this->uid,$this->email,$this->tel))){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }
    */

    private function uidTakenCheck(){
        $result=false;
        if(!($this->checkUid($this->uid))){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }

    private function emailTakenCheck(){
        $result=false;
        if(!($this->checkEmail($this->email))){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }

    private function invalidAddress(){
        $result=false;
        if(!preg_match("/[a-zA-Z0-9]*$/",$this->address)){
            $result=false;
        } else {
            $result=true;
        }
        return $result;
    }



    public function signupUser(){
        if($this->emptyInput() == false) {
            //echo "Empty Input!"
            $_SESSION["emptyerror"]="Please Fill in all the required forms !";
            header("Location: ./signup.php?error=emptyinput");
            exit();
        }

        if($this->invalidUid() == false) {
            //echo "invalid UID !"
            $_SESSION["invaliduiderror"]="Invalid UID";
            $_SESSION["wronguid"]=$this->uid;
            header("Location: ./signup.php?error=username");
            exit();
        } 
        
            if($this->invalidUid() == true) {
            $_SESSION["correctuid"]=$this->uid;
            }



        if($this->invalidEmail() == false) {
            //echo "invalid Email!"
            $_SESSION["invalidemailerror"]="Invalid Email";
            $_SESSION["wrongemail"]=$this->email;
            header("Location: ./signup.php?error=email");
            exit();
        }

            if($this->invalidEmail() == true) {
                $_SESSION["correctemail"]=$this->email;
            }

                if($this->invalidAddress() == false) {
                    //echo "invalid Address !"
                    $_SESSION["invalidaddresserror"]="Invalid Address";
                    $_SESSION["wrongaddress"]=$this->address;
                    header("Location: ./signup.php?error=address");
                    exit();
                } 
                
                    if($this->invalidAddress() == true) {
                    $_SESSION["correctaddress"]=$this->address;
                    }
        


        if($this->pwdMatch() == false) {
            //echo "Passwords don't match!"
            $_SESSION["pwdmatcherror"]="Password and Repeat Password don't match";
            $_SESSION["wrongpwd"]=$this->pwd;
            header("Location: ./signup.php?error=passwordmatch");
            exit();
        }


            if($this->pwdMatch() == true) {
                $_SESSION["correctpwd"]=$this->pwd;
            }
        
        
        /* if($this->uidTakenCheck() == false) {
            //echo "uidTaken!"
            $_SESSION["uidtakenerror"]="UID Taken";
            $_SESSION["takenuid"]=$this->uid;
            $_SESSION["takenemail"]=$this->email;
            $_SESSION["takentel"]=$this->tel;
            header("Location: ../signup.php?error=useroremailorteltaken");
            exit();
        } */

        if($this->uidTakenCheck() == false) {
            //echo "uidTaken!"
            //$_SESSION["uidtakenerror"]="UID Taken";
            $_SESSION["uidtaken"]=$this->uid;
            header("Location: ./signup.php?error=uidtaken");
            exit();
        }

        if($this->emailTakenCheck() == false) {
            //echo "uidTaken!"
            //$_SESSION["uidtakenerror"]="UID Taken";
            $_SESSION["emailtaken"]=$this->email;
            header("Location: ./signup.php?error=emailtaken");
            exit();
        }

            /* if($this->uidTakenCheck() == true) {
                $_SESSION["correctuidtakencheck"]=$this->uid;
                $_SESSION["correctemailtakencheck"]=$this->email;
                $_SESSION["correctteltakencheck"]=$this->tel;
            } */
            
            if(($this->emptyInput() == true)&&($this->invalidUid() == true)&&($this->invalidEmail() == true)&&($this->invalidAddress() == true)&&($this->pwdMatch() == true)&&($this->uidTakenCheck() == true)){
                $_SESSION["allok"]="allok";
            }
        



        $this->setUser($this->uid,$this->pwd,$this->email/* ,$this->city */,$this->address);
    }

}