<?php
	//check if cookie are active
    if(isset($_COOKIE['foo']) && $_COOKIE['foo']=='bar'){
        session_start();
        //cookies are active
        if(isset($_COOKIE['page'])){
			//back to the calling page if it is set
            setcookie("checked", true, time()+3600); 
            header("location:".$_COOKIE['page']); 
        }else{
			//otherwise back to homepage
            setcookie("checked", true, time()+3600); 
            header("location:index.php"); 
        } 
    }else{
        //cookie are not set. block navigation
        echo("<div style='padding: 15px; background-color: #DB0007; color: white;'>
                <b>Danger!</b> Cookie are not enabled. Navigation through the website is forbidden. Please enable them from the setting page of your broswer and click to
                <a href='check.php' style='color:white;'><b>REFRESH</b></a> the page.
            </div>");
        setcookie('foo', 'bar', time()+3600); 
    }
    
?>


