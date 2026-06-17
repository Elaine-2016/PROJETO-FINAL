<?php
session_start();
require 'conexao.php';

// Segurança: Apenas administradores
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Verifica se recebeu o ID do evento
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // A nossa chave estrangeira ON DELETE CASCADE vai garantir que as compras 
        // e carrinhos associados a este evento também sejam limpos da base de dados!
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        header("Location: admin_dashboard.php?sucesso=apagado");
        exit();
    } catch (PDOException $e) {
        die("Erro ao apagar o evento: " . $e->getMessage());
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>