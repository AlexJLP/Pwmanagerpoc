    <?php
        ini_set('display_errors', 'On');
        include 'include/functions.php';

        // Get post data and excape
       $username = filter($_POST["username"]);
       $password = filter($_POST["password"]);
?>
<html>
<body>
<?php signup($username, $password); ?>
<button onclick="window.location.href = 'index.php';">Return to 
login</button>

</body>
</html> 
