<?php
session_start();
// Destruye todas las variables de sesión
$_SESSION = [];

// Destruye la sesión
session_destroy();

// Redirige al login
header('Location: login.php');
exit;
