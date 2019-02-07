<?php
session_start();
    
    include 'functions.php';
    if (isset($_SESSION["username"])){
        if(isLoginSessionExpired()){
			//if the login session is expired redirect client to login page 
            session_destroy();
            header('HTTP/1.1 307 temporary redirect');
            header("location:access.php?action=login");
            exit();
        }else if(hasBooked($_SESSION["username"])){
			//else if it has booked redirect to user homepage, it is not admitted to book again
            $_SESSION["logtime"] = time();
            header('HTTP/1.1 307 temporary redirect');
            header("location:userhome.php");
            exit();
        }else{
			//otherwise, update login time and allow booking
            $_SESSION["logtime"] = time();
        }
    }else{
		//if it is not logged in redirect to homepage
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
        exit();
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
	//set https if it is not set yet 
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
            <title>Booking</title>
            <meta charset="utf-8">
			<!-- multiplatform meta tag, device-width follow the screen of the device, initial scale to one set the initial zoom level -->
            <meta name="viewport" content="width=device-width, initial-scale=1">
			<!-- import stylesheet -->
            <link rel="stylesheet" href="css/home.css" type="text/css">
            <link rel="stylesheet" href="css/table.css" type="text/css">
			<!-- import javascript functions -->
            <script type="text/javascript" src="javascript/jsfunctions.js"></script>
            <noscript>
                <div class="alert">
					<!-- Javascript not enabled alert -->
                    <b>Warning!</b> Javascript is not enabled. Web site may not work properly. Plese enable it from the setting page of your broswer
                </div>
            </noscript>
        </head>
        
        
        <body onload="checkCookie(); hidefield();">
            <div class="userheader">
                <div class="hometitle">
                    <span class="border">SHUTTLE SERVICE</span>
                    <br><br>
					<!--span is like a div but is an inline tag -->
                    <span class="innerborder">Welcome, <?php $name = explode("@", $_SESSION["username"]); echo($name[0]); ?></span>
                </div>
            </div>
        
            <div class="content">
                <div class="leftsidebar">
                    <a href="index.php">Homepage</a>
                    <a href="userhome.php">Your Profile</a>
                    <a href="logout.php">Logout</a>
                    <a href="about.php">About</a>
                </div>
            
                <div class="mainsection">
                    <div class="container" style="width: 500px; box-shadow:0 0 0 0;">
                        <h1 style="text-align: center; color: #4CAF50;">Book your trip</h1><br>
                        
                        <form action="server.php" method="post">
							<!--starting of the form to book a trip -->
							<!--choice of departure-->
                            <p><b>DEPARTURE:</b><br>select a place from the list or choose 'Other' to insert a place:</p>
                            <div class="selectsection">
                                <select name="departure" id="departure" onchange="showfield1(this.options[this.selectedIndex].value);">
                                    <option value="disabled">Click to select one place or Other</option>
                                    <?php
                                    $ris = returnRoutes();
                                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                    while($row!=NULL){
                                    ?><option><?php echo($row[0]); ?></option>
                                    <?php
                                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                    }
                                    mysqli_free_result($ris);
                                    ?>
                                    <option value="other1">Other</option>
                                </select>
                            </div>
                            <div id="div1">
                                <p>Insert your departure place:</p>
                                <input type="text" name="other1text" id="other1text" placeholder="text here">
                            </div>
                            
                            <br>
                            <!-- choice of arrival -->
                            <p><b>ARRIVAL:</b><br>select a place from the list or choose 'Other' to insert a place:</p>
                            <div class="selectsection">
                                <select name="arrival" id="arrival" onchange="showfield2(this.options[this.selectedIndex].value);">
                                    <option value="disabled">Click to select one place or Other</option>
                                    <?php
                                    $ris = returnRoutes();
                                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                    while($row!=NULL){
                                    ?><option><?php echo($row[0]); ?></option>
                                    <?php
                                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                    }
                                    mysqli_free_result($ris);
                                    ?>
                                    <option value="other2">Other</option>
                                </select>
                            </div>
                            <div id="div2">
                                <p>Insert your arrival place:</p>
                                <input type="text" name="other2text" id="other2text" placeholder="text here">
                            </div>
                            
                            <br>
                            <!-- choice the number of passengers -->
                            <p><b>PASSENGERS:</b><br>select how many passengers you travel with:</p>
                            <div class="selectsection">
                                <select name="passengers" id="passengers">
                                    <?php
                                    for($i=0; $i<$buscapacity; $i++){
                                    ?><option><?php echo($i+1); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <br>
                            <div>
								<!--submit the form -->
                                <input type="submit" name="book" value="Book" onclick="validateBooking();">
                            </div>  
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                &copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
			
        </body>
    </html>
