<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Secure Credential Manager</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/localcookie.js"></script>
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
      <h2>Add new Entry<h2>
        <?php
        if (isset($_COOKIE['username']))
        {
            $username = $_SESSION['uname'];
        }
        ?>
        <form name="addcont" cla/upanel.phpss="form-horizontal" action="do-add-entry.php" method="post">
          <fieldset>
            <div class="form-group">
	            <label for="contenttype">State</label>
              <label for="content">Entry Title</label>
              <input class="form-control" id="title" name="title" required=""> </br>

	            <select id="contenttype" name="contenttype" class="form-control">
	              <option selected>Password</option>
	              <option>Public Key</option>
	              <option>Private Key</option>
	              <option>Symmetric Key</option>
	            </select> <br>
              <label for="content">Content</label>
              <textarea class="form-control" id="content" rows="3" onchange="encrypt();"></textarea> </br>
              <input id="encrypted" name="content" style="display:none;">
              <input id="encrypted_ctr" name="ctr" style="display:none;">
              <div class="controls">
                <button id="submit" name="submit" type="submit" value="submit" class="btn btn-primary">Submit</button>
              </div>

            </div>
          </fieldset>
        </form>
    </div>

    <textarea id="key" rows="4" style="display:none;"></textarea> <br>
    <a href="#" onclick="encrypt();" style="display:none;">encrypt</a> </br>

    <textarea id="decrypted" rows="4" style="display:none;"></textarea>     <a href="#" onclick="decrypt();" style="display:none;">decrypt</a> </br>







    <div style="position:absolute;top:0;right:0;">      
      <button onclick="window.location.href = 
'logout.php';">LOGOUT</button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="keymodal" tabindex="-1" role="dialog" aria-labelledby="keyModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="keymodalLabel">Decryption Key</h5>
          </div>
          <div class="modal-body">
            Your decryption key is not set for the current session. You need to set it every time you log in. This key is used to encrypt your entries before they are sent to the server. <br> DO NOT LOSE THIS PASSPHRASE! You can not restore your entries without it!</br>
          </div>
          <div class="modal-footer">
            <input id="passphrase" type="password" placeholder="passphrase" class="input-medium" required="">
            <button type="button" class="btn btn-primary" onclick="setC()">Set</button>
          </div>
        </div>
      </div>
    </div>
    <!-- jQuery (necessary for ootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
     var offline_key = getCookie('sscm_offline_key');
     if (!offline_key) {
       $('#keymodal').modal('show');
     } else {
       // Cookie exists => import key from it.
      document.getElementById("key").value = offline_key;
      //impEncKey(offline_key);
     }

     // If we have a key in cookie:
     // Import!
     async function impEncKey(key) {
       var key_json = JSON.parse('{"alg":"A256CTR","ext":true,"k":"'+key+'","key_ops":["encrypt","decrypt"],"kty":"oct"}');
       var offline_key_obj = await crypto.subtle.importKey('jwk', key_json, { name: 'AES-CTR', length: 256 }, true, ['encrypt','decrypt']);
       return offline_key_obj;
      }
     // If we have no key in cookie yet:
     // Generate key from password
     // From https://webbjocke.com/javascript-web-encryption-and-hashing-with-the-crypto-api/
     async function genEncryptionKey (password, mode, length) {
       var algo = {
         name: 'PBKDF2',
         hash: 'SHA-256',
         salt: new TextEncoder().encode('decrkey'),
         iterations: 10
       };
       var derived = { name: mode, length: length };
       var encoded = new TextEncoder().encode(password);
       var key = await crypto.subtle.importKey('raw', encoded, { name: 'PBKDF2' }, true, ['deriveKey']);
       crypto.subtle.deriveKey(algo, key, derived, true, ['encrypt', 'decrypt'])
             .then(function(dkey){
               window.crypto.subtle.exportKey("jwk", dkey)
                     .then(function(keydata){
                       setCookie('sscm_offline_key',keydata['k'], 1);
                       $('#keymodal').modal('hide');
                       console.error(keydata);
                       console.error(JSON.stringify(keydata));
                       location.reload(); 
                     })
                     .catch(function(err){
                       console.error(err);
                     });
             });
     }

     function setC(){ // Set cookie for offline key storage
       var passphrase = document.getElementById("passphrase").value;
       genEncryptionKey(passphrase, 'AES-CTR', 256);
     }

      function buf2hex(r){return Array.prototype.map.call(new Uint8Array(r),r=>("00"+r.toString(16)).slice(-2)).join("")}
     function hex2buf(e){const n=[];for(let r=0;r<e.length;r+=2)n.push(Number.parseInt(e.slice(r,r+2),16));return new Uint8Array(n)}

     async function encrypt() {
       const privKey = await impEncKey(offline_key);
       console.error(privKey);
       var plaintext = document.getElementById("content").value;
       var ctrarray = new Uint8Array(16);
       window.crypto.getRandomValues(ctrarray);
       console.error(ctrarray);
       var ciphertext = await crypto.subtle.encrypt(
           {name: "AES-CTR", counter: ctrarray, length: 128},
           privKey,
           new TextEncoder("utf-8").encode(plaintext));
       console.error(ciphertext);
       document.getElementById("encrypted").value = buf2hex(ciphertext);
       document.getElementById("encrypted_ctr").value = buf2hex(ctrarray);
     }

     async function decrypt() {
       const privKey = await impEncKey(offline_key);
       console.error(privKey);
       var ciphertext = document.getElementById("encrypted").value;
       //convert to buffer
       var s = document.getElementById("encrypted_ctr").value;
       //var temp = [];
       //for(var i = 0; i < s.length; i+=2) {
       //  temp.push(parseInt(s.substring(i, i + 2), 16));
       //}
       var ctrarray = Uint8Array.from(hex2buf(s));
       console.error(ctrarray);
       var plaintext = await crypto.subtle.decrypt(
         {name: "AES-CTR", counter: ctrarray, length: 128},
         privKey,
         hex2buf(ciphertext));
       console.error(plaintext);
       document.getElementById("decrypted").value = new TextDecoder("utf-8").decode(plaintext);
     }
    


    </script>


  </body>
</html>

