<?php

$host = 'mysql-marvel.alwaysdata.net';
$db = 'marvel_sql';
$user = 'marvel';
$pass = 'marvelkandji10';

try {
    $connecte = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

    $connecte->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie";
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}
?>