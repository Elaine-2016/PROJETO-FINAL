<?php
session_start();
require 'conexao.php';

// Segurança: Apenas administradores
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Verifica se a requisição é POST e se os dados existem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['compra_id']) && isset($_POST['status'])) {
    
    $compra_id = $_POST['compra_id'];
    $novo_status = $_POST['status'];

    // Validação extra para garantir que ninguém injeta status estranhos no HTML
    $status_permitidos = ['pendente', 'concluida', 'cancelada'];
    if (!in_array($novo_status, $status_permitidos)) {
        die("Erro: O estado selecionado não é válido.");
    }

    try {
        // Atualiza apenas a coluna status da compra específica
        $stmt = $pdo->prepare("UPDATE purchases SET status = :status WHERE id = :id");
        $stmt->execute([
            ':status' => $novo_status,
            ':id' => $compra_id
        ]);

        header("Location: admin_dashboard.php?sucesso=1");
        exit();

    } catch (PDOException $e) {
        die("Erro ao atualizar o estado da compra: " . $e->getMessage());
    }
} else {
    // Acesso indevido ao ficheiro
    header("Location: admin_dashboard.php");
    exit();
}
?>