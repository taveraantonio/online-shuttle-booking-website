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
        }else if(isset($_POST["book"])||isset($_POST["delete"])){
			//else if the user is logged in and send a form to the server with booking or deleting request, update time
            $_SESSION["logtime"] = time();
        }else {
			//else update time and redirect to homepage
            $_SESSION["logtime"] = time();
            header('HTTP/1.1 307 temporary redirect');
            header("location:userhome.php");
            exit(); 
        }
    }else if(!isset($_POST["register"]) && !isset($_POST["login"])){
		//if user has no permission to access this page, redirect to homepage 
        header('HTTP/1.1 307 temporary redirect');
        header("location:index.php");
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
            <title>Server</title>
            <meta charset="utf-8">
        </head>
		
        <body>
            <?php
            ///////////////////////////////////
            //////// REGISTER SERVICE//////////
            ///////////////////////////////////
            if(isset($_POST["register"]))
            {
                if(isset($_POST["username"])&&isset($_POST["password"])){
                    
					
                    $username = sanitizeString($_POST["username"]);  
                    $password = sanitizeString($_POST["password"]);
                    $connection = dbConnect();
                   
                    try{
                        mysqli_autocommit($connection, false);
						//lock the members table
                        $sql = "SELECT * FROM members FOR UPDATE";
                        if(!mysqli_query($connection, $sql)){
                                throw new Exception("Error for update");
                        }
                        //test email and password if they satisfy requirement 
                        if(!checkUsername($username)){
                            throw new Exception("Invalid email"); 
                        }
                        if(!checkPassword($password)){
                            throw new Exception("Invalid password"); 
                        }
						//do the hash of the password 
                        $password = md5($password);
						//check if the user is already registered
                        if(alreadyRegistered($connection, $username)){
                            throw new Exception("You are already registered");
                        }
						//insert the new user 
                        $sql = "INSERT INTO members (user, password, booked, source, destination) VALUES ('" . $username . "', '" . $password ."', 0, '', '')";
                        if(!mysqli_query($connection, $sql)){ 
                            throw new Exception("Not successful registration error"); 
                        }
                        //commit
                        mysqli_commit($connection);
                        
                    }catch(Exception $e){
                        mysqli_rollback($connection);
                        mysqli_autocommit($connection, true);
                        ?>
                        <script type =text/javascript>
                        alert("<?php echo("Error: " . $e->getMessage()); ?>")
                        window.location.href = "access.php";
                        </script>
                        <?php
                    }
                    mysqli_autocommit($connection, true);
                    mysqli_close($connection);
                    ?>
                    <script type=text/javascript>
                    alert("Registration Done");
                    window.location.href = "access.php?action=login";
                    </script>
                    <noscript>
                        <p>Javascript disabled. Manually refresh page</p>
                        <a href="access.php">Go</a>
                    </noscript>
                    <?php
                         
                }else{
                    ?><script type=text/javascript>
                    alert("Both field are required"); 
                    window.location.href = "access.php";
                    </script>
                    <noscript>
                        <p>Both field are required. Tap the link to go back</p>
                        <a href="access.php">Back</a>
                    </noscript>
                    <?php
                }  
            }
            /////////////////////////////////// 
            ////////LOGIN SERVICE//////////////
            ///////////////////////////////////
            else if(isset($_POST["login"]))  
            {  
                if(isset($_POST["username"])&&isset($_POST["password"])){
                    $connect = dbConnect(); 
                    $username = sanitizeString($_POST["username"]);  
                    $password = sanitizeString($_POST["password"]);  
                    $password = md5($password);  
                    if(validUser($username, $password)){
                        //correct username and password, granted login
						//set session parameters 
                        $_SESSION['username'] = $username;
                        $_SESSION['logtime'] = time();
                        mysqli_close($connect);
                        ?><script type=text/javascript>
                        window.location.href = "userhome.php";
                        </script>
                        <noscript>
                            <p>Login done. Tap the button to go to your personal page </p>
                            <a href="userhome.php">Go</a>
                        </noscript>
                        <?php
                    }else{
                        //not correct username or password, no granted login
                        mysqli_close($connect);
						?><script type=text/javascript>
                        alert("Not successful login. Try again"); 
                        window.location.href = "access.php?action=login";
                        </script>
                        <noscript>
                            <p>Not successful login. Go back to try again</p>
                            <a href="access.php?action=login">Back</a>
                        </noscript>
                        <?php
                    }
                }else{
					?>
					<script type=text/javascript>
                    alert("Both field are required"); 
                    window.location.href = "access.php?action=login";
                    </script>
                    <noscript>
                        <p>Both field are required. Tap the button to go back</p>
                        <a href="access.php?action=login">Back</a>
                    </noscript>
                    <?php
                }
            }
            /////////////////////////////////// 
            ////////BOOKING SERVICE////////////
            ///////////////////////////////////
            else if(isset($_POST["book"])){
                if(isset($_POST["departure"])&&isset($_POST["arrival"])&&isset($_POST["passengers"])){
                    
                    $conn = dbConnect(); 
                    try{
        
                        mysqli_autocommit($conn, false);
						//lock tables 
                        $sql = "SELECT * FROM routes FOR UPDATE";
                        if(!mysqli_query($conn, $sql)){
                                throw new Exception("Error for update 1");
                        }
                        $sql = "SELECT * FROM booking FOR UPDATE";
                        if(!mysqli_query($conn, $sql)){
                                throw new Exception("Error for update 2");
                        }
                        $sql = "SELECT * FROM itinerary FOR UPDATE";
                        if(!mysqli_query($conn, $sql)){
                                throw new Exception("Error for update 3");
                        }
                        $sql = "SELECT * FROM members FOR UPDATE";
                        if(!mysqli_query($conn, $sql)){
                                throw new Exception("Error for update 4");
                        }
                        
                        //////////////////DEPARTURE////////////////////////////////////////
                        if($_POST["departure"]=="Other" || $_POST["departure"]=="other1"){
							//insert the new route and update all the booking of the other passengers
                            $dep = sanitizeString($_POST["other1text"]);
							$dep = strtoupper($dep);
                            if($dep==""){
								throw new Exception("Please insert a departure place"); 
							}
                            if(isinDatabase($conn, $dep)){
                                throw new Exception("Departure place already in our route, please select it from the drop down menu");
                            }
                            $sql = "INSERT INTO routes (route) VALUES ('". $dep ."')";
                            if(!mysqli_query($conn, $sql)){
                                throw new Exception("Insert new departure station failed");
                            }
                            //select the route previous the new inserted by the client
                            $sql = "SELECT route FROM routes WHERE (route<'" . $dep . "') ORDER BY route DESC LIMIT 1";
                            $ris = mysqli_query($conn, $sql);
                            if(!$ris){
                                throw new Exception("Selecting the previous station to the new one failed");
                            }
                            $rownum = mysqli_num_rows($ris);
                            if($rownum == 0){
                                $previous = $dep; 
                            }else{
                                $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                $previous = mysqli_real_escape_string($conn, $row[0]); 
                            }
                            
                            //select the route next the new inserted by the client
                            $sql = "SELECT route FROM routes WHERE (route > '" . $dep . "') ORDER BY route LIMIT 1";
                            $ris = mysqli_query($conn, $sql);
                            if(!$ris){
                                throw new Exception("Selecting the next station to the new one failed");
                            }
                            $rownum = mysqli_num_rows($ris);
                            if($rownum == 0){
                                $next = $dep; 
                            }else{
                                $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                $next = mysqli_real_escape_string($conn, $row[0]); 
                            }
                            mysqli_free_result($ris);
                            if($previous != $next){
                                //select from itinerary all the routes that have source = previous and dest = next 
                                //select from booking all the routes that have source = previous and dest = next  
								//ma in itinerary e in booking potrebbe non esserci il caso, caso nuovo. quindi inserisco 
                                $sql = "SELECT * FROM itinerary WHERE source='" . $previous . "' AND destination='" . $next . "'";
                                $ris1 = mysqli_query($conn, $sql);
                                if(!$ris1){
                                    throw new Exception("Select from itinerary failed");
                                }
                                $sql = "SELECT * FROM booking WHERE source='" . $previous . "' AND destination='" . $next . "'";
                                $ris2 = mysqli_query($conn, $sql);
                                if(!$ris2){
                                    throw new Exception("Select from booking failed");
                                }
                                //insert into itinerary two new routes, one with source = previous and dest = $dep 
                                //insert into itinerary another route with source = $dep and dest = next
                                $rownum1 = mysqli_num_rows($ris1);
                                if($rownum1 == 0){
                                    $z = 0; 
                                    $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". $previous ."', '". $next ."', '" . $z ."')";
                                    $ris4 = mysqli_query($conn, $sql);
                                    if(!$ris4){
                                        throw new Exception("Insert new itinerary into itinerary failed");
                                    }
                                }else{
                                    $row = mysqli_fetch_array($ris1, MYSQLI_ASSOC);
                                    while($row!=NULL){
                                        $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". mysqli_real_escape_string($conn, $row['source']) ."', '". $dep ."', '". $row['passengers'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 1 into itinerary failed");
                                        }
                                        $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". $dep ."', '". mysqli_real_escape_string($conn, $row['destination']) ."', '". $row['passengers'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 2 into itinerary failed");
                                        }
                                        $row = mysqli_fetch_array($ris1, MYSQLI_ASSOC);
                                    }
                                
                                    //insert into booking two new routes, one with source = previous and dest = $dep 
                                    //insert into booking another route with source = $dep and dest = next           
                                    $row = mysqli_fetch_array($ris2, MYSQLI_ASSOC);
                                    while($row!=NULL){
                                        $sql = "INSERT INTO booking (user, source, destination, numpeople) VALUES ('". $row['user'] ."', '". mysqli_real_escape_string($conn, $row['source']) ."', '". $dep ."', '". $row['numpeople'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 1 into booking failed");
                                        }
                                        $sql = "INSERT INTO booking (user, source, destination, numpeople) VALUES ('". $row['user'] ."', '". $dep ."', '". mysqli_real_escape_string($conn, $row['destination']) ."', '". $row['numpeople'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 2 into booking failed");
                                        }
                                        $row = mysqli_fetch_array($ris2, MYSQLI_ASSOC);
                                    }
                                    //delete from itinerary all the routes that have source = previous and dest = next
                                    //delete from booking all the routes that have source = previous and dest = next
                                    $sql = "DELETE FROM itinerary WHERE source = '" . $previous . "' AND destination = '" . $next . "'";
                                    $ris1 = mysqli_query($conn, $sql);
                                    if(!$ris1){
                                        throw new Exception("delete from itinerary failed");
                                    }
                                    $sql = "DELETE FROM booking WHERE source = '" . $previous . "' AND destination = '" . $next . "'";
                                    $ris1 = mysqli_query($conn, $sql);
                                    if(!$ris1){
                                        throw new Exception("delete from booking failed");
                                    }
                                }
                                //all reschedule done 
                            }else{
                                //there are no routes 
                                $dep = $previous; 
                            }
                        }else{
							//selected a route already in the database
                            $dep = sanitizeString($_POST["departure"]);
							if($dep =="disabled"){
                                throw new Exception("Select a departure"); 
                            }
                            $dep = strtoupper($dep);
                        }
                        
                        /////////////////////////ARRIVAL/////////////////////////////
                        
                        if($_POST["arrival"]=="Other" || $_POST["arrival"]=="other2"){
							//if a new route is inserted, reschedule all the other passengers that are affected by these new route
                            $arr = sanitizeString($_POST["other2text"]);
							$arr = strtoupper($arr);
							
                            if($arr==""){
								throw new Exception("Please insert an arrival place"); 
							}
                            if(isinDatabase($conn, $arr)){
                                throw new Exception("Arrival place already in our route, please select it from the drop down menu");
                            }
                            $sql = "INSERT INTO routes (route) VALUES ('". $arr ."')";
                            if(!mysqli_query($conn, $sql)){
                                throw new Exception("insert route 2 failed");
                            }
                            //select the route previous the new inserted by the client
                            $sql = "SELECT route FROM routes WHERE (route<'" . $arr . "') ORDER BY route DESC LIMIT 1";
                            $ris = mysqli_query($conn, $sql);
                            if(!$ris){
                                throw new Exception("select previous 2 failed");
                            }
                            $rownum = mysqli_num_rows($ris);
                            if($rownum == 0){
                                $previous = $arr; 
                            }else{
                                $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                if($row ==""){
                                    $previous = $arr;
                                }
                                $previous = mysqli_real_escape_string($conn, $row[0]); 
                            }
                            
                            //select the route next the new inserted by the client
                            $sql = "SELECT route FROM routes WHERE (route>'" . $arr . "') ORDER BY route LIMIT 1";
                            $ris = mysqli_query($conn, $sql);
                            if(!$ris){
                                throw new Exception("select next 2 failed");
                            }
                            $rownum = mysqli_num_rows($ris);
                            if($rownum == 0){
                                $next = $arr; 
                            }else{
                                $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                                if($row ==""){
                                    $next = $arr;
                                }
                                $next = mysqli_real_escape_string($conn, $row[0]); 
                            }
                            mysqli_free_result($ris);
                            
                            if($previous != $next){
                                //select from itinerary all the routes that have source = previous and dest = next DONE
                                //select from booking all the routes that have source = previous and dest = next   DONE
                                $sql = "SELECT * FROM itinerary WHERE source='" . $previous . "' AND destination='" . $next . "'";
                                $ris1 = mysqli_query($conn, $sql);
                                if(!$ris1){
                                    throw new Exception("select from itinerary 2 failed");
                                }
                                $sql = "SELECT * FROM booking WHERE source = '" . $previous . "' AND destination = '" . $next . "'";
                                $ris2 = mysqli_query($conn, $sql);
                                if(!$ris2){
                                    throw new Exception("select from booking 2 failed");
                                }
                                //insert into itinerary two new routes, one with source = previous and dest = $arr 
                                //insert into itinerary another route with source = $arr and dest = next           
                                $rownum1 = mysqli_num_rows($ris1);
                                if($rownum1 == 0){
                                    $z = 0; 
                                    $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". $previous ."', '". $next ."', '".$z."')";
                                    $ris4 = mysqli_query($conn, $sql);
                                    if(!$ris4){
                                        throw new Exception("insert new into itinerary failed");
                                    }
                                }else{
                                    $row = mysqli_fetch_array($ris1, MYSQLI_ASSOC);
                                    while($row!=NULL){
                                        $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". mysqli_real_escape_string($conn, $row['source']) ."', '". $arr ."', '". $row['passengers'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 1 into itinerary failed");
                                        }
                                        $sql = "INSERT INTO itinerary (source, destination, passengers) VALUES ('". $arr ."', '". mysqli_real_escape_string($conn, $row['destination']) ."', '". $row['passengers'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 2 into itinerary failed");
                                        }
                                        $row = mysqli_fetch_array($ris1, MYSQLI_ASSOC);
                                    }
                                
                                    //insert into booking two new routes, one with source = previous and dest = $arr 
                                    //insert into booking another route with source = $arr and dest = next           
                                    $row = mysqli_fetch_array($ris2, MYSQLI_ASSOC);
                                    while($row!=NULL){
                                        $sql = "INSERT INTO booking (user, source, destination, numpeople) VALUES ('". $row['user'] ."', '". mysqli_real_escape_string($conn, $row['source']) ."', '". $arr ."', '". $row['numpeople'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 1 into booking 2 failed");
                                        }
                                        $sql = "INSERT INTO booking (user, source, destination, numpeople) VALUES ('". $row['user'] ."', '". $arr ."', '". mysqli_real_escape_string($conn, $row['destination']) ."', '". $row['numpeople'] ."')";
                                        $ris3 = mysqli_query($conn, $sql);
                                        if(!$ris3){
                                            throw new Exception("insert 2 into booking 2 failed");
                                        }
                                        $row = mysqli_fetch_array($ris2, MYSQLI_ASSOC);
                                    }
                                    //delete from itinerary all the routes that have source = previous and dest = next
                                    //delete from booking all the routes that have source = previous and dest = next
                                    $sql = "DELETE FROM itinerary WHERE source='" . $previous . "' AND destination='" . $next . "'";
                                    $ris1 = mysqli_query($conn, $sql);
                                    if(!$ris1){
                                        throw new Exception("delete from itinerary 2 failed");
                                    }
                                    $sql = "DELETE FROM booking WHERE source='" . $previous . "' AND destination='" . $next . "'";
                                    $ris1 = mysqli_query($conn, $sql);
                                    if(!$ris1){
                                        throw new Exception("delete from booking 2 failed");
                                    }
                                    //all reschedule done
                                }
                            }else{
                                //there are no routes 
                                $arr = $previous; 
                            }
                        }else{
							//route is already in the database
                            $arr = sanitizeString($_POST["arrival"]);
							if($arr =="disabled"){
                                throw new Exception("Select an arrival"); 
                            }
							$arr = strtoupper($arr);
                            
                        }
                        
                        //////////PASSENGER/////////////////////
                        $nump = $_POST["passengers"];
                        
                        
                        //////////BOOKING///////////////////////
                        //check if $dep < $arr
                        if(strnatcmp($dep,$arr)>=0){
                            throw new Exception("Arrival must be alphabetically greater than departure. Try again");
                        }
                        //update itinerary with num of passengers
                        //insert into booking the new reservation
                        $sql = "SELECT route FROM routes WHERE route>='" . $dep . "' AND route<='" . $arr . "' ORDER BY route";
                        $ris = mysqli_query($conn, $sql);
                        if(!$ris){
                            throw new Exception("select from route booking failed"); 
                        }
                        $row1 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                        $row2 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                        while($row2 != NULL){
                            //select number of passengers
                            $sql = "SELECT passengers FROM itinerary WHERE source='" . mysqli_real_escape_string($conn, $row1["route"]) . "' AND destination = '" . mysqli_real_escape_string($conn, $row2["route"]) . "'";
                            $ris1 = mysqli_query($conn, $sql);
                            if(!$ris1){
                                throw new Exception("select booking failed"); 
                            }
                            $rowp= mysqli_fetch_array($ris1, MYSQLI_NUM);
                            $newp = $rowp[0] + $nump;
                            if($newp > $buscapacity){
                                 throw new Exception("Update booking failed. There isn't enough space in the bus");
                            }
                            //update itinerary with num of passengers
                            $sql = "UPDATE itinerary SET passengers='". $newp . "' WHERE source='" . mysqli_real_escape_string($conn, $row1["route"]) . "' AND destination='" . mysqli_real_escape_string($conn, $row2["route"]) . "'";
                            $ris2 = mysqli_query($conn, $sql);
                            if(!$ris2){
                                throw new Exception("Update booking failed"); 
                            }
                            //check again 
                            $sql = "SELECT passengers FROM itinerary WHERE source='" . mysqli_real_escape_string($conn, $row1["route"]) . "' AND destination = '" . mysqli_real_escape_string($conn, $row2["route"]) . "'";
                            $ris1 = mysqli_query($conn, $sql);
                            if(!$ris1){
                                throw new Exception("select booking failed"); 
                            }
                            $rowp= mysqli_fetch_array($ris1, MYSQLI_NUM);
                            if($rowp[0] > $buscapacity){
                                 throw new Exception("Update booking failed 2. There isn't enough space in the bus");
                            }
                            //insert into booking the new reservation
                            $sql = "INSERT INTO booking (user, source, destination, numpeople) VALUES ('". $_SESSION["username"] ."', '". mysqli_real_escape_string($conn, $row1["route"]) ."', '". mysqli_real_escape_string($conn, $row2["route"]) ."', '". $nump ."')";
                            $ris3 = mysqli_query($conn, $sql);
                            if(!$ris3){
                                throw new Exception("insert into booking failed"); 
                            }
                            $row1 = $row2;
                            $row2 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                        }
                        //update member bookin, set booked to 1
                        $sql = "UPDATE members SET booked = '" . true . "', source = '" . $dep . "', destination = '" . $arr . "' WHERE user = '" . $_SESSION["username"] . "'";
                        $ris = mysqli_query($conn, $sql);
                        if(!$ris){
                            throw new Exception("Update members failed"); 
                        }
                        //check itinerary again for security
                        $sql = "SELECT passengers FROM itinerary WHERE passengers > '" . $buscapacity . "'";
                        $ris1 = mysqli_query($conn, $sql);
                        if(!$ris1){
                            throw new Exception("Last select from itinerary failed"); 
                        }
                        $rownum = mysqli_num_rows($ris1);
                        if($rownum!=0){
                            throw new Exception("More passengers than capacity");
                        }
                        
                        mysqli_commit($conn);
                        
                    }catch(Exception $e){
						//booking not complete, some errors
                        mysqli_rollback($conn);
                        ?><script type =text/javascript>alert("<?php echo("Error: " . $e->getMessage()); ?>")</script><?php
                        mysqli_autocommit($conn, true);
                        ?>
                        <script type=text/javascript>
                        window.location.href = "booking.php";
                        </script>
                        <?php
                    }
                    mysqli_autocommit($conn, true);
                    mysqli_close($conn);
                    ?><script type =text/javascript>alert("Your booking has complete. Enjoy your trip")</script><?php
                    ?><script type=text/javascript>
                    window.location.href = "userhome.php";
                    </script>
                    <noscript>
                        <p>Javascript disabled. Manually refresh page</p>
                        <a href="server.php">Go</a>
                    </noscript>
                    <?php
                    
                }
               
            }
            /////////////////////////////////// 
            //////// DELETE BOOKING ///////////
            ///////////////////////////////////
            else if(isset($_POST["delete"])){
                $connection = dbConnect();
                try{
                    mysqli_autocommit($connection, false);
                    
                    $user = $_SESSION["username"];
                    $ris = returnUserTrip($user);
                    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
                    $dep = mysqli_real_escape_string($connection,$row[0]);
                    $arr = mysqli_real_escape_string($connection,$row[1]);
                    $pass = getPassengerNumber($connection, $user);
                    
                    //lock the database
                    $sql = "SELECT * FROM routes FOR UPDATE";
                    if(!mysqli_query($connection, $sql)){
                            throw new Exception("Error for update 1");
                    }
                    $sql = "SELECT * FROM booking FOR UPDATE";
                    if(!mysqli_query($connection, $sql)){
                            throw new Exception("Error for update 2");
                    }
                    $sql = "SELECT * FROM itinerary FOR UPDATE";
                    if(!mysqli_query($connection, $sql)){
                            throw new Exception("Error for update 3");
                    }
                    $sql = "SELECT * FROM members FOR UPDATE";
                    if(!mysqli_query($connection, $sql)){
                            throw new Exception("Error for update 4");
                    }
        
                    //delete route from members and booking
                    if(!removeBooking($connection, $user)){
                        throw new Exception("Error while removing booking from user");
                    }
                    //update itinerary table
                    $sql = "SELECT route FROM routes WHERE route>='" . $dep . "' AND route<='" . $arr . "' ORDER BY route";
                    $ris = mysqli_query($connection, $sql);
                    if(!$ris){
                        throw new Exception("Select from route for deleting failed"); 
                    }
                    $row1 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                    $row2 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                    while($row2!=NULL){
                        $sql = "UPDATE itinerary SET passengers=passengers -'". $pass  . "' WHERE source='" . mysqli_real_escape_string($connection,$row1["route"]) . "' AND destination='" . mysqli_real_escape_string($connection,$row2["route"]) . "'";
                        $ris2 = mysqli_query($connection, $sql);
                        if(!$ris2){
                            throw new Exception("Update itinerary for deleting failed"); 
                        }
                        $row1=$row2;
                        $row2 = mysqli_fetch_array($ris, MYSQLI_ASSOC);
                    }
                    
                    //delete all routes if are starting route to zero 
                    $sql = "SELECT * FROM itinerary ORDER BY source ASC";
                    $ris = mysqli_query($connection, $sql);
                    if(!$ris){
                        throw new Exception("Select from itinerary for deleting failed"); 
                    }	
				
					$row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
					$continue=true; 
					while($row!=NULL&&$continue){
						if($row["passengers"]==0){
							$sql1 = "DELETE FROM itinerary WHERE source = '" . mysqli_real_escape_string($connection, $row["source"]) ."' AND destination = '" . mysqli_real_escape_string($connection, $row["destination"]) . "'";
							$res = mysqli_query($connection, $sql1);
							if(!$res){
								throw new Exception("error while deleting from itinerary1");
							}
							
							$sql1 = "DELETE FROM routes WHERE route = '" . mysqli_real_escape_string($connection,$row["source"]) . "'";
							//delete also from routes table
							$res = mysqli_query($connection, $sql1);
							if(!$res){
								throw new Exception("error while deleting from routes1");
							}
							
							$row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
						}else{
							$continue=false;
						}
					}
					mysqli_free_result($ris); 
						
					//delete all routes if are pending route to zero 
					$sql = "SELECT * FROM itinerary ORDER BY destination DESC";
					$ris = mysqli_query($connection, $sql);
					if(!$ris){
						throw new Exception("Select from itinerary desc for deleting failed"); 
					}
					$row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
					$continue = true;
					while($row!=NULL&&$continue){
						if($row["passengers"]==0){
							$sql1 = "DELETE FROM itinerary WHERE source = '" . mysqli_real_escape_string($connection, $row["source"]) ."' AND destination = '" . mysqli_real_escape_string($connection, $row["destination"]) . "'";
							$res = mysqli_query($connection, $sql1);
							if(!$res){
								throw new Exception("error while deleting from itinerary2");
							}
							//delete also from routes table
							$sql1 = "DELETE FROM routes WHERE route = '" . mysqli_real_escape_string($connection, $row["destination"]) . "'";
							$res = mysqli_query($connection, $sql1);
							if(!$res){
								throw new Exception("error while deleting from routes2");
							}
							$row = mysqli_fetch_array($ris, MYSQLI_ASSOC);
						}else{
							$continue=false;
						}
					}
				
                    //rebuilt route
                    rebuiltDatabase($connection, $dep, $arr);
                    mysqli_commit($connection);
                    
                }catch(Exception $e){
					//error while deleting 
                    mysqli_rollback($connection);
                    ?><script type =text/javascript>alert("<?php echo("Error: " . $e->getMessage()); ?>")</script><?php
                    mysqli_autocommit($connection, true);
					?><script type=text/javascript>
                    window.location.href = "removebooking.php";
                    </script>
					<?php
                }
                mysqli_autocommit($connection, true);
                mysqli_close($connection);
                ?><script type=text/javascript>
                window.location.href = "userhome.php";
                </script>
                <noscript>
                    <p>Javascript disabled. Manually refresh page</p>
                    <a href="server.php">Go</a>
                </noscript>
                <?php
    
            }

            /////////////////////////////////// 
            ////////EXIT THE SERVER////////////
            ///////////////////////////////////
            else{
                ?><script type=text/javascript>
                window.location.href="index.php";
                </script>
                <noscript>
                    <p>You have no rights to access this page. Tap the button to go back</p>
                    <a href="index.php">Go</a>
                </noscript>
                <?php
            }
             
            ?>
             
        </body>
    </html>

 