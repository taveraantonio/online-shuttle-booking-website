<?php
    session_start();
    include 'functions.php'; 
    if(isset($_SESSION["username"]))  
    {
        if(!isLoginSessionExpired()){
			//if the login session is not expired, update login time 
            $_SESSION["logtime"] = time(); 
        }else{
			//else redirect client to homepage 
            session_destroy();
            header('HTTP/1.1 307 temporary redirect');
            header("location:index.php");
            exit(); 
        } 
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
            <title>About Page</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="css/home.css" type="text/css">
            <noscript>
                <div class="alert">
					<!-- Javascript not enabled alert -->
                    <b>Warning!</b> Javascript is not enabled. Web site may not work properly. Plese enable it from the setting page of your broswer
                </div>
            </noscript>
        </head>
        
        
        <body onload="checkCookie();">
			
            <div class="header">
                <div class="hometitle">
                    <span class="border">ABOUT</span>
                </div>
            </div>
        
            <div class="content">
                <div class="leftsidebar">
                    <a href="index.php">Homepage</a>
                    <?php
                    if(isset($_SESSION["username"])){ ?>
                        <a href="userhome.php">Personal Page</a>
                        <a href="logout.php">Logout</a>
                    <?php }else{ ?>
                    <a href="access.php?action=login">Login</a>
                    <a href="access.php">Sign Up</a>
                    <?php } ?>
                </div>
            
                <div class="mainsection">
                    <br><br>
                    <div class="container" style="width:auto;">
                        <h2>Our Company</h2>
                        At Shuttle Service, our goal is to provide green and smart mobility options as alternatives to the private car.
                        Bus travel is one of the eco-friendliest modes of transportation, and at Shuttle Service we take sustainability to the next level.
                        Every bus in our fleet meets a high level of efficiency in terms of fuel consumption and greenhouse gas emissions. 
                    </div>
                    <br><br>
                    <div class="container" style="width:auto;">
                        <h2>Customer Satisfaction</h2>
                        We go to great lengths to ensure our customers are satisfied. Our goal is to get passengers to their destinations safely,
                        on time and with the highest comfort possible.
                    </div>
                    <br><br>
                    <div class="container" style="width:auto;">
                        <h2>Contact</h2>
                        <b>Have a trip in mind?</b> <a href="access.php">Register</a> and <a href="access.php?action=login">Login</a> and choose one of the present choices or insert your own.<br>
                        <b>Want to see what trip the bus does?</b> Go to the <a href="index.php">homepage</a>.<br>
                        <b>Do you want to delete your reservation?</b> <a href="access.php?action=login">Access</a> and follow the 'Delete Trip' button.<br>
                        <b>Do you have other question?</b> Simply <a href="mailto:s243869@studenti.polito.it">write us</a> following the link.
                    </div>
					<br>
                </div>
            </div>
            
            <div class="footer">
                &copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
            
        </body>
    </html>