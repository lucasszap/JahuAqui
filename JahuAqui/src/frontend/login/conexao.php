<?php
  $host = 'localhost';
  $user = 'root';
  $pass = 'etecjau';
  $db = 'DB_LOGINS';

  $conn = new mysqli($host, $user, $pass, $db);

  if ($conn->connect_error) {
    die('falha na conexão: '. $conn->connect_error);
  }
?>