<?php
ini_set('display_errors', 'On');
include 'include/functions.php';

// Get post data and excape

$title= filter($_POST["title"]);
$content = filter($_POST["content"]);
$content2 = filter($_POST["content2"]);
$type = filter($_POST["contenttype"]);
$ctr = filter($_POST["ctr"]);

?>
<html>
    <body>
        <?php
	      $logged_in = check_signed_in();
        if (isset($_COOKIE['username']))
        {
            $username = $_SESSION['uname'];
        }
	      if($logged_in === true) {
            echo $username;
	          echo $content;
            add_content($username, $title, $content, $type, $ctr);
            if($content2 != '') {
                add_content($username, $title, $content2, "Private Key", $ctr);
            }
	      } else {
		        echo "Error logging in.";
		        echo "<br><a href ='index.php'>Return to login</a>";
	      }
        ?>

        <button onclick="window.location.href = 'upanel.php';" class="btn btn-primary">Back to User Panel</button>

    </body>
</html> 
