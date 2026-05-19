<?php

$host = "sql309.infinityfree.com";
$dbname = "if0_41962136_findafundi";
$username = "if0_41962136";
$password = "LLovxRyWp3Ie";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

?>