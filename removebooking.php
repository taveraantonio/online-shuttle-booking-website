<?php
session_start();
    include 'functions.php'; 
    if (isset($_SESSION["username"])){
        if(hasBooked($_SESSION["username"])){
			if(isLoginSessionExpired()){
				//if user has booked a trip and login session is expired, redirect to homepage 
				session_destroy();
				header('HTTP/1.1 307 temporary redirect');
				header("location:access.php?action=login");
				exit(); 
            }else{
				//otherwise allow navigation
                $_SESSION["logtime"] = time();
            }  
        }else{
			//if user has not booked redirect to user homepage
            header('HTTP/1.1 307 temporary redirect');
            header("location:userhome.php");
            exit();    
        }
    }else{
		//if someone, that is not the logged user, try to acces this page, redirect to homepage 
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
        exit; 
    }
    
	//set cookie and check if they are active 
    setcookie('foo', 'bar', time()+3600);
    setcookie('page', $_SERVER['REQUEST_URI'], time()+3600);
    if((isset($_COOKIE["checked"]) && $_COOKIE["checked"]==false ) || (!isset($_COOKIE["checked"]))){
        header('HTTP/1.1 307 temporary redirect');
        header("location:check.php");
        exit(); 
    }
?>


<!DOCTYPE html>  
    <html lang="it">  
       
		<head>  
			<title>Remove Booking</title>
			<meta charset="utf-8">
			<!-- multiplatform meta tag, device-width follow the screen of the device, initial scale to one set the initial zoom level -->
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<!-- import stylesheet -->
			<link rel="stylesheet" href="css/home.css" type="text/css">
			<noscript>
                <div class="alert">
					<!-- Javascript not enabled alert -->
					<b>Warning!</b> Javascript is not enabled. Web site may not work properly. Plese enable it from the setting page of your broswer
                </div>
            </noscript>
           
		</head>
      
		<body onload="checkCookie();">
           
			<div class="userheader">
                <div class="hometitle">
                    <span class="border">SHUTTLE SERVICE</span>
                    <br><br>
                    <span class="innerborder">Welcome, <?php $name = explode("@", $_SESSION["username"]); echo($name[0]); ?></span>
                </div>
			</div>
           
			<div class="content">
            
                <div class="leftsidebar">
                    <a href="userhome.php">Personal Page</a>
                    <a href="about.php">About</a>
                </div>
            
                <div class="mainsection">
					<br><br><br>
                    <div class="container">
						<form action="server.php" method="post">
							<h3 style="text-align: center;">Are you sure to delete your trip?</h3>
							<input type="submit" value="Delete" id="delete" name="delete">   
						</form>
						<button onclick="window.location.href='userhome.php';">Cancel</button>    
                    </div>  
                </div>
            </div>
            
            <div class="footer">
				&copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
           
		</body>
	</html> 
      
   