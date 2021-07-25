<?php
$servername = "localhost";
$username = "root";
$password = "";
$vt_adi = "crout";

$baglanti = new mysqli($servername, $username, $password, $vt_adi);


if ($baglanti->connect_error) {
  die("Connection failed: " . $baglanti->connect_error);
}

?>