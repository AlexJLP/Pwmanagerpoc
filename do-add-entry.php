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

        $title= filter($_POST["title"]);
        $content = filter($_POST["content"]);
        $type = filter($_POST["contenttype"]);
        $ctr = filter($_POST["ctr"]);

?>
<html>
<body>
<?php
	$logged_in = check_signed_in();
	if($logged_in === true) {
        echo $username;
	echo $content;
    add_content($username, $title, $content, $type, $ctr);
	} else {
		echo "Error logging in.";
		echo "<br><a href ='index.php'>Return to login</a>";
	}
?>

<button onclick="window.location.href = 'upanel.php';" class="btn btn-primary">Back to User Panel</button>

</body>
</html> 
