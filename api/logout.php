<?php
require_once '../config/config.php';

header('Content-Type: application/json');

session_destroy();
session_unset();

jsonResponse([
    'success' => true,
    'message' => 'Logout realizado com sucesso'
]);
?>
