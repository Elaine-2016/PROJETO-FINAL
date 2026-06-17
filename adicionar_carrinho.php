<?php
session_start();

// 1. Verifica se os dados vieram do formulário (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recebe os dados e converte para números inteiros por segurança
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;

    // 2. Validação básica: O ID do evento e a quantidade têm de ser válidos
    if ($event_id > 0 && $quantidade > 0) {
        
        // 3. Se o carrinho ainda não existir nesta sessão, criamos um array vazio
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        // 4. Lógica de adição
        if (isset($_SESSION['carrinho'][$event_id])) {
            // Se o evento já está no carrinho, somamos a nova quantidade à existente
            $_SESSION['carrinho'][$event_id] += $quantidade;
        } else {
            // Se for a primeira vez que adiciona este evento, criamos o registo
            $_SESSION['carrinho'][$event_id] = $quantidade;
        }

        // 5. Redireciona o utilizador diretamente para a página do Carrinho
        header("Location: carrinho.php");
        exit();
        
    } else {
        // Se os dados estiverem corrompidos, devolve à página de eventos
        header("Location: eventos.php?erro=dados_invalidos");
        exit();
    }
} else {
    // Se alguém tentar aceder a este ficheiro diretamente escrevendo no URL
    header("Location: index.php");
    exit();
}
?>