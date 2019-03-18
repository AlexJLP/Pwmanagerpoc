    <?php
        ini_set('display_errors', 'On');
        include 'include/functions.php';

        // Get post data and excape
        if (isset($_COOKIE['username'], $_COOKIE['session']))
        {
             if (check_token($_COOKIE['username'], $_COOKIE['session']))
             {$username = $_COOKIE['username']; }
        else {$username = "NULL";}
        }
        $id= filter($_GET["id"]);

?>
<html>
<body>
<?php
	$logged_in = check_signed_in();
	if($logged_in === true) {
    del_content($username, $id);
	} else {
		echo "Error deleting.";
		echo "<br><a href ='index.php'>Return to login</a>";
	}
?>

<button onclick="window.location.href = 'upanel.php';" class="btn btn-primary">Back to User Panel</button>

</body>
</html> 
