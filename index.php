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
    ?>
    <div class="jumbotron">
      <h1>Super Secure Credential Manager</h1>
    </div>

    <div class="container">
	    <?php
      print '<form name="login" class="form-horizontal" 
action="do-login.php" 
method="post">';
      print '  <fieldset>';
      print '    <legend>User Login</legend>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="username">User name</label>';
      print '      <div class="controls">';
      print '        <input id="username" name="username" type="text" placeholder="username" class="input-medium" required="">';
      print '      </div>';
      print '    </div>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="password">Password</label>';
      print '      <div class="controls">';
      print '        <input id="password" type="password" placeholder="password" class="input-medium" required="" onchange="salt_and_hash()">';
      print '      </div>';
      print '    </div>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="createaccount"></label>';
      print '      <div class="controls">';
      print '        <button id="login" name="login" type="submit" value="login" class="btn btn-default">Log in</button>';
      print '      </div>';
      print '    </div>';
      print '        <input id="password_hashed" name="password" class="input-medium" required="">';
      print '  </fieldset>';
      print '</form>';
      ?>
      <p> No account yet? </p>       <button onclick="window.location.href = 
'signup.php';">Click Here to create a new account</button>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
      function hex(t){for(var e=[],n=new DataView(t),i=0;i<n.byteLength;i+=4){var r="00000000",a=(r+n.getUint32(i).toString(16)).slice(-r.length);e.push(a)}return e.join("")}

     function salt_and_hash() {
      var data = document.getElementById("username").value + document.getElementById("password").value ;
      var buffer = new TextEncoder("utf-8").encode(data);
       return crypto.subtle.digest("SHA-512", buffer).then(
         function (hash) {
           document.getElementById("password_hashed").value = hex(hash)
         }
       );
     }
    </script>
  </body>
</html>

