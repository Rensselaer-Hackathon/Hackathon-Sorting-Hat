<?php
  /*
  Joshua Makinen
  */
  require_once("constants.php");//get system-specific variables

  $db = connectDb();//connect to mysql

  if(session_id() == '') {
    session_start();
  }

  function sanitizeString($var){//cleans a string up so there are no crazy vulerabilities
    $var = strip_tags($var);
    $var = htmlentities($var);
    return stripslashes($var);
  }

  function connectDb(){
    require_once("constants.php");//get system-specific variables
    $db=0;
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;//string to connect to database
    try{
      $db = new PDO($dsn, DB_USER, DB_PASS);
    }catch(PDOException $e){//connection failed, set up a new database
      $db = setup();
    }
    return $db;
  }

  function setup(){//creates the database needed to run the application
    $db=0;
    try {
      $db = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);//connect to host
    } catch (PDOException $e) {//probably username or password wrong.  Sometimes problem with PDO class or mysql itself
      error_log('Connection failed: ' . $e->getMessage());
      exit;
    }
    try {
      $db->exec("CREATE DATABASE IF NOT EXISTS ".DB_NAME.";");//creates database in mysql for the app
    } catch (PDOException $e) {//could not make database
      error_log('Database '.DB_NAME.' was unsuccessful: ' . $e->getMessage());
      exit;
    }
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);//connects to new database
    // Temporarily not enforcing foreign key constraints, only noting with "References"
    executeSQL($db,
      'CREATE TABLE User(
        userId INT NOT NULL AUTO_INCREMENT,
        username VARCHAR(64) NOT NULL UNIQUE,
        password VARCHAR(128),
        joinedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        admin BIT(1) DEFAULT 0,

        INDEX(userId),

        PRIMARY KEY(userId, username)
      )
    ;');//stores each user as a row with relevent info

    executeSQL($db,
      'CREATE TABLE Idea(
        ideaId int NOT NULL AUTO_INCREMENT,
        title VARCHAR(64) UNIQUE,
        submitterId int REFERENCES User,
        description VARCHAR(500),
        submittedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        hatFrequency int DEFAULT 0,
        siteFrequency int DEFAULT 0,
        valid BIT(1) DEFAULT 0,
        INDEX(ideaId),

        PRIMARY KEY(ideaId, submitterId)
      )
    ;');

  }

  function executeSQL($db, $query){//runs a query with PDO's specific syntax
    try{
      $db->exec($query);
    }catch(PDOException $e){//something went wrong...
      error_log('Query failed: ' . $e->getMessage());
      exit;
    }
  }

  function getUserId($db, $username){//checks for row in user table corresponding to username provided
    $username = sanitizeString($username);//prevents sql injection attempts
    $stmt = $db->prepare(
      'SELECT
        *
      FROM
        User
      WHERE
        username=:username
    ;');//performs check
    $stmt->bindValue(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount()<=0){
      $stmt = $db->prepare(
        'INSERT INTO
          User(
            username
          )
        VALUES(
          :username
        )
      ;');
      $stmt->bindValue(':username', $username);
      $stmt->execute();
      return $db->lastInsertId();
    }else{
      $result = $stmt->fetch(PDO::FETCH_OBJ);
      return $result->userId;
    }
  }
/*

  function makeAdmin($db, $args){//creates account with an array of user information given
    $results = array("errors"=>array());
    if(is_array($args)&&array_key_exists("username", $args)&&array_key_exists("password", $args)){//valid array was given
      $username = sanitizeString($args['username']);
      $password = hash('sha512',PRE_SALT.sanitizeString($args['password']).POST_SALT);
      $userId = getUserId($db, $username);
      if(!$userId){//user already exists
        array_push($results['errors'], "user doesn't exist");
      } else {
        try {
          $stmt = $db->prepare(
            'UPDATE
              User
            SET
              password=:password,
              admin=1
            WHERE
              userId=:userId,
            )
          ;');//makes new row with given info
          $stmt->bindValue(':password', $password);
          $stmt->bindValue(':userId', $userId);
          $stmt->execute();
          $results['status'] = "success";
        } catch (PDOException $e) {//something went wrong...
          error_log("Error: " . $e->getMessage());
          array_push($results['errors'], "database error");
        }
      }
    }else{
      array_push($results['errors'], "missing username or password");
    }
    return $results;
  }
  function logIn($db, $args){//sets session data if the user information matches a user's row
    $results = array("errors"=>array());
    if(is_array($args)&&array_key_exists("username", $args)&&array_key_exists("password", $args)){//valid array was given
      $username = sanitizeString($args['username']);
      $password = hash('sha512',PRE_SALT.sanitizeString($args['password']).POST_SALT);
      try{
        $stmt = $db->prepare(
          'SELECT
            *
          FROM
            User
          WHERE
            username=:username and
            password=:password
        ;');//checks for matching row
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->execute();

        if($stmt->rowCount()==1){//if successfully logged in
          $result = $stmt->fetch(PDO::FETCH_OBJ);
          //startSeries($db, $result->userId);
          $results['status'] = 'success';
          $results['token'] = session_id();
          $_SESSION['username'] = $result->username;
          $_SESSION['userId'] = $result->userId;
          $_SESSION['logged'] = 1;
        }else{
          array_push($results['errors'], "bad username/password combination");
        }
      } catch (PDOException $e) {//something went wrong...
        error_log("Error: " . $e->getMessage());
        array_push($results['errors'], "database error");
      }

    }else{
      array_push($results['errors'], "missing username or password");
    }
    return $results;
  }
*/
  function submitIdea($db, $args){
    $results = array("errors"=>array());
    $required_keys = array("title", "description");
    if (is_array($args)){
      $valid=1;
      foreach ($required_keys as $key) {
        if (!array_key_exists($key, $args)){
          $valid=0;
          break;
        }
      }
      if($valid){
        $username="anonymous";
        if (array_key_exists("username", $args)&&$args['username']){
          $username= sanitizeString($args['username']);
        }
        $userId=getUserId($db, $username);
        error_log($userId);
        $title=sanitizeString($args['title']);
        $description=sanitizeString($args['description']);
        try{
          $stmt = $db->prepare(
            'INSERT INTO
              Idea(
                title,
                description,
                submitterId
              )
            VALUES(
              :title,
              :description,
              :userId
            )
          ;');
          $stmt->bindValue(':title', $title);
          $stmt->bindValue(':description', $description);
          $stmt->bindValue(':userId', $userId);
          $stmt->execute();

          $results['status'] = 'success';
        }catch (PDOException $e) {//something went wrong...
          error_log("Error: " . $e->getMessage());
          array_push($results['errors'], "database error");
        }
      }else{
        array_push($results['errors'], "must have both title and description");
      }
    }else{
      array_push($results['errors'], "invalid args");
    }
    return $results;
  }

  function getIdea($db, $args=-1){
    $results = array("errors"=>array());
    $ideaId=$args;
    $APIKey=0;
    $type="site";
    if (is_array($args)){
      $ideaId=-1;
      if(array_key_exists("ideaId", $args)){
        $ideaId = sanitizeString($args['ideaId']);
      }
      if(array_key_exists("APIKey", $args)){
        $APIKey = sanitizeString($args['APIKey'])==API_ARDUINO_KEY;
      }
    }
    if($APIKey){
      $type = "hat";
    }
    try{
      if($ideaId>=0){
        $stmt = $db->prepare(
        'SELECT
          ideaId,
          title,
          description,
          submittedDate,
          username
        FROM
          Idea i,
          User u
        WHERE
          i.ideaId=:ideaId and
          i.submitterId=u.userId
        ;');
        $stmt->bindValue(':ideaId', $ideaId);
        $stmt->execute();

        if ($stmt->rowCount()>0){
          $results['idea']=$stmt->fetch(PDO::FETCH_OBJ);
          $results['status']="success";
        }else{
          $ideaId=-1;
        }
      }
      if($ideaId<0){
        // /SELECT * FROM `table` WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `table` ) ORDER BY id LIMIT 1;
        $stmt = $db->prepare(
        "SELECT
          ideaId,
          title,
          description,
          submittedDate,
          username
        FROM
          Idea i,
          User u
        WHERE
          i.{$type}Frequency=(SELECT MIN({$type}Frequency) FROM Idea) and
          i.submitterId=u.userId
        ORDER BY
          RAND()
        LIMIT
          1
        ;");
        $stmt->execute();

        if ($stmt->rowCount()>0){
          $idea = $stmt->fetch(PDO::FETCH_OBJ);
          $asdf = $db->prepare(
          "UPDATE
            Idea
          SET
            {$type}Frequency={$type}Frequency+1
          WHERE
            ideaId=:ideaId
          ;");
          $asdf->bindValue(':ideaId', $idea->ideaId);

          $asdf->execute();
          $results['idea']=$idea;
          $results['status']="success";
        }else{
          $results['status']="failed";
        }
      }

    }catch (PDOException $e) {//something went wrong...
      error_log("Error: " . $e->getMessage());
      array_push($results['errors'], "database error");
    }
    return $results;
  }
?>