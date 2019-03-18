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

    $logged_in = check_signed_in();
    if (!$logged_in) {
	    header( 'Location: index.php' ) ;
    }
    ?>
    <div class="jumbotron">
      <h1>SSCM User Area</h1>
    </div>

    <div class="container">
      <h2>Generate new Entry</h2>
      <?php
      if (isset($_COOKIE['username'], $_COOKIE['session']))
      {
        if (check_token($_COOKIE['username'], $_COOKIE['session']))
        {$username = $_COOKIE['username']; }
        else {$username = "NULL";}
      }
      ?> <!--  '-->
      <div class="panel-group" id="accordion">
        <div class="panel panel-default" id="panel1">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-target="#collapseOne" href="#collapseOne">
                Password
              </a>
            </h4>

          </div>
          <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
              <script>
                 function pw_generate(){
                 // Adapted from https://codepen.io/EightArmsHQ/pen/MwwLmL
                 var charset = document.getElementById("pw_chars").value;
                 var password = '';
                 var password_length = parseInt(document.getElementById("pw_length").value);
                 for(var i = 0; i < password_length; i ++){
                   var random_position = Math.floor(Math.random() * charset.length);
                   password += charset[random_position];
                 }
                 if(password.length == password_length){
                   password = password.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                   document.getElementById("pw_out").value = password;
                   document.getElementById("pw_out_e").value = password;

                 }else{
                   //Error?
                   console.log(password.length , password_length, password);
                 }
                return false; // to not submit form
               };
              </script>

              <form name="addcont" class="form-horizontal" action="do-add-entry.php" method="post">
                <label for="">Password Length:</label>
                <select id="pw_length" class="form-control">
                  <option selected>8</option>
                  <option>9</option>
                  <option>10</option>
                  <option>11</option>
                  <option>12</option>
                  <option>13</option>
                  <option>14</option>
                  <option>15</option>
                  <option>16</option>
                  <option>17</option>
                  <option>18</option>
                  <option>19</option>
                  <option>20</option>
                </select><br> 
                <label for="">Characters:</label>
                <input type="text" value='12345677890!@£$%^&*()-=_+qwertyuiopasdfghjklzxcvbnmQWERTYUIOPSDFGHJKLZXCVBNM' id="pw_chars">
                <br>
                <input class="form-control" type="text" placeholder="Please click generate below..." readonly id="pw_out"><br>

                <input class="form-control" type="hidden" id="pw_out_e" name="content" value="test">
	              <input class="form-control" type="hidden" id="contenttype" name="contenttype" value="Password">

                <br>

                <span id="pw_gen" name="pw_gen" type="pw_gen" class="btn btn-success" onclick="pw_generate();" type="button"> 1 Generate</span> <br><br>
                <label for="content">Give your new entry a title:</label>
                <input class="form-control" id="title" name="title"> </br>
                <button id="pw_save" name="pw_save" type="pw_save" class="btn btn-success" type="submit"> 2 Save</button>
              </form>
            </div>
          </div>
        </div>
        <div class="panel panel-default" id="panel2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-target="#collapseTwo" href="#collapseTwo" class="collapsed">
                Public - Private Key Pair
              </a>
            </h4>

          </div>
          <div id="collapseTwo" class="panel-collapse collapse">
            <div class="panel-body">

              <script> // converting functions from https://riptutorial.com/javascript/example/17845/generating-rsa-key-pair-and-converting-to-pem-format
               function arrayBufferToBase64(arrayBuffer) {
                 var byteArray = new Uint8Array(arrayBuffer);
                 var byteString = '';
                 for(var i=0; i < byteArray.byteLength; i++) {
                   byteString += String.fromCharCode(byteArray[i]);
                 }
                 var b64 = window.btoa(byteString);

                 return b64;
               }

               function addNewLines(str) {
                 var finalString = '';
                 while(str.length > 0) {
                   finalString += str.substring(0, 64) + '\n';
                   str = str.substring(64);
                 }

                 return finalString;
               }

               function toPem(privateKey) {
                 var b64 = addNewLines(arrayBufferToBase64(privateKey));
                 var pem = "-----BEGIN PRIVATE KEY-----\n" + b64 + "-----END PRIVATE KEY-----";
                 return pem;
               }

               function toPemP(publicKey) {
                 var b64 = addNewLines(arrayBufferToBase64(publicKey));
                 var pem = "-----BEGIN PUBLIC KEY-----\n" + b64 + "-----END PUBLIC KEY-----";
                 return pem;
               }


               function a_generate() {
                 var Bits = document.getElementById("a_key").value;
                 // Use browser function to generate keyPair
                 window.crypto.subtle.generateKey(
                   {
                     name: "RSA-OAEP",
                     modulusLength: Bits,
                     publicExponent: new Uint8Array([0x01, 0x00, 0x01]),
                     hash: {name: "SHA-256"} 
                   },
                   true,
                   ["encrypt", "decrypt"]
                 ).then(function(keyPair) {
                   window.crypto.subtle.exportKey(
                     "pkcs8",
                     keyPair.privateKey
                   ).then(function(exportedPrivateKey) {
                     var pem = toPem(exportedPrivateKey);
                     document.getElementById("sk").value = pem;
                   }).catch(function(err) {
                     document.getElementById("sk").value = "Error!";
                   });
                   window.crypto.subtle.exportKey(
                     "spki",
                     keyPair.publicKey
                   ).then(function(exportedPublicKey) {
                     var pem = toPemP(exportedPublicKey);
                     document.getElementById("pk").value = pem;
                   }).catch(function(err) {
                     document.getElementById("pk").value = err;
                   });
                 });
               }
              </script>

              <p> Key Length: <p>       <select id="a_key" class="form-control">
                <option selected>1024</option>
                <option>2048</option>
                <option>4096</option>
              </select> <br>
              <p> Private Key : </p>
              <textarea class="form-control" type="text" placeholder="Please click generate below..." readonly id="sk">
              </textarea> <br>
              <p> Public Key : </p>
              <textarea class="form-control" type="text" placeholder="Please click generate below..." readonly id="pk"></textarea> <br>
              <button id="a_gen" name="a_gen" type="a_gen" class="btn btn-success" onclick="a_generate();"> 1 Generate</button>
              <button id="a_save" name="a_save" type="a_save" class="btn btn-success"> 2 Save</button>
            </div>
          </div>
        </div>
        <div class="panel panel-default" id="panel3">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-target="#collapseThree" href="#collapseThree" class="collapsed">
                Symmetric Key
              </a>
            </h4>

          </div>
          <div id="collapseThree" class="panel-collapse collapse">
            <div class="panel-body">
              <script> 
               function s_generate() {
                 var Bits = document.getElementById("s_key").value;
                 window.crypto.subtle.generateKey({name: "AES-CTR", length: Bits}, true,  ["encrypt", "decrypt"] )
                       .then(function(key){
                         window.crypto.subtle.exportKey("jwk", key)
                               .then(function(keydata){

                                 console.error(keydata);
                                 document.getElementById("aes_key").value = keydata['k'];
                               })
                               .catch(function(err){
                                 console.error(err);
                               });
                       });
               }

              </script>

              <p> Key Length: <p>       <select id="s_key" class="form-control">
                <option selected>128</option>
                <option>192</option>
                <option>256</option>
              </select> <br>
              <p> Key : </p>
              <input class="form-control" type="text" placeholder="Please click generate below..." readonly id="aes_key"><br>
              <button id="s_gen" name="s_gen" type="s_gen" class="btn btn-success" onclick="s_generate();"> 1 Generate</button>
              <button id="s_save" name="s_save" type="s_save" class="btn btn-success"> 2 Save</button>
            </div>
          </div>
        </div>
      </div>









      <form name="addcont" class="form-horizontal" action="do-add-entry.php"
            method="post">
        <fieldset>
          <div class="form-group">
	          <label for="contenttype">State</label>
            <label for="content">Entry Title</label>
            <input class="form-control" id="title" name="title"> </br>

	          <select id="contenttype" name="contenttype" class="form-control">
	            <option selected>Password</option>
	            <option>Public Key</option>
	            <option>Private Key</option>
	            <option>Symmetric Key</option>
	          </select> <br>
            <label for="content">Content</label>
            <textarea class="form-control" id="content" name="content" rows="3"></textarea> </br>
            <div class="controls">
              <button id="submit" name="submit" type="submit" value="submit" class="btn btn-primary">Submit</button>
              <button id="generate" name="generate" class="btn btn-success">Generate New</button>
            </div>

          </div>
        </fieldset>
      </form>
    </div>






    <div style="position:absolute;top:0;right:0;">      
      <button onclick="window.location.href = 
'logout.php';">LOGOUT</button>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/jsbn.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/random.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/hash.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/rsa.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/aes.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/api.js"></script>


  </body>
</html>

