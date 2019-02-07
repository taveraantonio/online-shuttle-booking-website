<?php
//Define bus capacity
$buscapacity = 4;

///////////////////////////////////
//////// SANITIZE STRING //////////
///////////////////////////////////
function sanitizeString($string)
{
	$conn = dbConnect();
	$string = strip_tags($string);						/*return a string with all NULL bytes, HTML and PHP tags stripped from the string*/
	$string = htmlentities($string);					/*all characters which have HTML character entity equivalents are translated into these entities.*/
	$string = stripslashes($string);					/*un-quotes a quoted string*/
	return mysqli_real_escape_string($conn, $string); 	/*create a legal SQL string that you can use in an SQL statement*/
}
?>


<?php
///////////////////////////////////
//////CONNECT TO DATABASE /////////
///////////////////////////////////
function dbConnect() {
    $conn = mysqli_connect("localhost", "root", "", "s243869");
	//$conn = mysqli_connect("localhost", "s243869", "", "s243869");
    if(!$conn){
        die('Errore nella connessione (' . mysqli_connect_errno() . '): ' . mysqli_connect_error());
    }
    if (!mysqli_set_charset($conn, "utf8")){
        die('Errore nel caricamento del set di caratteri utf8: ' . mysqli_error($conn));
    }
    return $conn;
}
?>


<?php
///////////////////////////////////
////// CHECK IF VALID USER ////////
///////////////////////////////////
function validUser($user, $password) {
    $conn = dbConnect();
    $sql = "SELECT password
			FROM members
			WHERE user = '". $user ."'";
    $ris = mysqli_query($conn, $sql);
    if(!$ris){
            die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	$rownum = mysqli_num_rows($ris);
	if($rownum==0){
		//no user with that username
		mysqli_free_result($ris);
		mysqli_close($conn);
		return false;
	}
    $row = mysqli_fetch_array($ris, MYSQLI_NUM);
    if($password == $row[0]){
		//user inserted correct password
		mysqli_free_result($ris);
		mysqli_close($conn); 
		return true;
	}else{
		//user has inserted not correct password 
		mysqli_free_result($ris);
		mysqli_close($conn); 
		return false;
	}
	
}
?>


<?php
///////////////////////////////////
//////// FILTER EMAIL /////////////
///////////////////////////////////
function checkUsername($username){
	if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
		return false; 
    }
	return true; 
}
?>


<?php
///////////////////////////////////
//////// FILTER PASSWORD //////////
///////////////////////////////////
function checkPassword($pass){
	if( preg_match( '~[a-z]~', $pass) &&
		(preg_match( '~[A-Z]~', $pass) || preg_match( '~\d~', $pass) ) &&
		(strlen($pass) > 1)){
		return true;
	} else {
		return false; 
	}
}
?>


<?php
/////////////////////////////////////
//CHECK IF USER ALREADY REGISTERED //
/////////////////////////////////////
function alreadyRegistered($conn, $user){
	
	$upper = strtoupper($user);
	$lower = strtolower($user);
	
	$sqlupper = "SELECT * FROM members WHERE user = '". $upper ."'";
	$sqllower = "SELECT * FROM members WHERE user = '". $lower ."'";
	$resupper = mysqli_query($conn, $sqlupper);
	$reslower = mysqli_query($conn, $sqllower);
	
	if(!$resupper || !$reslower){
        throw new Exception("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	$rownumupper = mysqli_num_rows($resupper);
	$rownumlower = mysqli_num_rows($reslower);
	if($rownumupper==0 && $rownumlower==0){
		//user is not in the database
		return false;
	}else{
		//user is in the database
		return true; 
	}
}
?>


<?php
///////////////////////////////////
//////// RETURN ITINERARY//////////
///////////////////////////////////
function returnItinerary(){
	$conn = dbConnect(); 
	$sql = "SELECT *
			FROM itinerary
			ORDER BY source";
	$ris = mysqli_query($conn, $sql);
	if(!$ris){
        die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	mysqli_close($conn);
	return $ris; 
}
?>


<?php
///////////////////////////////////
//////// RETURN ROUTES/////////////
///////////////////////////////////
function returnRoutes(){
	$conn = dbConnect(); 
	$sql = "SELECT *
			FROM routes
			ORDER BY route";
	$ris = mysqli_query($conn, $sql);
	if(!$ris){
            die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	mysqli_close($conn);
	return $ris; 
}
?>

<?php
/////////////////////////////////////
/// CHECK IF LOGIN SESSION > 2MIN ///
/////////////////////////////////////
function isLoginSessionExpired() {
	$login_session_duration = 120;
	$current_time = time(); 
	if(isset($_SESSION['logtime'])){
		if(((time() - $_SESSION['logtime']) > $login_session_duration)){
            unset($_SESSION['username']);
            unset($_SESSION['logtime']);
            return true;
		} 
	}
	return false;
}
?>


<?php
/////////////////////////////////////
///CHECK IF USER HAS A RESERVATION///
/////////////////////////////////////
function hasBooked($username){
	
	$conn = dbConnect();
	$sql = "SELECT booked
			FROM members
			WHERE user = '". $username ."'";
    $ris = mysqli_query($conn, $sql);
    if(!$ris){
            die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	$row = mysqli_fetch_array($ris, MYSQLI_NUM);
	if($row[0]==0){
		//user has not booked
		mysqli_free_result($ris);
		mysqli_close($conn);
		return false;
	}else{
		//user has booked
		mysqli_free_result($ris);
		mysqli_close($conn);
		return true; 
	}
}
?>


<?php
///////////////////////////////////
///RETURN ITINERARY WITH PEOPLE ///
///////////////////////////////////
function returnItineraryPeople(){
	$conn = dbConnect(); 
	$sql1 = "SELECT booking.user, itinerary.source, itinerary.destination, itinerary.passengers , booking.numpeople
			FROM itinerary 
			LEFT JOIN booking
			ON booking.source = itinerary.source AND booking.destination = itinerary.destination
			ORDER BY itinerary.source
	";
	$ris = mysqli_query($conn, $sql1);
	if(!$ris){
            die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	mysqli_close($conn);
	return $ris; 
}
?>


<?php
/////////////////////////////////
///// RETURN TRIP OF USER ///////
/////////////////////////////////
function returnUserTrip($user){
	$conn = dbConnect(); 
	$sql = "SELECT source, destination
			FROM members
			WHERE user = '". $user ."'";
	$ris = mysqli_query($conn, $sql);
	if(!$ris){
            die("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	mysqli_close($conn);
	return $ris; 
}
?>


<?php
////////////////////////////////////
///CHECK IF A ROUTE ALREADY EXIST //
////////////////////////////////////
function isInDatabase($conn, $str){

	$sql = "SELECT route FROM routes";
	$res = mysqli_query($conn, $sql);
	if(!$res){
        throw new Exception("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	$row = mysqli_fetch_array($res);
	while($row!=NULL){
		if($str==mysqli_real_escape_string($conn, $row[0])){
			mysqli_free_result($res); 
			return true; 
		}
		$row = mysqli_fetch_array($res);
	}
	mysqli_free_result($res); 
	return false;
	
}
?>


<?php
////////////////////////////////////////////
///GET # OF BOOKED PASSENGER FOR THE USER //
////////////////////////////////////////////
function getPassengerNumber($conn, $user){
	
	$sql = "SELECT numpeople
			FROM booking
			WHERE user = '". $user ."'
			LIMIT 1";
	$res = mysqli_query($conn, $sql);
	if(!$res){
        throw new Exception("Query non riuscita (" . mysqli_errno($conn) . "): " . mysqli_error($conn));
    }
	$row = mysqli_fetch_array($res, MYSQLI_NUM);
	mysqli_free_result($res); 
	return $row[0];

}
?>


<?php
////////////////////////////////////
/////REMOVE BOOKING OF A USER///////
///FROM BOOKING AND MEMBERS TABLE///
////////////////////////////////////
function removeBooking($conn, $user){
	
	$sql = "DELETE 
			FROM booking
			WHERE user = '". $user ."'";
	$res = mysqli_query($conn, $sql);
	if(!$res){
        return false; 
    }
	
	$sql = "UPDATE members  
			SET booked = 0, source = '', destination = ''
			WHERE user = '". $user ."'";
	
	$res = mysqli_query($conn, $sql);
	if(!$res){
		mysqli_free_result($res); 
        return false;
	}
	mysqli_free_result($res); 
	return true;	
}
?>


<?php
////////////////////////////////////
////////DELETE ROUTES TABLE/////////
////////////////////////////////////
function deleteRoutes($conn){
	//this function is called when its remained only one route in the db
	$sql = "DELETE 
			FROM routes
			WHERE 1";
	$res = mysqli_query($conn, $sql);
	if(!$res){
        throw new Exception("Rebuilt of routes 1 failed"); 
    }
	mysqli_free_result($res); 
}
?>


<?php
///////////////////////////////////////
//////////REBUILT ITINERARY////////////
///////////////////////////////////////
function rebuiltDatabase($conn, $dep, $arr){
	
	$sql = "SELECT source
			FROM members
			WHERE source<>'"."'
			ORDER BY source";
	$res = mysqli_query($conn, $sql);
	if(!$res){
		throw new Exception("select from memebers for rebuilt failed"); 
    }
	$rownum = mysqli_num_rows($res);
	if($rownum==0){
		//no booking, delete the remaining route 
		deleteRoutes($conn);
		
	}else{
		//select from members table all the route
		//select from routes table all the route
		//do the difference and we obtain the route to be deleted
		//update booking and itinerary by deleting the no more useful route 
		$row=mysqli_fetch_array($res, MYSQLI_NUM);
		$string1 ="";
		while($row!=NULL){
			$string1 = $string1 . " " . mysqli_real_escape_string($conn, $row[0]);
			$row=mysqli_fetch_array($res, MYSQLI_NUM);
		}
		
		$sql = "SELECT destination
				FROM members
				WHERE destination<>'"."'
				ORDER BY destination";
		$res = mysqli_query($conn, $sql);
		if(!$res){
			throw new Exception("Rebuilt of routes 2 failed"); 
		}
		$row=mysqli_fetch_array($res, MYSQLI_NUM);
		$string2 ="";
		while($row!=NULL){
			$string2 = $string2 . " " . mysqli_real_escape_string($conn, $row[0]);
			$row=mysqli_fetch_array($res, MYSQLI_NUM);
		}
		$array1 = explode(" ", $string1);
		$array2 = explode(" ", $string2);
		$array = array_merge($array1, $array2);
		//inside array i have all the routes taken from members table 
		$array = array_unique($array, SORT_REGULAR);
		
		
		$sql = "SELECT route
				FROM routes
				ORDER BY route";
		$res = mysqli_query($conn, $sql);
		if(!$res){
			throw new Exception("Rebuilt of routes 2 failed"); 
		}
		$row=mysqli_fetch_array($res, MYSQLI_NUM);
		$string ="";
		while($row!=NULL){
			$string = $string . " " . mysqli_real_escape_string($conn,$row[0]);
			$row=mysqli_fetch_array($res, MYSQLI_NUM);
		}
		//inside array1 i have all the route taken from routes table
		$array1 = explode(" ", $string);
	
		//do the diff
		$diff = array_diff($array1, $array);
		if(count($diff)>0){
			//if there are a no mor euseful route do this 
			foreach ($diff as $value) {
				//take the station that is before and after the no more useful route ($value) and do substitution into itinerary and booking table
				$key = array_search($value, $array1);
				$previouskey = intval($key)-1;
				$nextkey = intval($key)+1;
				$previous = $array1[$previouskey];
				$next =  $array1[$nextkey];
				$value = $value;
				
				//search all the booking that have $value as destination and substitute this with $next
				//search all the booking that have $value as source substitute this with $previous 
				$sql = "UPDATE booking  
				SET destination = '" . $next . "'
				WHERE destination = '". $value ."'";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed first update booking rebuilting database");
				}
				$sql = "UPDATE booking  
				SET source = '" . $previous . "'
				WHERE source = '". $value ."'";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed second update booking rebuilting database");
				}
				$sql = "UPDATE itinerary  
				SET source = '" . $previous . "'
				WHERE source = '". $value ."'";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed first update itinerary rebuilting database");
				}
				$sql = "UPDATE itinerary  
				SET destination = '" . $next . "'
				WHERE destination = '". $value ."'";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed second update itinerary rebuilting database");
				}
				
				//delete diff from routes
				$sql = "DELETE 
				FROM routes
				WHERE route = '". $value ."'";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed delete from routes"); 
				}
				
				//delete duplicate booking
				$sql = "SELECT COUNT(*)
						FROM booking
						GROUP BY user, source, destination
						HAVING COUNT(*)>1";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed deleting duplicates 1 from booking"); 
				}
				$row=mysqli_fetch_array($res, MYSQLI_NUM);
				$limit = intval($row[0]);
				$limit = $limit - 1;
				$sql = "SELECT user
						FROM booking
						GROUP BY user, source, destination
						HAVING COUNT(*)>1";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed deleting duplicates 1 from booking"); 
				}
				$row=mysqli_fetch_array($res, MYSQLI_NUM);
				while($row!=NULL){
					$sql = "DELETE 
							FROM booking
							WHERE source = '" . $previous . "'
							AND destination = '" . $next . "'
							AND user = '" . $row[0] . "'
							LIMIT " . $limit . "";
					
					$res1 = mysqli_query($conn, $sql);
					if(!$res1){
						throw new Exception("Failed deleting duplicates 2 from booking"); 
					}
					$row=mysqli_fetch_array($res, MYSQLI_NUM);
				}
				
				//deleting duplicates from itinerary
				$sql = "DELETE 
						FROM itinerary
						WHERE source = '" . $previous . "'
						AND destination = '" . $next . "'
						LIMIT 1";
				$res = mysqli_query($conn, $sql);
				if(!$res){
					throw new Exception("Failed deleting duplicates 2 from itinerary"); 
				}	
			}
		}	
	}
}

?>
