<?php
$host    = 'localhost'; 
$db      = 'spotify';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES   => false,                 
];

try {

    $pdo = new PDO($dsn, $user, $pass, $options);
    header("Location: index.html");
    
} catch (\PDOException $e) {

    die("Hubo un error al conectar: ");
}
session_start();
require_once 'login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare('SELECT id, username, password FROM usuarios WHERE username = :username');
    $stmt->execute(['username' => $username,'password'=> $password]);
    $username = $stmt->fetch();

    if ($username && $password === $username['password']) {
        
        $_SESSION['usuario_id'] = $username['id'];
        $_SESSION['usuario_nombre'] = $username['username'];
        header("Location: index.html");
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
} else {
    header("Location: login.html");
    exit;
}
?>