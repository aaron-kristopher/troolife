<?php

$servername = "localhost";
$username = "troolifeuser";
$password = "troolife_secure_password";
$dbname = "troolife";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hashedPassword)
{
    return password_verify($password, $hashedPassword);
}
