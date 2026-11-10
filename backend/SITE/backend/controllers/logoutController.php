<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpa todas as variáveis da sessão
$_SESSION = array();

// Destrói a sessão no servidor
session_destroy();

// Redireciona o usuário de volta para a Home
header("Location: ../../frontend/home.php");
exit;