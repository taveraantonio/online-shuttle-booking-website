<?php
session_start();
    
    include 'functions.php';
    if (isset($_SESSION["username"])){
        if(isLoginSessionExpired()){
			//if user was logged in and is login session is expired, redirect to login page 
            session_destroy();
            header('HTTP/1.1 307 temporary redirect');
            header("location:access.php?action=login");
            exit(); 
        }else{
			//else update time
            $_SESSION["logtime"] = time();
        }
    }else{
		//if user is not logged in it has no permission to access this page, redirect to homepage
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
        exit();
    }
    
	
	//set cokkie and check if they are active 
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
            <title>User Homepage</title>
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
            
            <div class="userheader">
                <div class="hometitle">
                    <span class="border">SHUTTLE SERVICE</span>
                    <br><br>
                    <span class="innerborder">Welcome, <?php $name = explode("@", $_SESSION["username"]); echo($name[0]); ?></span>
                </div>
            </div>
        
            <div class="content">
                <div class="leftsidebar">
                    <a href="index.php">Homepage</a>
                    <?php if(hasBooked($_SESSION["username"])){ ?><a href="removebooking.php">Delete Trip</a> <?php }else{ ?> <a href="booking.php">Book a Trip</a>  <?php } ?>
                    <a href="logout.php">Logout</a>
                    <a href="about.php">About</a>
                   
                </div>
            
                <div class="mainsection">
                    <?php
                    //do this if user has already booked a trip, draw its table higlithing in red departure and arrival
                    if(hasBooked($_SESSION["username"])){
						//return the user trip (arrival and departure)
                        $infouser = returnUserTrip($_SESSION["username"]);
                        $row = mysqli_fetch_array($infouser, MYSQLI_ASSOC);
                        $userSource = $row["source"];
                        $userDest = $row["destination"];
                        $conn = dbConnect();
						//return all the itinerary with people in each route from itinerary table 
                        $ris = returnItineraryPeople();
                        $dim = mysqli_num_rows($ris);
                        $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                        ?>
                        <div class="outertablediv">
                            <h3>Your Booking</h3>
                            <h4>
                                Here you can see the full itinerary of the shuttle. You can also see, for each segment, the number of passengers who will
                                be on the shuttle in that segment along with the usernames of the users who have booked them and for how many passengers each user has booked.
                                You can see, highlighted in red color, the departure and the arrival address of your booking.
                                If you want to delete your reservation just click on the Delete button in the left.
                            </h4>
                            </div>
                        <table class="innertablediv">
                            <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Total</th>
                            <th>Passengers</th>
                            </tr>
                            
                            <?php
                            while ($row != NULL) { ?>
                                <tr>
                                    <?php
                                    $username = explode("@", $row["user"]); 
                                    $source = $row["source"];
                                    $dest = $row["destination"];
                                    $numpeople = $row["numpeople"];
                                    if($numpeople ==""||$numpeople==NULL){
                                        $numpeople = 0; 
                                    }
                                    $total = $numpeople; 
                                    ?>
                                    <td><?php
                                        if($source == $userSource)
                                            echo("<span style='background-color:red; color:white;'>" . $source . "</span>"); 
                                        else
                                            echo($source);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($dest == $userDest)
                                            echo("<span style='background-color:red; color:white;'>" . $dest . "</span>"); 
                                        else
                                            echo($dest);
                                        ?>
                                    </td>
                                    <?php
                                    if($numpeople == 0){
                                        $array="No passengers";
                                    }else {
                                        $array="user " . $username[0] . "(" . $numpeople . " passenger/s)";
                                    }
                                    $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                                    while($source ==  $row["source"] && $row!= NULL){
                                        $username = explode("@", $row["user"]); 
                                        $total = $total + $row["numpeople"];
                                        $array = ($array . ", user " . $username[0] . "(" . $row["numpeople"] . " passenger/s)");
                                        $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                                    }
                                    ?>
                                    <td><?php echo($total); ?></td>
                                    <td><?php echo($array); ?></td>
                                </tr><?php
                            }?>
                        </table>
                     
                    <?php
                    
                    //do this if user has not booked a trip, the same as above but not highliting in red
                    } else {
                        $ris = returnItineraryPeople();
                        $dim = mysqli_num_rows($ris);
                        $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                        ?>
                        <div class="outertablediv">
                            <?php if($dim>0){ ?>
                            <h3>Full Itinerary</h3>
                            <h4>
                                Here you can see the full itinerary of the shuttle. You can also see, for each segment, the number of passengers who will
                                be on the shuttle in that segment along with the usernames of the users who have booked them and for how many passengers each user has booked.
                                If you want to book a trip with us you can simply follow the Book button on the left and make your reservation
                            </h4>
                        </div>
                        
                        <table class="innertablediv">
                            <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Total</th>
                            <th>Passengers</th>
                            </tr>
                            
                            <?php
                            while ($row != NULL) { ?>
                                <tr>
                                    <?php
                                    $username = explode("@", $row["user"]); 
                                    $source = $row["source"];
                                    $dest = $row["destination"];
                                    $numpeople = $row["numpeople"];
                                    if($numpeople ==""||$numpeople==NULL){
                                        $numpeople = 0; 
                                    }
                                    $total = $numpeople; 
                                
                                    ?>
                                    <td><?php echo($source); ?></td>
                                    <td><?php echo($dest); ?></td>
                                    <?php
                                    if($numpeople == 0){
                                        $array="No passengers";
                                    }else {
                                        $array="user " . $username[0] . "(" . $numpeople . " passenger/s)";
                                    }
                                   
                                    $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                                    while($source == $row["source"] && $row!= NULL){
                                        $username = explode("@", $row["user"]); 
                                        $total = $total + $row["numpeople"];
                                        $array = ($array . ", user " . $username[0] . "(" . $row["numpeople"] . " passenger/s)");
                                        $row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                                    }
                                    ?>
                                    <td><?php echo($total); ?></td>
                                    <td><?php echo($array); ?></td>
                                </tr><?php 
                            }?>
                        </table>
                        <?php } else { ?>
                        <br><br>
                        <h3>There is no planned trip. Book yours!</h3>
                        <h4>
                            Here you can see the full itinerary of the shuttle. You can also see, for each segment, the number of passengers who will
                            be on the shuttle in that segment along with the usernames of the users who have booked them and for how many passengers each user has booked.
                            If you want to book a trip with us you can simply follow the Book button on the left and make your reservation
                        </h4>
                        </div>
                        <?php  }
                    } ?>
                </div>
            </div>
            
            <div class="footer">
                &copy; 2018 | Designed by Antonio Tavera - s243869 | <a href="mailto:s243869@studenti.polito.it">Contact Me</a>
            </div>
            
        </body>
    </html>
