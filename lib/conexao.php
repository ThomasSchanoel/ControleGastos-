<?php

$host = "localhost";
$db = "controlegastos";
$user = "root";
$pass = "";



$mysqli = new mysqli($host, $user, $pass, $db);
if($mysqli->connect_errno){
    die("Falha na conexão com o banco de dados.");
}

$mysqli->set_charset('utf8mb4');
