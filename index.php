
<a href="api.php?action=getIdea">get a new idea</a>
<form action="api.php" method="POST">
  <input type="hidden" name="action" value="submitIdea">
  title <input type="text" name="title"><br>
  username: <input type="text" name="username"><br>
  description: <input type="textarea" name="description"><br>
  <input type="submit" value="Submit">
</form>
<?php
require_once("includes/functions.php");//basic database operations
?>