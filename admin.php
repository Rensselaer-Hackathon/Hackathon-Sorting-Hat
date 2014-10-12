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
<form action='' method='post'>
  <input type="hidden" name="action" value="login">
  username <input type="text" name="username"><br>
  Password: <input type="password" name="password">
  <input type="submit" value="Submit">
</form>
<?php
  endif;
?>