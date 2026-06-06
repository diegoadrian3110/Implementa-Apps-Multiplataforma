<?php
// Datos de configuración del servidor local (XAMPP / WAMP)
$host = "localhost";
$db   = "spotify";
$user = "root";
$pass = ""; // En XAMPP por defecto está vacío
$charset = "utf8mb4";

// Configuración de opciones de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa el reporte de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos en arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // Se crea la instancia de la conexión
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Conexión exitosa"; // Descomenta esto solo para probar que funcione
} catch (\PDOException $e) {
    // Si hay un error, detiene la app y lo muestra
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

require_once 'conexion.php';

// 2. Verificar que los datos llegaron por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recorrer y limpiar campos básicos
    $usuario  = $_POST['username'] ?? '';
    $correo   = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validar que ningún campo viaje vacío
    if (empty($usuario) || empty($correo) || empty($password)) {
        die("Todos los campos son obligatorios.");
    }

    try {
        /* 3. Preparar la consulta SQL de inserción.
           Usamos marcadores (:user, :email, :pass) para evitar inyección SQL.
        */
        $sql = "INSERT INTO usuarios (usuario, correo, contrasena) VALUES (:user, :email, :pass)";
        $stmt = $pdo->prepare($sql);
        
        /* 4. Ejecutar la consulta pasando los valores reales.
           Nota escolar: En un entorno real, la contraseña NUNCA se guarda en texto plano; 
           se encripta usando password_hash($password, PASSWORD_BCRYPT).
        */
        $resultado = $stmt->execute([
            'user'  => $usuario,
            'email' => $correo,
            'pass'  => $password 
        ]);

        // 5. Si la inserción fue exitosa, redirigir al Login
        if ($resultado) {
            // Reemplaza 'login.html' por el nombre real de tu archivo de login
            header("Location: index.hmtl?registro=exitoso");
            exit();
        }

    } catch (\PDOException $e) {
        // Manejo de errores (por ejemplo, si el usuario o correo ya existen y son campos UNIQUE)
        if ($e->getCode() == 23000) {
            echo "El nombre de usuario o el correo ya están registrados.";
        } else {
            echo "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
?>