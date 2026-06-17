<?php
session_start();

// Verifica se os dados vieram do botão de atualizar (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['quantidade'])) {
    
    $id = (int)$_POST['id'];
    $nova_quantidade = (int)$_POST['quantidade'];

    // Se a quantidade for maior que zero, atualiza o carrinho
    if ($nova_quantidade > 0) {
        $_SESSION['carrinho'][$id] = $nova_quantidade;
    } else {
        // Se por algum motivo o utilizador enviar 0, removemos o item para evitar erros
        unset($_SESSION['carrinho'][$id]);
    }
}

// Redireciona de volta para o carrinho imediatamente
header("Location: carrinho.php");
exit();
?>