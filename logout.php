<?php
session_start();

    if (isset($_SESSION["username"])){
		//if the user is logged in unset password and login time and destroy session, redirect to homepage
        unset($_SESSION['username']);
        unset($_SESSION['logtime']);
        session_destroy();
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
        exit(); 
    }else{
        //if someone else, that is not the logged user, try to access this page is automatically redirected to homapage
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
        exit(); 
    }      
?>


