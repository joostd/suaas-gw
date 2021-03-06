<?php

/**
 * This is a simple example using PDO and a sqlite database for storing
 * registrations. It supports multiple registrations associated with each user.
 */

include_once('../../config.php');

require_once('finally.php');
require_once('../../vendor/yubico/u2flib-server/src/u2flib_server/U2F.php');

function clearAuth()
{
  $_SESSION['authReq'] = null;
}

$dbfile = '/var/tmp/u2f-pdo.sqlite';

$pdo = new PDO($config['userstore']['dsn'], $config['userstore']['username'], $config['userstore']['password']);
#$pdo = new PDO("sqlite:$dbfile");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

#$pdo->exec("create table if not exists users (id integer primary key, name varchar(255))");
#$pdo->exec("create table if not exists u2f_registrations (id integer primary key, user_id integer, keyHandle varchar(255), publicKey varchar(255), certificate text, counter integer)");

$scheme = isset($_SERVER['HTTPS']) ? "https://" : "http://";
$u2f = new u2flib_server\U2F($scheme . $_SERVER['HTTP_HOST']);

session_start();

function createAndGetUser($name) {
  global $pdo;
  $sel = $pdo->prepare("select * from user where name_id = ?");
  $sel->execute(array($name));
  $user = $sel->fetch();
  if(!$user) {
    $ins = $pdo->prepare("insert into user (name_id) values(?)");
    $ins->execute(array($name));
    $sel->execute(array($name));
    $user = $sel->fetch();
  }
  return $user;
}

function getRegs($user_id) {
  global $pdo;
  $sel = $pdo->prepare("select * from u2f_registrations where user_id = ?");
  $sel->execute(array($user_id));
  return $sel->fetchAll();
}

function addReg($user_id, $reg) {
  global $pdo;
  $ins = $pdo->prepare("insert into u2f_registrations (user_id, keyHandle, publicKey, certificate, counter) values (?, ?, ?, ?, ?)");
  $ins->execute(array($user_id, $reg->keyHandle, $reg->publicKey, $reg->certificate, $reg->counter));
}

function updateReg($reg) {
  global $pdo;
  $upd = $pdo->prepare("update u2f_registrations set counter = ? where id = ?");
  $upd->execute(array($reg->counter, $reg->id));
}

?>

<html>
<head>
<title>PHP U2F example</title>

<script src="chrome-extension://pfboblefjcgdjicmnffhdgionmgcdmne/u2f-api.js"></script>

<script>
<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
  if(!$_POST['username']) {
    echo "alert('no username provided!');";
  } else if(!isset($_POST['action']) && !isset($_POST['register2']) && !isset($_POST['authenticate2'])) {
    echo "alert('no action provided!');";
  } else {
    $user = createAndGetUser($_POST['username']);

    if(isset($_POST['action'])) {
      switch($_POST['action']):
/*
        case 'register':
          try {
            $data = $u2f->getRegisterData(getRegs($user->id));

            list($req,$sigs) = $data;
            $_SESSION['regReq'] = json_encode($req);
            echo "var req = " . json_encode($req) . ";";
            echo "var sigs = " . json_encode($sigs) . ";";
            echo "var username = '" . $user->name_id . "';";
?>
        setTimeout(function() {
            console.log("Register: ", req);
            u2f.register([req], sigs, function(data) {
                var form = document.getElementById('form');
                var reg = document.getElementById('register2');
                var user = document.getElementById('username');
                console.log("Register callback", data);
                if(data.errorCode) {
                    alert("registration failed with errror: " + data.errorCode);
                    return;
                }
                reg.value = JSON.stringify(data);
                user.value = username;
                form.submit();
            });
        }, 1000);
<?php
          } catch( Exception $e ) {
            echo "alert('error: " . $e->getMessage() . "');";
          }

          break;
*/

        case 'authenticate':
          try {
            $reqs = json_encode($u2f->getAuthenticateData(getRegs($user->id)));

            $_SESSION['authReq'] = $reqs;
            echo "var req = $reqs;";
            echo "var username = '" . $user->name_id . "';";
?>
        setTimeout(function() {
            console.log("sign: ", req);
            u2f.sign(req, function(data) {
                var form = document.getElementById('form');
                var auth = document.getElementById('authenticate2');
                var user = document.getElementById('username');
                console.log("Authenticate callback", data);
                auth.value=JSON.stringify(data);
                user.value = username;
                form.submit();
            });
        }, 1000);
<?php
          } catch( Exception $e ) {
            echo "alert('error: " . $e->getMessage() . "');";
          }

          break;

      endswitch;
	// end action POSTed
/*
    } else if($_POST['register2']) {
      $finally = new Finally("clearReg");
      try {
        $reg = $u2f->doRegister(json_decode($_SESSION['regReq']), json_decode($_POST['register2']));
        addReg($user->id, $reg);
      } catch( Exception $e ) {
        echo "alert('error: " . $e->getMessage() . "');";
//      } finally {
//        $_SESSION['regReq'] = null;
      }
      $finally->done();
*/

// ?username=Joost&authenticate2=%7B%22keyHandle%22%3A%223NVsu7KSzieI8FtdWhS9WkUWcYUUB1VhmD2cZH8gvw7Ix4FqrWwNUjP-o9Iw-K2p2HiZefyGC3sxJABPofQJeQ%22%2C%22clientData%22%3A%22eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZ2V0QXNzZXJ0aW9uIiwiY2hhbGxlbmdlIjoiSHFGMFpxY3ZDZ2tiV2s0bVhkWURjZC1EeWhMZ0hwbUxXakdaVVF1QUxQYyIsIm9yaWdpbiI6Imh0dHBzOi8vc3VhYXMtZ3cuc3VyZm5ldC5ubCIsImNpZF9wdWJrZXkiOiIifQ%22%2C%22signatureData%22%3A%22AQAAAAswRgIhAOpdyUl3ienJDAEa_NNNsAwDYTyOZTQHGEhGdOcbVVnaAiEAqN8QXgIxGSioWS3IhImF6LW1awJFRG42roZ2nD9Mc5k%22%7D

    } else if($_POST['authenticate2']) {
      $finally = new Finally("clearAuth");
      try {
        $reg = $u2f->doAuthenticate(json_decode($_SESSION['authReq']), getRegs($user->id), json_decode($_POST['authenticate2']));
        updateReg($reg);
        echo "alert('success: " . $reg->counter . "');";
      } catch( Exception $e ) {
        echo "alert('error: " . $e->getMessage() . "');";
//      } finally {
//        $_SESSION['authReq'] = null;
      }
      $finally->done();
    }
  }
}
?>
</script>
</head>
<body>

<!-- ?username=1&action=authenticate&authenticate2= -->

<form method="POST" id="form">
username: <input name="username" id="username"/><br/>
<!--
register: <input value="register" name="action" type="radio"/><br/>
-->
authenticate: <input value="authenticate" name="action" type="radio"/><br/>
<!--
<input type="hidden" name="register2" id="register2"/>
-->
<input type="hidden" name="authenticate2" id="authenticate2"/>
<button type="submit">Authenticate</button>
</form>

</body>
</html>
