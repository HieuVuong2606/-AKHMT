<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tan_trung";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

?>