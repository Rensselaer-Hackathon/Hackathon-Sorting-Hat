<?php
  require_once("includes/functions.php");//basic database operations
  if (isset($_POST['action'])){
    if ($_POST['action']=='makeAdmin'){
      makeAdmin($db, $_POST);
      error_log('asdf');
    }elseif ($_POST['action']=='login'){
      login($db, $_POST);
    }elseif ($_POST['action']=='validate'){
      validate($db, $_POST);
    }elseif ($_POST['action']=='inValidate'){
      inValidate($db, $_POST);
    }
  }

  if (isset($_SESSION['admin']) && $_SESSION['admin']):
?>
  Welcome <?php echo $_SESSION['username'] ?>!! to make an admin, enter a username and a password for them here!</br>
  <form action='' method='post'>
    <input type="hidden" name="action" value="makeAdmin">
    username <input type="text" name="username"><br>
    Password: <input type="password" name="password">
    <input type="submit" value="Submit">
  </form>
  <form action='' method='post'>
    <table style="width:100%">
      <?php
        $invalid=listInvalid($db)['ideas'];
        for ($i=0; $i < count($invalid); $i++) {
          $idea=$invalid[$i];
          echo "<tr><td><input type='checkbox' name='ideaIds[]' value='{$idea->ideaId}'/></td><td>{$idea->title}</td><td>{$idea->username}</td><td>{$idea->description}</td></tr>";
        }
      ?>
    </table>
    <input type="submit" name="action" value="validate">
  </form>
  <form action='' method='post'>
    <table style="width:100%">
      <?php
        $valid=listValid($db)['ideas'];
        for ($i=0; $i < count($valid); $i++) {
          $idea=$valid[$i];
          echo "<tr><td><input type='checkbox' name='ideaIds[]' value='{$idea->ideaId}'/></td><td>{$idea->title}</td><td>{$idea->username}</td><td>{$idea->description}</td><td>{$idea->hatFrequency}</td><td>{$idea->siteFrequency}</td></tr>";
        }
      ?>
    </table>
    <input type="submit" name="action" value="inValidate">
  </form>
<?php
  else:
?>

<!DOCTYPE/>
<html lang="en">

  <head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="decription" content="hacks out of a hat!">

    <link href="static/css/bootstrap.css" rel="stylesheet">
    <link href="static/css/bootstrap-theme.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!-- [if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

  <body>

  <div class="container">
    <div class="row">
      <div class="col-md-12 center">
        <h1>Hello, World!</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
        <form action='' method='post' class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputUserName3" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
              <input name="username" type="text" class="form-control" id="inputUsername3" placeholder="Username">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" name="password" class="form-control" id="inputPassword3" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" name="action" value="login" class="btn btn-default">Sign in</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>

  </body>

  <script src="script/jquery-2.1.1.min.js"></script>
    <script src="script/bootstrap.min.js"></script>
  </body>

</html>

<?php
  endif;
?>