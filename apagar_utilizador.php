<?php
session_start();
require 'conexao.php';

// Segurança máxima: Apenas administradores
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Verifica se o ID foi passado no URL
if (isset($_GET['id'])) {
    $id_para_apagar = $_GET['id'];

    // Segurança Extra: O sistema impede o Admin de cometer o erro de apagar a sua própria conta
    if ($id_para_apagar == $_SESSION['user_id']) {
        die("Erro de Segurança: Não podes apagar a tua própria conta enquanto estás ligado.");
    }

    try {
        // Graças ao ON DELETE CASCADE da base de dados, apagar o utilizador apagará também as suas compras!
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id_para_apagar]);

        // Redireciona de volta com a mensagem verde de sucesso
        header("Location: admin_dashboard.php?sucesso=1");
        exit();
        
    } catch (PDOException $e) {
        die("Ocorreu um erro ao tentar apagar o utilizador: " . $e->getMessage());
    }
} else {
    // Se tentarem aceder ao ficheiro diretamente sem um ID
    header("Location: admin_dashboard.php");
    exit();
}
?>