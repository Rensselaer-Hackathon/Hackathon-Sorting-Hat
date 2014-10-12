<?php
  require_once("includes/functions.php");//basic database operations
  if (isset($_POST['username'])){
    if (isset($_SESSION['admin']) && $_SESSION['admin']){
      makeAdmin($db, $_POST);
      error_log('asdf');
    }else{
      login($db, $_POST);
    }
  }

  if (isset($_SESSION['admin']) && $_SESSION['admin']):
?>
  Welcome <?php echo $_SESSION['username'] ?>!! to make an admin, enter a username and a password for them here!</br>
  <form action='' method='post'>
    username <input type="text" name="username"><br>
    Password: <input type="password" name="password">
    <input type="submit" value="Submit">
  </form>
<?php
  else:
?>
<form action='' method='post'>
  username <input type="text" name="username"><br>
  Password: <input type="password" name="password">
  <input type="submit" value="Submit">
</form>
<?php
  endif;
?>