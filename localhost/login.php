<?php
   ob_start();
   session_start();
   if(isset($_SESSION['username']))
      echo "Logged in as: ".$_SESSION['username'];
   $ok_toLogin = FALSE;
?>

<?
   // error_reporting(E_ALL);
   // ini_set("display_errors", 1);
?>

<html lang = "en">
   
   <head>
   </head>
	
   <body>
      
      <h2>Enter Username and Password</h2> 
      <div class = "container form-signin">
         
         <?php
            $msg = '';
            
            if (isset($_POST['login']) && !empty($_POST['username']) 
               && !empty($_POST['password'])) {
				//check user/pass


                           $conn = oci_connect("admin", "parola1234", "//localhost:1521/xe");
                           if (!$conn) {
                              $m = oci_error();
                              echo $m['message'], "\n";
                              exit;
                           }
                           else {
                              print "Connected to Oracle!";
                           }

                           $array = ARRAY();
                           $stid = oci_parse($conn, "Select count(*) from USERS where u_id= :username and password= :password");  
                           echo $_POST['username'],$_POST['password'];
                           oci_bind_by_name($stid, ':username', $_POST['username']);
                           oci_bind_by_name($stid, ':password', $_POST['password']);
                           $ok_toLogin = oci_execute($stid);
                           echo $ok_toLogin;


               if ($ok_toLogin) {
                  $_SESSION['valid'] = true;
                  $_SESSION['timeout'] = time();
                  $_SESSION['username'] = $_POST['username'];
                  header("Location: /sgbd/autocomplete.php"); /* Redirect browser */
                  exit();
 
               }
            }
         ?>
      </div> <!-- /container -->
      
      <div class = "container">
      
         <form class = "form-signin" role = "form" 
            action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); 
            ?>" method = "post">
            <h4 class = "form-signin-heading"><?php echo $msg; ?></h4>
            <input type = "text" class = "form-control" 
               name = "username" placeholder = "1" 
               required autofocus></br>
            <input type = "password" class = "form-control"
               name = "password" placeholder = "xxx" required>
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" 
               name = "login">Login</button>
         </form>
			
         Click here to clean <a href = "logout.php" tite = "Logout">Session.
         
      </div> 
      
   </body>
</html>