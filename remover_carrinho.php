<?php
session_start();

// Verifica se recebeu um ID
if (isset($_GET['id'])) {
    $id_para_remover = (int)$_GET['id'];

    // Se o evento estiver no carrinho, remove-o do array da sessão usando unset()
    if (isset($_SESSION['carrinho'][$id_para_remover])) {
        unset($_SESSION['carrinho'][$id_para_remover]);
    }
}

// Redireciona de volta para o carrinho
header("Location: carrinho.php");
exit();
?>