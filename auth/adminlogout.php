<?php
require_once 'session.php';
require_once '../config/connection.php';
session_start();

session_destroy();
header("Location: ../auth/loginadmin.php");
?>