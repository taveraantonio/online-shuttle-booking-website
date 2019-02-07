<?php
    session_start();
    include 'functions.php'; 
    if(isset($_SESSION["username"]))  
    {
        if(!isLoginSessionExpired()){
			//if the login session is not expired, update login time and redirect to homepage 
            $_SESSION["logtime"] = time(); 
        }else{
			//else redirect to login page 
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
            <title>Shuttle Homepage</title>
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
        
        
        <body onload="checkCookie();">
            <div class="header">
                <div class="hometitle">
                    <span class="border"> SHUTTLE SERVICE </span>
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
                    <a href="about.php">About</a>
                </div>
            
                <div class="mainsection">
                    <?php
                    $ris = returnItinerary();
                    $dim = mysqli_num_rows($ris);
                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                    ?>
                    <div class="outertablediv">
                        <?php if($dim>0){ ?>
                        <h3>Our itinerary</h3>
                        <h4>
                            Here you can see the full list of addresses to be visited by the shuttle, in alphabetical order, (which is also the itinerary
                            of the shuttle). For each segment you can see the number of booked passenger. If you want to have more information or if you want
                            to book a trip please Register or, if you are already a member, Login following the relative buttons in the left. 
                            Each user can make a single booking for number of people who will travel together, from a certain departure address to a certaine destination address.
                        </h4>
                    </div>
                    <table class="innertablediv">
                        <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Passengers</th>
                        </tr>
                        <?php 
                        while ($row != NULL) { ?>
                            <tr>
                            <td><?php echo ($row[0]); ?></td>
                            <td><?php echo ($row[1]); ?></td>
                            <td><?php
                                if($row[2]==0)
                                    echo ("No passengers"); 
                                else
                                    echo ($row[2]);
                                ?>
                            </td>
                            </tr><?php
                            $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                        }?>
                    </table>
                    <?php
					mysqli_free_result($ris);
					} else { ?>
                        <br><br>
                        <h3>There is no planned trip. Register or Login and book yours</h3>
                        <h4>
                            Here you can see the full list of addresses to be visited by the shuttle, in alphabetical order, (which is also the itinerary
                            of the shuttle). For each segment you can see the number of booked passenger. If you want to have more information or if you want
                            to book a trip please Register or, if you are already a member, Login following the relative buttons in the left. 
                            Each user can make a single booking for number of people who will travel together, from a certain departure address to a certaine destination address.
                        </h4>
                        </div>
                    <?php  } ?>
                </div>
            </div>
            
            <div class="footer">
                &copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
            
        </body>
    </html>