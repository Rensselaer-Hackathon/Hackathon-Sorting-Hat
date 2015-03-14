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
        catId int REFERENCES Category,
        valid BIT(1) DEFAULT 0,
        solverId int REFERENCES User,
        githubSolution VARCHAR(128),
        INDEX(ideaId),

        PRIMARY KEY(ideaId, submitterId)
      )
    ;');

    executeSQL($db,
      'CREATE TABLE Category(
        catId int NOT NULL AUTO_INCREMENT,
        categoryName VARCHAR(64) UNIQUE,
        INDEX(catId),

        PRIMARY KEY(catId, categoryName)
      )
    ;');

    makeAdmin($db, array('username'=>ADMIN, 'password'=>PASSWORD), 1);
    addCategory($db, "hardware");
    addCategory($db, "web development");
    addCategory($db, "mobile");
    addCategory($db, "design");
    addCategory($db, "desktop");
    addCategory($db, "machine learning");
  }

  function executeSQL($db, $query){//runs a query with PDO's specific syntax
    try{
      $db->exec($query);
    }catch(PDOException $e){//something went wrong...
      error_log('Query failed: ' . $e->getMessage());
      exit;
    }
  }

  function addCategory($db, $categoryName){
    executeSQL($db,
      "INSERT INTO
        Category(
          categoryName
        )
      VALUES(
        '$categoryName'
      )
    ;");
  }


  function listCategories($db, $args=0){
    $results = array("errors"=>array());
    try {
      $stmt = $db->prepare(
        'SELECT
          *
        FROM
          Category
      ;');
      $stmt->execute();
      $results['categories']=array();
      while ($row = $stmt->fetch(PDO::FETCH_OBJ)){//creates an array of the results to return
        array_push($results['categories'], $row);
      }
      $results['status'] = "success";
    } catch (PDOException $e) {//something went wrong...
      error_log("Error: " . $e->getMessage());
      array_push($results['errors'], "database error");
    }
    return $results;
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

  function makeAdmin($db, $args, $force=0){//creates account with an array of user information given
    $results = array("errors"=>array());
    if($force || (isset($_SESSION['admin']) && $_SESSION['admin'])){
      if(is_array($args)&&array_key_exists("username", $args)&&array_key_exists("password", $args)){//valid array was given
        $username = sanitizeString($args['username']);
        $password = hash('sha512',PRE_SALT.sanitizeString($args['password']).POST_SALT);
        $userId = getUserId($db, $username);
        try {
          $stmt = $db->prepare(
            'UPDATE
              User
            SET
              password=:password,
              admin=1
            WHERE
              userId=:userId
          ;');//makes new row with given info
          $stmt->bindValue(':password', $password);
          $stmt->bindValue(':userId', $userId);
          $stmt->execute();
          $results['status'] = "success";
        } catch (PDOException $e) {//something went wrong...
          error_log("Error: " . $e->getMessage());
          array_push($results['errors'], "database error");
        }
      }else{
        array_push($results['errors'], "missing username or password");
      }
    }else{
      array_push($results['errors'], "you do not have permissions to do this");
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
          $_SESSION['admin'] = $result->admin;
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
        $catId=-1;
        $username="anonymous";
        if (array_key_exists("username", $args)&&$args['username']){
          $username= sanitizeString($args['username']);
        }
        if (array_key_exists("catId", $args)&&$args['catId']){
          $catId= sanitizeString($args['catId']);
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
                submitterId,
                catId
              )
            VALUES(
              :title,
              :description,
              :userId,
              :catId
            )
          ;');
          $stmt->bindValue(':title', $title);
          $stmt->bindValue(':description', $description);
          $stmt->bindValue(':userId', $userId);
          $stmt->bindValue(':catId', $catId);
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
    $catQuery1='';
    $catQuery2='';
    $catId=0;
    if (is_array($args)){
      $ideaId=-1;
      if(array_key_exists("ideaId", $args)){
        $ideaId = sanitizeString($args['ideaId']);
      }
      if(array_key_exists("APIKey", $args)){
        $APIKey = sanitizeString($args['APIKey'])==API_ARDUINO_KEY;
      }
      if(array_key_exists("catName", $args) && $args['catName']){
        $stmt = $db->prepare(
          "SELECT
            catId
          FROM
            Category
          WHERE
            categoryName=:catName
        ;");
        $stmt->bindValue(':catName', $args['catName']);
        $stmt->execute();

        if ($stmt->rowCount()>0){
          $catId = $stmt->fetch(PDO::FETCH_OBJ)->catId;
        }
      }
      if(array_key_exists("catId", $args) && $args['catId']){
        $catId = sanitizeString($args['catId']);
      }
      if($catId){
        error_log($catId);
        $catQuery1 = " and catId='".$catId."' ";
        $catQuery2 = " and c.catId='".$catId."' ";
      }
    }
    if($APIKey){
      $type = "hat";
    }
    try{
      if($ideaId>=0){
        $stmt = $db->prepare(
        "SELECT
          ideaId,
          title,
          description,
          submittedDate,
          username,
          categoryName
        FROM
          Idea i,
          User u,
          Category c
        WHERE
          i.ideaId=:ideaId and
          i.valid=1 and
          i.submitterId=u.userId and
          i.catId = c.catId
        ;");
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
          username,
          categoryName
        FROM
          Idea i,
          User u,
          Category c
        WHERE
          i.{$type}Frequency=(SELECT MIN({$type}Frequency) FROM Idea WHERE valid=1 {$catQuery1}) and
          i.valid=1 and
          i.submitterId=u.userId and
          i.catId = c.catId
          {$catQuery2}
        ORDER BY
          RAND()
        LIMIT
          1
        ;");
        $stmt->execute();

        if ($stmt->rowCount()>0){
          $idea = $stmt->fetch(PDO::FETCH_OBJ);
          $stmt = $db->prepare(
          "UPDATE
            Idea
          SET
            {$type}Frequency={$type}Frequency+1
          WHERE
            ideaId=:ideaId
          ;");
          $stmt->bindValue(':ideaId', $idea->ideaId);

          $stmt->execute();
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

  function listInvalid($db, $args=0){
    $results = array("errors"=>array());
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      try {
        $stmt = $db->prepare(
          'SELECT
            i.ideaId,
            i.title,
            i.description,
            u.username
          FROM
            User u,
            Idea i
          WHERE
            u.userId=i.submitterId and
            i.valid=0
        ;');//makes new row with given info
        $stmt->execute();
        $results['ideas']=array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)){//creates an array of the results to return
          array_push($results['ideas'], $row);
        }
        $results['status'] = "success";
      } catch (PDOException $e) {//something went wrong...
        error_log("Error: " . $e->getMessage());
        array_push($results['errors'], "database error");
      }
    }else{
      array_push($results['errors'], "you do not have permissions to do this");
    }
    return $results;
  }

  function listValid($db, $args=0){
    $results = array("errors"=>array());
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      try {
        $stmt = $db->prepare(
          'SELECT
            i.ideaId,
            i.title,
            i.description,
            i.hatFrequency,
            i.siteFrequency,
            u.username
          FROM
            User u,
            Idea i
          WHERE
            u.userId=i.submitterId and
            i.valid=1
        ;');
        $stmt->execute();
        $results['ideas']=array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)){//creates an array of the results to return
          array_push($results['ideas'], $row);
        }
        $results['status'] = "success";
      } catch (PDOException $e) {//something went wrong...
        error_log("Error: " . $e->getMessage());
        array_push($results['errors'], "database error");
      }
    }else{
      array_push($results['errors'], "you do not have permissions to do this");
    }
    return $results;
  }

  function validate($db, $args){
    $results = array("errors"=>array());
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      if(array_key_exists("ideaIds", $args)){
        try {
          $inQuery = implode(',', array_fill(0, count($args['ideaIds']), '?'));
          $stmt = $db->prepare(
            "UPDATE
              Idea
            SET
              valid=1
            WHERE
              ideaId IN({$inQuery})
          ;");
          foreach ($args['ideaIds'] as $k => $id)$stmt->bindValue(($k+1), $id);
          $stmt->execute();
          $results['status'] = "success";
        } catch (PDOException $e) {//something went wrong...
          error_log("Error: " . $e->getMessage());
          array_push($results['errors'], "database error");
        }
      }else{
        array_push($results['errors'], "needs ideaIds");
      }
    }else{
      array_push($results['errors'], "you do not have permissions to do this");
    }
    return $results;
  }

  function inValidate($db, $args){
    $results = array("errors"=>array());
    if(isset($_SESSION['admin']) && $_SESSION['admin']){
      if(array_key_exists("ideaIds", $args)){
        try {
          $inQuery = implode(',', array_fill(0, count($args['ideaIds']), '?'));
          error_log(json_encode($args['ideaIds']));
          $stmt = $db->prepare(
            "UPDATE
              Idea
            SET
              valid=0
            WHERE
              ideaId IN({$inQuery})
          ;");
          foreach ($args['ideaIds'] as $k => $id)$stmt->bindValue(($k+1), $id);
          $stmt->execute();
          $results['status'] = "success";
        } catch (PDOException $e) {//something went wrong...
          error_log("Error: " . $e->getMessage());
          array_push($results['errors'], "database error");
        }
      }else{
        array_push($results['errors'], "needs ideaIds");
      }
    }else{
      array_push($results['errors'], "you do not have permissions to do this");
    }
    return $results;
  }
?>