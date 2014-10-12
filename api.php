<?php
/*
  interprets api requests and returns status info and other data
  basically a REST API
  but like without .htaccess
  GET commands get info
  and Post commands make changes
  or have sensitive info like passwords
*/
  require_once("includes/functions.php");//basic database operations
  $results = array();//array to be returned to client

  if(isset($_GET['action'])){//GETs info ie. list of Videos or list of users
    if($_GET['action']=="listValid"){
      $results = array_merge_recursive($results, listValid($db, $_GET));
    }else if($_GET['action']=="listInvalid"){
      $results = array_merge_recursive($results, listInvalid($db, $_GET));
    }else if($_GET['action']=="getIdea"){
      $results = array_merge_recursive($results, getIdea($db, $_GET));
    }
  }else if(isset($_POST['action'])){//handles POST requests ie. login or addVideo
    if($_POST['action']=="submitIdea"){//logs into an account
      $results = array_merge_recursive($results, submitIdea($db, $_POST));//send POST data to log in
    }else if($_POST['action']=="submitSolution"){//adds new video to playlist
      $results = array_merge_recursive($results, submitSolution($db, $_POST));
    }else if($_POST['action']=="markValid"){//marks video as watched
      $results = array_merge_recursive($results, markValid($db, $_POST));
    }else if($_POST['action']=="markInvalid"){//removes video
      $results = array_merge_recursive($results, markInvalid($db, $_POST));
    }else if($_POST['action']=="removeIdea"){//creates a party
      $results = array_merge_recursive($results, removeIdea($db, $_POST));
    }
  }

  //this block makes the status either success or failed and unsets errors if it doesn't exist
  $finalStatus = 'failed';
  if(array_key_exists("status", $results)){
    if(is_array($results['status'])){
      $finalStatus = 'success';
      foreach ($results['status'] as $status){
        if($status != "success"){
          $finalStatus = 'failed';
          break;
        }
      }
    }elseif($results['status']=='success'){
      $finalStatus = 'success';
    }
  }

  if(array_key_exists("errors", $results) && count($results['errors'])==0){
    unset($results['errors']);
  }else{
    $finalStatus = 'failed';
  }
  $results['status'] = $finalStatus;


  echo json_encode($results);//return info to client
?>