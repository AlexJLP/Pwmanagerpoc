<html lang="en">
          <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Super Secure Credential Manager</title>

          <!-- Bootstrap -->
          <link href="css/bootstrap.min.css" rel="stylesheet">

          <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
          <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php
      ini_set('display_errors', 'On');
      include 'include/functions.php';
      include 'include/gated.php';
    ?>
    <div class="jumbotron">
       <h1>SSCM User Area</h1>
    </div>

    <div class="container">
    <h2>Storage<h2>

    <ul class="list-group">
<?php

        if (isset($_COOKIE['username']))
        {
         $username = $_SESSION['uname'];
         //echo $username;
        $results = get_content($username);
        foreach($results as $result) {
        echo '<li class="list-group-item"><a href="displayentry.php?cid=', $result[0], '">' , $result[2];
        echo ' </a><span class="badge badge-primary badge-pill">', $result[1], '</span></li>';
    }
    }
?>

  <li class="list-group-item"><a href="add-entry.php">+ Add new item (manual entry)</a></li>
  <li class="list-group-item"><a href="generate-entry.php">+ Add new item (generate)</a></li>
</ul>
    </div>






<div style="position:absolute;top:10;right:10;">
    <button onclick="window.location.href = 'logout.php';" class="btn btn-secondary">LOGOUT</button>
</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
                   <!-- Include all compiled plugins (below), or include individual files as needed -->
                   <script src="js/bootstrap.min.js"></script>
                   </body>
                   </html>

