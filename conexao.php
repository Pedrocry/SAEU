<?php
$host = 'localhost'; // Seu host MySQL
$dbname = 'sistema_arquivos'; // Nome do seu banco de dados
$username = 'root'; // Seu nome de usuário do MySQL
$password = ''; // Sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>