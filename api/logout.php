<?php
require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

session_destroy();
session_unset();

jsonResponse([
    'success' => true,
    'message' => 'Logout realizado com sucesso'
]);
?>
