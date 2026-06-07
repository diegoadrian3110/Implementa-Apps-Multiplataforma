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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username  = trim($_POST['username']);
    $email  = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        die("Por favor, llena todos los campos.");
    }

    try {
        $sql = "INSERT INTO usuarios (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $pdo->prepare($sql);

        $resultado = $stmt->execute([
            ':username'  => $username,
            ':email' => $email,
            ':password' => $password
        ]);

        if ($resultado) {
            echo "¡Usuario registrado exitosamente de manera segura!";
        }

    } catch (\PDOException $e) {

        if ($e->getCode() == 23000) {
            echo "El nombre de usuario ya está en uso.";
        } else {
            echo "Error al registrar el usuario: " . $e->getMessage();
        }
    }
} else {

    header('Location: registro.php');
    exit;
}
?>