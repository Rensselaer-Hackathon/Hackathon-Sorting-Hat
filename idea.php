<?php
  require_once("includes/functions.php");//basic database operations
  $id=-1;
  if (isset($_GET['id'])){
    $id=$_GET['id'];
  }
  $idea = getIdea($db, $id)['idea'];
  if($id==-1){
    header("location:idea.php?id=".$idea->ideaId);
  }
?>
<a href="idea.php">get a random idea</a></br>
<a href="/">add a new idea</a></br>
Title: <?php echo $idea->title ?></br>
Submission Time: <?php echo $idea->submittedDate ?></br>
Submitter: <?php echo $idea->username ?></br>
Description: <?php echo $idea->description ?></br>

