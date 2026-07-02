<?php
// Ligar a exibição de erros (vital para vermos o que se passa!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>A testar a ligação...</h1>";

$host = 'alojamentogi-mysql-8g3t8r';
$user = 'casadogi_user';
$pass = 'CasadoGi2026';
$db   = 'casadogi';

try {
    // Tentar ligar usando PDO (mais robusto para apanhar erros)
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2 style='color: green;'>Sucesso! A ligação à base de dados funcionou caralho!</h2>";
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>Falhou:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>