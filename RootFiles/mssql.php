<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

try {

  //$dbh = new PDO($dsn, $dbuser, $password);
  
  //$dbh = new PDO ("dblib:host=mssql;dbname=INTGRATE","sa","Neat$9123");
  //$dbh = new \PDO("dblib:host=xxxxxx;dbname=xxxxx", 'xxx', 'xxxxx');
  $dbh = new \PDO("dblib:host=ec2-13-211-170-213.ap-southeast-2.compute.amazonaws.com:1433;dbname=INTGRATE","sa","Neat$9123"); 
  //$pdo = new PDO("dblib:host=mssql;dbname=$dbname", "$dbuser","$dbpwd");
  
  $stmt = $dbh->prepare("SELECT TABLE_NAME FROM INTGRATE.INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
  $stmt->execute();
  echo "<pre>";
  while ($row = $stmt->fetch()) {
    print_r($row);
  }
  unset($dbh); unset($stmt);
  echo "</pre>";
  } catch (PDOException $e) {

      echo '<br />Start<br />Connection failed: ' . $e->getMessage()."<br />End";

}
phpinfo();
?>