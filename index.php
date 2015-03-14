<?php
  require_once("includes/functions.php");//basic database operations
  if (isset($_POST['action'])){
    if ($_POST['action']=='submitIdea'){
      submitIdea($db, $_POST);
    }
  }
  $categories=listCategories($db)['categories'];
?>
<a href="idea.php">get a new idea</a>
<form action="" method="POST">
  <input type="hidden" name="action" value="submitIdea">
  title <input type="text" name="title"><br>
  username: <input type="text" name="username"><br>
  description: <input type="textarea" name="description"><br>
  <table style="width:100%">
    <?php
      for ($i=0; $i < count($categories); $i++) {
        $category=$categories[$i];
        echo "<tr><td><input type='radio' name='catId' value='{$category->catId}'/></td><td>{$category->categoryName}</td></tr>";
      }
    ?>
  </table>
  <input type="submit" value="Submit">
</form>