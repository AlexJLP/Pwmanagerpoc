<?php
    ini_set('display_errors', 'On');
    include 'include/functions.php';
    include 'include/gated.php';
        // Get post data and excape
    if (isset($_COOKIE['username']))
    {
      $username = $_SESSION['uname'];
    }
    $id= filter($_GET["id"]);

?>
<html>
<body>
<?php
    del_content($username, $id);
?>

<button onclick="window.location.href = 'upanel.php';" class="btn btn-primary">Back to User Panel</button>

</body>
</html> 
