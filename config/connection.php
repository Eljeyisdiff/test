<?php
require_once 'constants.php';

$conn = new mysqli(db_host, db_user, db_pass, db_name, db_port);

if($conn -> connect_error){
  die("Connection Failed: ".$conn->connect_error);
}

?>