<?php
    session_start();
    include 'functions.php';
    if(isset($_SESSION["username"])) {
        if(!isLoginSessionExpired()){
			//if the login session is not expired, update login time and redirect to homepage 
            $_SESSION["logtime"] = time();
            header('HTTP/1.1 301 temporary redirect');
            header("location:userhome.php");
            exit();
        }else{
			//else redirect to login page 
            session_destroy();
            header('HTTP/1.1 307 temporary redirect');
            header("location:access.php?action=login");
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

<?php
	//use https in this page if it is not yet set 
    if (!isset($_SERVER["HTTPS"])&&strtolower($_SERVER["HTTPS"])!=="on") { 
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location:'.$redirect);
	exit();
    }
?>



<!DOCTYPE html>
    <html lang="it">
       
		<head>
			<title>Access Page</title>
			<meta charset="utf-8">
			<!-- multiplatform meta tag, device-width follow the screen of the device, initial scale to one set the initial zoom level -->
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<!-- import stylesheet -->
			<link rel="stylesheet" href="css/home.css" type="text/css">
			<script type="text/javascript" src="javascript/jsfunctions.js"></script>
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
                    <span class="border">SHUTTLE SERVICE</span>
                </div>
			</div>
           
			<div class="content">
            
                <div class="leftsidebar">
					<a href="index.php">Homepage</a>
                    <a href="access.php?action=login">Login</a>
                    <a href="access.php">Sign Up</a>
                    <a href="about.php">About</a>
                </div>
            
                <div class="mainsection">
                 <br><br><br>
                    <div class="container">
						<?php
						if(isset($_REQUEST["action"])){
							//login action 
							$action = sanitizeString($_REQUEST["action"]);
							if($action == "login"){ ?>
							    <h2 align="center">Login</h2>
							    <hr> <br>
							    <form method="post" action="server.php">
							        <p>Enter Username:</p>
                                    <input type="text" name="username" class="username" id="email" placeholder="enter email here" required>
                                    <br>
                                    <p>Enter Password:</p>
                                    <input type="password" name="password" class="password" id="password" placeholder="enter password here" required>
									<br>
                                    <input type="submit" name="login" value="Login" onclick="validate()">
                                    <br>
								</form>
                       
								<div>
                                    <button onclick="window.location.href='index.php';">Cancel</button>
								</div>
								<div>
                                    <p align="right"><a href="access.php">I don't have an account</a></p>
								</div>
                       
                               <?php
							}else{
								//requested an action not handled
								?><script type=text/javascript>
								window.location.href = "access.php?action=login";
								</script>
								<noscript>
								    <p>You have no rights to access this page. Go back</p>
                                    <a href="access.php?action=login">Back</a>
								</noscript>
								<?php
							}
						}else{ ?>
                            <!--register function -->
							<h2 align="center">Register</h2>
                            <hr><br>
                            <form method="post" action="server.php">
                                <p>Enter a valid email:</p>
                                <input type="text" name="username" id="email" placeholder="enter email here (valid format is: name@domain.xxx)" required>
								<br>
                                <p>Enter a valid password:</p>
                                <input type="password" name="password" class="password" id="password" placeholder="enter password here (1 l.c. and, 1digit or 1 u.c. char required)" required> 
                                <br>
                                <input type="submit" name="register" value="Register" onclick="validate()">
                                <br>
                            </form>
                            <div>
                                <button onclick="window.location.href='index.php';">Cancel</button>
                            </div>
                            <div>
                                <p align="right"><a href="access.php?action=login">I have an account</a></p>
                            </div>
							<?php
						} ?>
                    </div>
                </div>
			</div>
            
            <div class="footer">
                &copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
			
		</body>
 </html> 