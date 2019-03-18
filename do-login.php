    <?php
        ini_set('display_errors', 'On');
        include 'include/functions.php';

        // Get post data and excape
       $username = filter($_POST["username"]);
       $password = filter($_POST["password"]);
?>
<html>
<body>
<?php
	$logged_in = login($username, $password); 
	//$logged_in = check_signed_in();
	if($logged_in === true) {
		header("Location: upanel.php");
		echo ' Logged in. Click here if you are not redirected.';
	} else {
		echo "Error logging in.";
		echo "<br><a href ='index.php'>Return to login</a>";
	}
?>

</body>
</html> 
