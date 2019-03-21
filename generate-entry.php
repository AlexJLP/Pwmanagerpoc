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
      <h2>Generate new Entry</h2>
      <?php
      if (isset($_COOKIE['username']))
      {
      $username = $_SESSION['uname'];
      }
      ?>

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
                   en_pw();

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

                <br>

                <span id="pw_gen" name="pw_gen" type="pw_gen" class="btn btn-success" onclick="pw_generate();" type="button"> 1 Generate</span>
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
                     document.getElementById("content2").value = document.getElementById("sk").value;
                   }).catch(function(err) {
                     document.getElementById("sk").value = "Error!";
                   });
                   window.crypto.subtle.exportKey(
                     "spki",
                     keyPair.publicKey
                   ).then(function(exportedPublicKey) {
                     var pem = toPemP(exportedPublicKey);
                     document.getElementById("pk").value = pem;
                     document.getElementById("content").value = document.getElementById("pk").value;
                     document.getElementById("contenttype").value = "Public Key";
                     encrypt();

                   }).catch(function(err) {
                     document.getElementById("pk").value = err;
                   });
                 });
                 
                 

                 en_kp();
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
                                 en_sk();
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
            </div>
          </div>
        </div>
      </div>
        <form name="addcont" cla/upanel.phpss="form-horizontal" action="do-add-entry.php" method="post">
          <fieldset>
            <div class="form-group">
          <label for="content">2 Enter a title for your new entry:</label>
              <input class="form-control" id="title" name="title" required=""> </br>

          <select id="contenttype" name="contenttype" class="form-control" style="display:none;" >
	              <option selected>Password</option>
	              <option>Public Key</option>
	              <option>Private Key</option>
	              <option>Symmetric Key</option>
	            </select> <br>
          <label for="content" style="display:none;">Content</label>
          <textarea class="form-control" id="content" rows="3" onchange="encrypt();"></textarea>
          <textarea class="form-control" id="content2" rows="3" onchange="encrypt();"></textarea>

          <textarea id="key" rows="4" style="display:none;"></textarea>
          <input id="encrypted" name="content" >
          <input id="encrypted_2" name="content2" >
          <input id="encrypted_ctr" name="ctr" >
              <div class="controls">
                <button id="submit" name="submit" type="submit" value="submit" class="btn btn-primary">3 Submit</button>
              </div>

            </div>
          </fieldset>
        </form>

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

    <div style="position:absolute;top:0;right:0;">      
      <button onclick="window.location.href = 
'logout.php';">LOGOUT</button>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
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

       var plaintext2 = document.getElementById("content2").value;
       if (content2 != '') {
         var ciphertext2 = await crypto.subtle.encrypt(
           {name: "AES-CTR", counter: ctrarray, length: 128},
           privKey,
           new TextEncoder("utf-8").encode(plaintext2));
         document.getElementById("encrypted_2").value = buf2hex(ciphertext2);

       }

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
    
     function en_pw() {
       document.getElementById("content").value = document.getElementById("pw_out").value;
       document.getElementById("contenttype").value = "Password";
       encrypt();
     }

    
      function en_sk() {
        document.getElementById("content").value = document.getElementById("aes_key").value;
        document.getElementById("contenttype").value = "Symmetric Key";
      encrypt();
      }
      



    </script>



  </body>
</html>

